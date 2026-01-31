<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DefaultPanel\Resources\Api\Customer\RateResource;
class ProviderResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'image' => $this->getFirstMediaUrl(),
            'images'=>$this->getMedia('images')->map(fn($image) => $image->getFullUrl())->toArray(),

            'rate' => $this->getAvgRate(),
            $this->mergeWhen(request()->filled('latitude') && request()->filled('longitude'), [
                'distance'=>round($this->distance/1000,2),
            ]),
            'country'=>CountryResource::make($this->city?->state?->country),
            'state'=>StateResource::make($this->city?->state),
            'city'=>CityResource::make($this->city),
            'location' => [
                'lat'=>$this->location->getCoordinates()[1],
                'lng'=>$this->location->getCoordinates()[0],
            ],
            'distance' => 20,
            'working_days' =>WorkingTimesResource::collection(collect( $this->meta_data['days_list']??[])->where('status',1)),
            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,
            'complete_order_text' => $this->user?->options?->texts[app()->getLocale()]['text_when_order_completed']??'',
            'reservation_fees_include_taxes' => Money::parse(floatval($this->reservation_fees_include_taxes))->format(),
            "share_link" => route('site.share_provider', str_replace(" ", "&", $this->getTranslation('name', 'en') ?? $this->name)),
            'latest_rates' => $this->getGroupedRates(),
        ];
    }

    /**
     * Average rating including ALL reservation-based + manual ratings (excluding replies).
     */
    private function getAvgRate(): float
    {
        $avg = \App\CatalogModule\Models\Reservation\Rate::query()
            ->where(function($query) {
                // Reservation-based ratings
                $query->whereHas('reservation', function($q) {
                    $q->where('reservable_type', \App\UsersModule\Models\Provider::class)
                        ->where('reservable_id', $this->id);
                })
                // OR manual ratings with this provider
                ->orWhere(function($q) {
                    $q->where('provider_id', $this->user_id)
                        ->where('source', 'manual');
                });
            })
            ->whereNull('parent_id') // exclude replies
            ->where('is_approved', true)
            ->whereNotNull('rate') // ensure rate is not null
            ->avg('rate');

        return (float) ($avg ?? 0);
    }

    /**
     * Get grouped ratings (service and place together)
     */
    private function getGroupedRates(): array
    {
        // Get all ratings (not grouped yet)
        $allRatings = \App\CatalogModule\Models\Reservation\Rate::where(function($query) {
            // Reservation-based ratings
            $query->whereHas('reservation', function($q) {
                $q->where('reservable_type', \App\UsersModule\Models\Provider::class)
                  ->where('reservable_id', $this->id);
            })
            // OR manual ratings with this provider
            ->orWhere(function($q) {
                $q->where('provider_id', $this->user_id)
                  ->where('source', 'manual');
            });
        })
        ->whereNull('parent_id')
        ->where('is_approved', true)
        ->with(['user', 'reservation.customer', 'replies.user'])
        ->latest()
        ->get();

        // Group by pair_id or reservation_id
        $grouped = $allRatings->groupBy(function($rate) {
            return $rate->pair_id ?? $rate->reservation_id ?? 'single_' . $rate->id;
        });

        // Build grouped response
        $result = $grouped->take(5)->map(function($group) {
            $serviceRating = $group->firstWhere('type', 'service');
            $placeRating = $group->firstWhere('type', 'place');
            
            // Use the first rating for common data (user, date, etc.)
            $baseRating = $serviceRating ?: $placeRating ?: $group->first();
            
            // Collect all replies from both service and place ratings
            $allReplies = collect();
            if ($serviceRating && $serviceRating->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($serviceRating->replies);
            }
            if ($placeRating && $placeRating->relationLoaded('replies')) {
                $allReplies = $allReplies->merge($placeRating->replies);
            }
            
            // Remove duplicates and sort by created_at
            $replies = $allReplies->unique('id')
                ->sortBy('created_at')
                ->map(function($reply) {
                    return [
                        'comment' => $reply->comment,
                        'created_at' => $reply->created_at?->diffForHumans(),
                        'user' => $reply->user?->name ?? __('panel.provider'),
                    ];
                })
                ->values()
                ->toArray();
            
            return [
                'name' => $baseRating->user?->name ?? $baseRating->reservation?->customer?->name ?? __('panel.anonymous'),
                'created_at' => $baseRating->created_at?->diffForHumans(),
                'service' => $serviceRating ? [
                    'rate' => (int) $serviceRating->rate,
                    'comment' => $serviceRating->comment,
                ] : null,
                'place' => $placeRating ? [
                    'rate' => (int) $placeRating->rate,
                    'comment' => $placeRating->comment,
                ] : null,
                'replies' => $replies,
            ];
        })->values()->toArray();

        return $result;
    }
}

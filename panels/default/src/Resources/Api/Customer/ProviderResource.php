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
            'image' => $this->getDisplayImageUrl(),
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
            'share_link' => route('site.provider.show', $this->id),
            'latest_rates' => $this->getGroupedRates(),
            'available_coupons' => $this->getActiveCoupons(),
            'portfolio' => $this->getPortfolio(),
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

    /**
     * Get active coupons for this provider
     */
    private function getActiveCoupons(): array
    {
        $couponIds = \App\ContentModule\Models\Coupon::listingIdsForProvider($this->id);

        if ($couponIds->isEmpty()) {
            return [];
        }

        $coupons = \App\ContentModule\Models\Coupon::query()
            ->whereIn('id', $couponIds)
            ->where('status', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return $coupons->map(function($coupon) {
            // Handle dates - they might be strings or Carbon instances
            // Carbon::parse handles both cases
            $startDate = $coupon->start_date ? \Carbon\Carbon::parse($coupon->start_date)->toDateString() : null;
            $endDate = $coupon->end_date ? \Carbon\Carbon::parse($coupon->end_date)->toDateString() : null;
            
            return [
                'id' => $coupon->id,
                'name' => $coupon->name,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type->value,
                'discount_value' => $coupon->discount_value,
                'formatted_value' => $coupon->formattedValue(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'usages' => $coupon->usages,
                'usage_per_user' => $coupon->usage_per_user,
                'min_order_value' => $coupon->meta_data['min_order_value'] ?? 0,
            ];
        })->toArray();
    }
    /**
     * Get portfolio albums with items (mirrors provider-show and provider-gallery structure)
     */
    private function getPortfolio(): array
    {
        $locale = app()->getLocale();
        $albums = collect($this->meta_data['portfolio_albums'] ?? []);
        $allMedia = $this->getMedia('portfolio');

        if ($albums->isEmpty() && $allMedia->isNotEmpty()) {
            return [[
                'title' => __('site.heading.gallery'),
                'items' => $allMedia->map(fn ($m) => $this->formatPortfolioItem($m))->values()->all(),
            ]];
        }

        return $albums->map(function ($album) use ($allMedia, $locale) {
            $albumId = $album['album_id'] ?? null;
            $title = is_array($album['title'] ?? null)
                ? ($album['title'][$locale] ?? $album['title']['ar'] ?? $album['title']['en'] ?? '')
                : ($album['title'] ?? '');
            $items = $allMedia
                ->filter(fn ($m) => ($m->getCustomProperty('album_id') ?? '') === $albumId)
                ->map(fn ($m) => $this->formatPortfolioItem($m))
                ->values()
                ->all();
            return ['title' => $title, 'items' => $items];
        })->filter(fn ($a) => ! empty($a['items']))->values()->all();
    }

    private function formatPortfolioItem($media): array
    {
        $mimeType = $media->mime_type ?? '';
        $type = str_starts_with($mimeType, 'video/') ? 'video'
            : (str_starts_with($mimeType, 'audio/') ? 'audio' : 'image');

        return [
            'id' => $media->id,
            'url' => $media->getFullUrl(),
            'type' => $type,
            'title' => $media->getCustomProperty('title') ?? '',
        ];
    }
}

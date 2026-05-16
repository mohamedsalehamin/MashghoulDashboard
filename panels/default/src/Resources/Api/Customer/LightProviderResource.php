<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class LightProviderResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'image' => $this->getDisplayImageUrl(),
            'name' => $this->name,
            'city' => $this->city->name,
            'rate' => $this->getAvgRate(),
            $this->mergeWhen(request()->filled('latitude') && request()->filled('longitude'), [
                'distance' => round($this->distance / 1000, 2),
            ]),

            'location' => [
                'lat' => $this->location->getCoordinates()[1],
                'lng' => $this->location->getCoordinates()[0],
            ],

            'working_days' => WorkingTimesResource::collection(collect($this->meta_data['days_list'] ?? [])->where('status', 1)),
            'reservation_fees_include_taxes' => \Cknow\Money\Money::parse(floatval($this->reservation_fees_include_taxes))->format(),
            'slug' => $this->slug,
            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,

        ];
    }

    /**
     * Average rating including ALL reservation-based + manual ratings (excluding replies).
     */
    private function getAvgRate(): float
    {
        $avg = \App\CatalogModule\Models\Reservation\Rate::query()
            ->where(function ($query) {
                // Reservation-based ratings
                $query->whereHas('reservation', function ($q) {
                    $q->where('reservable_type', \App\UsersModule\Models\Provider::class)
                        ->where('reservable_id', $this->id);
                })
                // OR manual ratings with this provider
                    ->orWhere(function ($q) {
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
}

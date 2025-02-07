<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'image' => $this->getFirstMediaUrl(),
            'images'=>$this->getMedia('images')->map(fn($image) => $image->getFullUrl())->toArray(),

            'rate' => (float)$this->rate()->avg('rate')??0,
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
            'reservation_fees_include_taxes' => \Cknow\Money\Money::parse($this->reservation_fees_include_taxes)->format(),
        ];
    }
}

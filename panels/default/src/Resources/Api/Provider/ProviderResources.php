<?php

namespace App\DefaultPanel\Resources\Api\Provider;


use Illuminate\Http\Request;
use App\DefaultPanel\Resources\Api\Customer\CityResource;
use App\DefaultPanel\Resources\Api\Customer\CountryResource;
use App\DefaultPanel\Resources\Api\Customer\StateResource;
use App\DefaultPanel\Resources\Api\Customer\WorkingTimesResource;
use Illuminate\Http\Resources\Json\JsonResource;


class ProviderResources extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'image' => $this->getFirstMediaUrl(),
            'images' => $this->getMedia("images")->map(fn($item) => $item->getUrl()),
            'commercial_register' => $this->getFirstMediaUrl("commercial_register"),
            'category' => $this->category?->name,
            'country' => CountryResource::make($this?->city?->state?->country),
            'state' => StateResource::make($this->city?->state),
            'city' => CityResource::make($this->city),
            'location' => [
                'latitude' => $this->location?->getCoordinates()[1],
                'longitude' => $this->location?->getCoordinates()[0],
            ],
            'working_days' => WorkingTimesResource::collection(collect($this->meta_data['days_list']??[])->where('status', 1)),
            'complete_order_text' => $this->user?->options?->texts[app()->getLocale()]??'',
            "share_link" => route('site.share_provider', str_replace(" ", "&", $this->getTranslation('name', 'en') ?? $this->name)),
        ];
    }
}

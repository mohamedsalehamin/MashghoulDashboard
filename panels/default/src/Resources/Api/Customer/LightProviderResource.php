<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class LightProviderResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'image' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            'city' => $this->city->name,
            'rate' => $this->avgRate(),
            $this->mergeWhen(request()->filled('latitude') && request()->filled('longitude'), [
                'distance'=>round($this->distance/1000,2),
            ]),

            'location' => [
                'lat'=>$this->location->getCoordinates()[1],
                'lng'=>$this->location->getCoordinates()[0],
            ],

            'working_days' =>WorkingTimesResource::collection(collect( $this->meta_data['days_list']??[])->where('status',1)),

            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,

        ];
    }
}

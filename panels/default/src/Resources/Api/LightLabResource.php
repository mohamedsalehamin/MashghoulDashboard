<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LightLabResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'working_days'=>$this->getWorkingDays(),
            'city' => CityResource::make($this->city),
            'image' => $this->image,

            'rate' =>round( $this->rate()->avg('rate') ?? 0,2),
            'favorite' => $request->user('sanctum')?->isFavorited($this)??false,
            'available' => $this->isAvailableToday(),
            'share_url'=>route('labs.show',$this->id),
        ];
    }


}

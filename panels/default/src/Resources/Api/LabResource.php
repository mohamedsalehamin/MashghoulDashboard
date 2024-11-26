<?php

namespace App\DefaultPanel\Resources\Api;

use App\UsersModule\Models\Lab;
use Illuminate\Http\Resources\Json\JsonResource;

class LabResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'working_days'=>$this->getWorkingDays(),
            'lab_info' => [

                'city' => CityResource::make($this->city),
                'location' => [
                    'lat' => $this->location->getCoordinates()[1],
                    'lng' => $this->location->getCoordinates()[0],
                ],
                'images' => $this->getMedia()->map(function ($media) {
                    return $media->getFullUrl();
                }),
            ],
            'share_url'=>route('labs.show',$this->id),

            'image' => $this->image,
            'rate' => round($this->rate()->avg('rate') ?? 0, 2),
            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,
            'available' => $this->isAvailableToday(),
            'services' => LabServiceResource::collection($this->services()->enabled()->get()),
            'similar' => LightLabResource::collection(Lab::where('city_id', $this->city_id)->where('id', '!=', $this->id)->limit(5)->get()),
        ];
    }


}

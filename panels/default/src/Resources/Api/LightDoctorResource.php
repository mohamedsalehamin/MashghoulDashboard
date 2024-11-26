<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class LightDoctorResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar'=>$this->image,
            'bio' => $this->bio,
            'nationality' => $this->nationality->name,
            'city' => CityResource::make($this->clinic?->city),
            'location' => [
                'lat' => $this->clinic?->location?->getCoordinates()[1] ?? 0,
                'lng' => $this->clinic?->location?->getCoordinates()[0] ?? 0,
            ],
            'share_url'=>route('doctors.show',$this->id),
            'certifications'=>DoctorCertificationResource::collection($this->user->certificates),
            'working_days'=>$this->getWorkingDays(),
            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,
            'available' => $this->isAvailableToday(),
            'times_type' => $this->times_type?->getLabel()??'',
            'times_type_enum' => $this->times_type,
            'rate' =>round( $this->rate()->avg('rate') ?? 0,2),
            'title'=>$this->title?->name??null,
            'speciality' => SpecializationResource::make($this->specialization),
            'specializations'=>SpecializationResource::collection($this->specializations),


        ];
    }


}

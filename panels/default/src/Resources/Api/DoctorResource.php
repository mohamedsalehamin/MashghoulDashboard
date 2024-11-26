<?php

namespace App\DefaultPanel\Resources\Api;

use App\UsersModule\Models\Doctor;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar'=>$this->image,
            'bio' => $this->bio,
            'rate' => round($this->rate()->avg('rate') ?? 0, 2),
            'speciality' => SpecializationResource::make($this->specialization),
            'specializations'=>SpecializationResource::collection($this->specializations),
            'working_days'=>$this->getWorkingDays(),
            'title'=>$this->title?->name,
            'info' => [
                'experience_years' => $this->experience_years,
                'nationality' => $this->nationality->name,
                'languages' => $this->language->name,
            ],
            'clinic_info' => [
                'city' => CityResource::make($this->clinic->city),
                'location' => [
                    'lat' => $this->clinic->location->getCoordinates()[1] ?? 0,
                    'lng' => $this->clinic->location->getCoordinates()[0] ?? 0,
                ],
                'times_type' => $this->times_type->getLabel(),
                'times_type_enum' => $this->times_type,
                'images' => $this->user->getMedia('clinic')->map(fn($image) => $image->getFullUrl())->toArray(),
            ],
            'certifications'=>DoctorCertificationResource::collection($this->user->certificates),
            'share_url'=>route('doctors.show',$this->id),
            'favorite' => $request->user('sanctum')?->isFavorited($this) ?? false,
            'available' => $this->isAvailableToday(),
            'services' => DoctorServiceResource::collection($this->services()->enabled()->get()),
            'similar' => LightDoctorResource::collection(Doctor::where('specialty_id', $this->specialty_id)->where('id', '!=', $this->id)->limit(5)->get()),
        ];
    }


}

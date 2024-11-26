<?php

namespace App\DefaultPanel\Resources\Api\Doctors;

use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LightReservationResource extends JsonResource {

    public function toArray($request) {

        return array(
            'id' => $this->id,
            'doctor' => LightDoctorResource::make($this->reservable),
            'date' => $this->date->format('Y-m-d'),
            'period' => $this->period,
            'status' => $this->status->getLabel(),
            'status_code' => $this->status,
            'price' => $this->price->format(),

        );
    }


}

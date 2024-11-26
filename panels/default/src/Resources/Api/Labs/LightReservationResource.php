<?php

namespace App\DefaultPanel\Resources\Api\Labs;

use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use App\DefaultPanel\Resources\Api\LightDoctorResource;
use App\DefaultPanel\Resources\Api\LightLabResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LightReservationResource extends JsonResource {

    public function toArray($request) {
        return array(
            'id' => $this->id,
            'lab' => LightLabResource::make($this->reservable),
            'date' => $this->date->format('Y-m-d'),
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status->getLabel(),
            'status_code' => $this->status,
            'price' => $this->price->format(),

        );
    }


}

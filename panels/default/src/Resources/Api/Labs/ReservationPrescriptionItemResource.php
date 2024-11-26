<?php

namespace App\DefaultPanel\Resources\Api\Labs;


use App\DefaultPanel\Resources\Api\LightLabResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationPrescriptionItemResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'notes' => $this->notes,

        ];
    }


}

<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientHealthDataResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */

    public function toArray($request) {
        return [
            "length" => $this->length,
            "blood_type" => $this->blood_type,
            "blood_sugar_rate" => $this->blood_sugar_rate,
            "blood_pressure_rate" => $this->blood_pressure_rate
        ];
    }
}

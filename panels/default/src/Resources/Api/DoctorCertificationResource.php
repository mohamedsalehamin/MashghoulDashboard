<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorCertificationResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'university_name' => $this->university_name,

        ];
    }


}

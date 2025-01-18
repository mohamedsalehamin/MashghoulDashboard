<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class   WorkingTimeSlotsResource extends JsonResource {

    public function toArray($request) {

        return WorkingTimesResource::collection($this->resource);
    }
}

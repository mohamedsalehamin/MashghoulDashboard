<?php

namespace App\DefaultPanel\Resources\Api\Customer;
use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationConditionsResource extends JsonResource {

    public function toArray($request): array {

        return [
            "name" => $this->getName(),

        ];
    }


}

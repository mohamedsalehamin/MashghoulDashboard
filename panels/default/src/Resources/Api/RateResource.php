<?php

namespace App\DefaultPanel\Resources\Api;

use App\Brands\Resources\Api\BrandResources;
use App\Fleet\Http\Resources\DriverResource;
use App\Jobs\Http\Resources\Api\RateResources;
use App\Orders\Resources\Api\AddressOrderResource;
use App\Orders\Resources\Api\UserOrderResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource {

    public function toArray($request) {
        return [
            "score" => collect($this->rate),
            "comment" => $this->comment,
        ];
    }
}

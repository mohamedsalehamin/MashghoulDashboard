<?php

namespace App\DefaultPanel\Resources\Api;

use App\DefaultPanel\Resources\Api\Cart\CartServiceResource;
use Carbon\Carbon;
use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationProductResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this['id'],
            'title' => $this['title'][app()->getLocale()] ?? '',
            'price'=>Money::parse($this['price']['amount'])->format()
        ];
    }


}

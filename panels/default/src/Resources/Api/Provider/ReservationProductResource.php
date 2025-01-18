<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationProductResource extends JsonResource {

    public function toArray($request): array {
        return [
            'id' => $this['id'],
            'title' => $this['title'][app()->getLocale()] ?? '',
            'price'=>Money::parse($this['price']['amount'])->format(),
            'total_price'=>Money::parse(data_get($this,'price.amount',0)*$this['quantity'])->format(),
            'quantity'=>$this['quantity'],
            'image' => $this['image'],
        ];
    }


}

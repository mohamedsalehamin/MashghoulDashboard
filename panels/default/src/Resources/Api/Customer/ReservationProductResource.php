<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationProductResource extends JsonResource {

    public function toArray($request): array {
        $qty=data_get($this,'quantity',1);
        return [
            'id' => $this['id'],
            'title' => $this['title'][app()->getLocale()] ?? '',
            'price'=>Money::parse($this['price']['amount'])->format(),
            'total_price'=>Money::parse(data_get($this,'price.amount',0)*$qty)->format(),
            'quantity'=>$qty,
            'image' => data_get($this,'image',''),
        ];
    }


}

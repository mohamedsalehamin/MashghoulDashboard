<?php

namespace App\DefaultPanel\Resources\Api\Customer\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Money\Money;


class CartProductResource extends JsonResource {

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->title[app()->getLocale()]??'',
            'image' => $this->getFirstMediaUrl(),
            'price' => $this->price->format(),
            'quantity' => $this->quantity,
            'total_price' => \Cknow\Money\Money::parse($this->price->formatByDecimal() * $this->quantity)->format(),

        ];
    }

}

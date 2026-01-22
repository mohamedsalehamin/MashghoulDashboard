<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationProductResource extends JsonResource
{

    public function toArray($request): array
    {
        $qty = data_get($this, 'quantity', 1);
        $regularPrice = $this['price']['amount'];
        $salePrice = $this['sale_price']['amount'];

        $discountPercentage = false;
        if ($salePrice > 0 && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $salePrice) / $regularPrice * 100);
        }
        return [
            'id' => $this['id'],
            'title' => $this['title'][app()->getLocale()] ?? '',
            'price' => Money::parse($this['price']['amount'])->format(),
            'sale_price' => $this['sale_price']['amount'] > 0 ? Money::parse($this['sale_price']['amount'])->format() : false,
            'discount_percentage' => $discountPercentage,
            'total_price' => Money::parse((data_get($this, 'sale_price.amount') > 0 ? data_get($this, 'sale_price.amount') : data_get($this, 'price.amount', 0)) * $qty)->format(),
            'quantity' => $qty,
            'image' => data_get($this, 'image', ''),
        ];
    }
}
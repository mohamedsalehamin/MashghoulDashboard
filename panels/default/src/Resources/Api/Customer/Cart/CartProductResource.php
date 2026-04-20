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
         $regularPrice = $this->price->getAmount();
        $salePrice = $this->sale_price->getAmount();
        
        $discountPercentage = false;
        if (!$this->sale_price->isZero() && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $salePrice) / $regularPrice * 100);
        }
        return [
            'id' => $this->id,
            'name' => $this->title[app()->getLocale()]??'',
            'image' => $this->getProductImageUrl(),
            'price' => $this->price->format(),
            'sale_price' => !$this->sale_price->isZero() ? $this->sale_price->format() : false,
            'discount_percentage' => $discountPercentage, 
            'quantity' => $this->quantity,
            'total_price' => \Cknow\Money\Money::parse((!$this->sale_price->isZero() ? $this->sale_price->formatByDecimal() : $this->price->formatByDecimal()) * $this->quantity)->format(),

        ];
    }

}

<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource {

    public function toArray($request) {
        $regularPrice = $this->price->getAmount();
        $salePrice = $this->sale_price->getAmount();

        $discountPercentage = false;
        if (!$this->sale_price->isZero() && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $salePrice) / $regularPrice * 100);
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'service_group_id' => $this->pivot?->service_group_id ?? null,
            'price' => $this->price->format(),
            'price_include_taxes' => $this->price_include_taxes->format(),
            'sale_price' => !$this->sale_price->isZero() ? $this->sale_price->format() : false,
            'sale_price_include_taxes' => $this->sale_price_include_taxes->format(),
            'discount_percentage' => $discountPercentage,
            'duration' => $this->duration,
            'image' => $this->getServiceImageUrl(),
            'products' => ProductsResource::collection($this->products),


        ];
    }
}

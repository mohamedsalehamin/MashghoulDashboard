<?php

namespace App\DefaultPanel\Resources\Api\Customer;
use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationServiceResource extends JsonResource {

    public function toArray($request): array {
        $products=Arr::get($this->attributes, 'products', []);
        $regularPrice =Money::parse($this->associatedModel->price)->getAmount();
        $salePrice = Money::parse($this->associatedModel->sale_price)->getAmount();

        $discountPercentage = false;
        if ($salePrice > 0 && $regularPrice > 0) {
            $discountPercentage = round(($regularPrice - $salePrice) / $regularPrice * 100);
        }
        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->getServiceImageUrl(),
            "name" => $this->associatedModel?->title,
            "quantity" => $this->quantity,
            'products' => ReservationProductResource::collection($products),
            "service_price"=>Money::parse($this->associatedModel->price)->format(),
            "service_sale_price" => Money::parse($this->associatedModel->sale_price)->format(),
            'discount_percentage' => $discountPercentage,
            'products_total_price' => Money::parse(collect($products)->reduce(fn($carry, $product) => $carry + (($product['sale_price']['amount'] > 0 ? $product['sale_price']['amount'] : $product['price']['amount']) * data_get($product, 'quantity', 1)), 0))->format(),
        ];
    }


}

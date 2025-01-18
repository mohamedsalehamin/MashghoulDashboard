<?php

namespace App\DefaultPanel\Resources\Api\Customer;
use Arr;
use Cknow\Money\Money;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationServiceResource extends JsonResource {

    public function toArray($request): array {
        $products=Arr::get($this->attributes, 'products', []);

        return [
            'id' => $this->associatedModel->id,
            'image' => $this->associatedModel->getFirstMediaUrl(),
            "name" => $this->associatedModel?->title,
            "quantity" => $this->quantity,
            'products' => ReservationProductResource::collection($products),
            "service_price"=>Money::parse($this->associatedModel->price)->format(),

            'products_total_price' => Money::parse(collect($products)->reduce(fn($carry, $product) => $carry + ($product['price']['amount'] * data_get($product,'quantity',1)), 0))->format(),
        ];
    }


}

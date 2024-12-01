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
        ];
    }


}

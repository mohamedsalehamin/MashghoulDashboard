<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price->format(),
            'price_include_taxes' => $this->price_include_taxes->format(),

            'duration' => $this->duration,
            'image' => $this->getFirstMediaUrl(),
            'products' => ProductsResource::collection($this->products),


        ];
    }
}

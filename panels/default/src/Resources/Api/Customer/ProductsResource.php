<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price->format(),

        ];
    }
}

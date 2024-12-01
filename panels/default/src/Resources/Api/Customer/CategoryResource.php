<?php

namespace App\DefaultPanel\Resources\Api\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'image' => $this->getFirstMediaUrl(),
            'name' => $this->name,
            'has_children' => $this->children->count() > 0,


        ];
    }
}

<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryWithChildrenResource extends JsonResource {

    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->getFirstMediaUrl(),
            'children'=>CategoryResource::collection($this->children),


        ];
    }
}

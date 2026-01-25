<?php

namespace App\DefaultPanel\Resources;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesTreeResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request) {
        return [
            "id" => $this->id,
            'text' => $this->name,
            "state" => $this->when(($this->parent == 0 || $this->children->count()) && $this->children->count() <= 3, [
                "opened" => true
            ]),
            "children" => CategoriesTreeResource::collection($this->children),
        ];

    }
}

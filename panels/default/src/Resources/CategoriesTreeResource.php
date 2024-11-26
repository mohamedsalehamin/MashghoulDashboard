<?php

namespace App\DefaultPanel\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesTreeResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
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

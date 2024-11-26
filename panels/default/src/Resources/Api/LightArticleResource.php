<?php

namespace App\DefaultPanel\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class LightArticleResource extends JsonResource {

    public function toArray($request) {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => Str::limit(strip_tags($this->description)),
            'date' => $this->created_at->format('Y-m-d h:i a'),
            'category' => CategoryResource::make($this->category),
            'image' => $this->getFirstMediaUrl(),
            'is_favorite' => false,
        ];
    }


}

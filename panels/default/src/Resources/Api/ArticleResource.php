<?php

namespace App\DefaultPanel\Resources\Api;

use App\ContentModule\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource {

    public function toArray($request) {

        return array(
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->description,
            'date' => $this->created_at->format('Y-m-d h:i a'),
            'category' => $this->category->name,
            'image' => $this->getFirstMediaUrl(),
            'share_url' => route('articles.show', $this->id),
            'articles' => LightArticleResource::collection(Post::whereHas('category', fn($query) => $query->where('id', $this->category->id))->where('id', '!=', $this->id)->limit(3)->get()),

        );
    }


}

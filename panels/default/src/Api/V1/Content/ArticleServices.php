<?php

namespace App\DefaultPanel\Api\V1\Content;


use App\ContentModule\Models\Category;
use App\ContentModule\Models\Post;
use App\DefaultPanel\Resources\Api\ArticleResource;
use App\DefaultPanel\Resources\Api\CategoryResource;
use App\DefaultPanel\Resources\Api\LightArticleResource;
use Tasawk\Api\Facade\Api;

class ArticleServices {
    public function index() {
        return Api::isOk('List of articles', LightArticleResource::collection(Post::enabled()->filtered()->latest()->paginate()));
    }

    public function show(Post $article) {

        return Api::isOk('List of articles', ArticleResource::make($article));
    }

    public function categories() {
        return Api::isOk("List of categories", CategoryResource::collection(Category::enabled()->paginate()));
    }
}

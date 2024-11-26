<?php

namespace App\ContentModule\Filters;


use App\ContentModule\Filters\Articles\CategoryFilter;
use App\ContentModule\Filters\Articles\TitleFilter;
use App\DefaultPanel\Lib\Filters\FilterBaseAbstract;

class ArticlesFilter extends FilterBaseAbstract {
    protected $filters = [
        'category_id' => CategoryFilter::class,
        'title' => TitleFilter::class,

    ];
}

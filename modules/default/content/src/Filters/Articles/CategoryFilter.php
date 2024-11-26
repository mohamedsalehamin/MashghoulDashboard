<?php

namespace App\ContentModule\Filters\Articles;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class CategoryFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->where('category_id', $value);
    }

}

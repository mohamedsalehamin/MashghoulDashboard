<?php

namespace App\CatalogModule\Filters\Reservations;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class DirectionFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        $value = $value == 'asc' ? 'asc' : 'desc';
        return $builder->orderBy('created_at', $value);
    }

}

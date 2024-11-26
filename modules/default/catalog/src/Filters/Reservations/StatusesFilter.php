<?php

namespace App\CatalogModule\Filters\Reservations;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class StatusesFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->whereIn('status', $value);
    }

}

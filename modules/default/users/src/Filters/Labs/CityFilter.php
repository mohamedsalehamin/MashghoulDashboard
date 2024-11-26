<?php

namespace App\UsersModule\Filters\Labs;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class CityFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->where('city_id', $value);
    }

}

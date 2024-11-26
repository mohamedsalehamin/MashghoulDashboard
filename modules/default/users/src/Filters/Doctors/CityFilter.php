<?php

namespace App\UsersModule\Filters\Doctors;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class CityFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->whereHas('clinic', fn($q) => $q->where('city_id', $value));
    }

}

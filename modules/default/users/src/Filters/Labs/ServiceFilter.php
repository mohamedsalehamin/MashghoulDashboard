<?php

namespace App\UsersModule\Filters\Labs;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class ServiceFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->whereHas('user.services', fn($q) => $q->where('name', 'like', "%$value%" ));
    }

}

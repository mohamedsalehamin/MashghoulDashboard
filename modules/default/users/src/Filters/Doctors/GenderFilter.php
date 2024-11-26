<?php

namespace App\UsersModule\Filters\Doctors;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class GenderFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->whereHas('user',fn($q)=>$q->where('gender',$value));
    }

}

<?php

namespace App\UsersModule\Filters\Doctors;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;


class SpecializationsFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return $builder->whereHas('specializations', fn($q) => $q->whereIn('specialization_id', $value));
    }

}

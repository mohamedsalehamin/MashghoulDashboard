<?php

namespace App\UsersModule\Filters\Labs;


use App\DefaultPanel\Interfaces\IFilter;
use App\DefaultPanel\Lib\Filters\Html\Container;
use Illuminate\Database\Eloquent\Builder;
use function PHPUnit\Framework\matches;


class OrderByFilter implements IFilter {
    use Container;

    public function filter(Builder $builder, $value): Builder {
        return match ($value) {
            'rating' => $builder->withAvg('rate', 'rate')->orderBy('rate_avg_rate', request()->get('order_dir', 'desc')),
            default => $builder,
        };
    }

}

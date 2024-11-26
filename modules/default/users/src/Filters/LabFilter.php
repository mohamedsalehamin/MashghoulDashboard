<?php

namespace App\UsersModule\Filters;


use App\DefaultPanel\Lib\Filters\FilterBaseAbstract;
use App\UsersModule\Filters\Labs\CityFilter;
use App\UsersModule\Filters\Labs\OrderByFilter;
use App\UsersModule\Filters\Labs\ServiceFilter;
use App\UsersModule\Filters\Labs\TitleFilter;

class LabFilter extends FilterBaseAbstract {
    protected $filters = [
        'city_id' => CityFilter::class,
        'order_by' => OrderByFilter::class,
        'title' => TitleFilter::class,
        'service_name' => ServiceFilter::class

    ];
}

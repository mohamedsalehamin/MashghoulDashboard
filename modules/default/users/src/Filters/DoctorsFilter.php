<?php

namespace App\UsersModule\Filters;


use App\DefaultPanel\Lib\Filters\FilterBaseAbstract;
use App\UsersModule\Filters\Doctors\CityFilter;
use App\UsersModule\Filters\Doctors\GenderFilter;
use App\UsersModule\Filters\Doctors\NameFilter;
use App\UsersModule\Filters\Doctors\OrderByFilter;
use App\UsersModule\Filters\Doctors\SpecialityFilter;
use App\UsersModule\Filters\Doctors\SpecializationsFilter;
use App\UsersModule\Filters\Doctors\TitleFilter;

class DoctorsFilter extends FilterBaseAbstract {
    protected $filters = [
        'specialty_id' => SpecialityFilter::class,
        'city_id' => CityFilter::class,
        'order_by'=> OrderByFilter::class,
        'name' => NameFilter::class,
        'title_id'=>TitleFilter::class,
        "gender"=>GenderFilter::class,
        "specializations"=>SpecializationsFilter::class

    ];
}

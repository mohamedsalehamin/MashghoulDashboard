<?php

namespace App\CatalogModule\Filters;


use App\CatalogModule\Filters\Reservations\DirectionFilter;
use App\CatalogModule\Filters\Reservations\StatusesFilter;
use App\DefaultPanel\Lib\Filters\FilterBaseAbstract;

class ReservationFilter extends FilterBaseAbstract {
    protected $filters = [

        'direction' => DirectionFilter::class,
        'status' => StatusesFilter::class

    ];
}

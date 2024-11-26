<?php

namespace App\ReportsModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\ReportsModule\ReportsModule
 */
class ReportsModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\ReportsModule\ReportsModule::class;
    }
}

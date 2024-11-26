<?php

namespace App\ReportsModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\ReportsModule\ContentModule
 */
class ContentModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\ReportsModule\ContentModule::class;
    }
}

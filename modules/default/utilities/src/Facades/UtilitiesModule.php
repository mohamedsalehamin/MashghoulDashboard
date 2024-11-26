<?php

namespace App\UtilitiesModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\UtilitiesModule\UtilitiesModule
 */
class UtilitiesModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\UtilitiesModule\UtilitiesModule::class;
    }
}

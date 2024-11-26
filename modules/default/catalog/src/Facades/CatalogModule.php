<?php

namespace App\CatalogModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\CatalogModule\CatalogModule
 */
class CatalogModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\CatalogModule\CatalogModule::class;
    }
}

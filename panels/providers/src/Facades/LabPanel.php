<?php

namespace App\ProviderPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\ProviderPanel\LabPanel
 */
class LabPanel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\ProviderPanel\LabPanel::class;
    }
}

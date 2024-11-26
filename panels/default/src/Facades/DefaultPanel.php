<?php

namespace App\DefaultPanel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\DefaultPanel\DefaultPanel
 */
class DefaultPanel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\DefaultPanel\DefaultPanel::class;
    }
}

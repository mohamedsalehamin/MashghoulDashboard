<?php

namespace App\UsersModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\UsersModule\UsersModule
 */
class UsersModule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\UsersModule\UsersModule::class;
    }
}

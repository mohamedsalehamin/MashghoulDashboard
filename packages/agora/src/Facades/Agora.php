<?php

namespace Packages\Agora\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Packages\Agora\Agora
 */
class Agora extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Packages\Agora\Agora::class;
    }
}

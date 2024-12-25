<?php

namespace App\DefaultPanel\Settings;

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Settings;

class LandingSettings extends Settings
{

    public array $content = [];
    public array $logos = [];



    public static function group(): string
    {
        return 'landing';
    }
}

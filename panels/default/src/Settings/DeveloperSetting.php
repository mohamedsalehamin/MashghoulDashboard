<?php

namespace App\DefaultPanel\Settings;

use Spatie\LaravelSettings\Settings;

class DeveloperSetting extends Settings {
    public bool $otp_code_is_random;
    public bool $debug_mode;


    public static function group(): string {
        return 'developer';
    }
}

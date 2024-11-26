<?php

namespace App\DefaultPanel\Settings;

use Spatie\LaravelSettings\Settings;

class ThirdPartySettings extends Settings {
    public string|null $firebase_server_key;
    public string|null $firebase_server_id;
    public string|null $google_map_key;


    public static function group(): string {
        return 'third_party';
    }
}

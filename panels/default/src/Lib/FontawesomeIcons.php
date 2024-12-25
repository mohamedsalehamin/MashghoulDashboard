<?php

namespace App\DefaultPanel\Lib;

class FontawesomeIcons {
    static public function get(): array {
        return [
            'fab fa-facebook' => 'facebook',
            'fab fa-x' => 'x',
            'fab fa-instagram' => 'instagram',
            'fab fa-youtube' => 'youtube',
            'fab fa-telegram' => 'telegram',
            'fab fa-linkedin' => 'linked in',
            'fab fa-snapchat' => 'snapchat',
            'fab fa-tiktok' => 'tiktok',

        ];
    }

    static public function toSelect(): array {
        return collect(static::get())->mapWithKeys(fn($item, $key) => [$item =>
            <<<HTML
<i class="$key"> </i>
HTML

        ])->toArray();
    }
}

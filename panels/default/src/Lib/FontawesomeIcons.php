<?php

namespace App\DefaultPanel\Lib;

class FontawesomeIcons {
    static public function get(): array {
        return [
            'fab fa-facebook' => 'facebook',
            'fab fa-twitter' => 'twitter',
            'fab fa-instagram' => 'instagram',
            'fab fa-youtube' => 'youtube',
            'fab fa-telegram' => 'telegram',
        ];
    }

    static public function toSelect(): array {
        return collect(static::get())->mapWithKeys(fn($item, $key) => [$item =>
            <<<HTML
<i class="$key"> $item
HTML

        ])->toArray();
    }
}

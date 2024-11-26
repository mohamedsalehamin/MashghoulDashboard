<?php

namespace App\DefaultPanel\Traits\Filament;

use Illuminate\Support\Str;

trait HasTranslationLabel {
    public static function getPluralLabel(): ?string {
        return __('menu.' . Str::of(static::getModel())->afterLast('\\')->headline()->plural()->slug("_")->lower());
    }

    public static function getLabel(): ?string {
        return __('menu.' . Str::of(static::getModel())->afterLast('\\')->slug("_")->lower());
    }


}

<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum ContactSourceEnum: string implements HasLabel {
    case SITE = 'site';
    case PROVIDER = 'provider';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }



}

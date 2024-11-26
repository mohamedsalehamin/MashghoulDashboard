<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum UserStatus: int implements HasLabel {
    case PENDING = -1;
    case ACTIVE = 1;
    case IN_ACTIVE = 0;


    public function getLabel(): ?string {
        return __("panel.enums.$this->name");
    }

    public function getColor(): string {
        return match ($this->value) {
            -1 => 'warning',
            1 => 'success',
            0 => 'danger',
        };
    }

}

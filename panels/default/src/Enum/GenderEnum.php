<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum GenderEnum: string implements HasLabel {
    case MALE = 'male';
    case FEMALE = 'female';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'male', => 'warning',
            'female' => 'primary',
        };

    }

}

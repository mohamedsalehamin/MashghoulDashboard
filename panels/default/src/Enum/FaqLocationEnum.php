<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum FaqLocationEnum: string implements HasLabel {
    case SITE = 'site';
    case LAB = 'lab';
    case DOCTOR = 'doctor';

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

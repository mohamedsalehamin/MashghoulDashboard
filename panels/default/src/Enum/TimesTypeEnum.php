<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum TimesTypeEnum: string implements HasLabel {
    case SPECIFIC_DATES = 'specific_dates';
    case PRIORITY_OF_RESERVATION = 'priority_of_reservation';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'specific_dates', => 'success',
            'priority_of_reservation' => 'primary',
        };

    }

}

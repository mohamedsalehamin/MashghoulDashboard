<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum CouponTypes: string implements HasLabel {
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'percentage', => 'warning',
            'fixed' => 'primary',
        };

    }

}

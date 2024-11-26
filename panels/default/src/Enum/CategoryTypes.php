<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum CategoryTypes: string implements HasLabel {
    case MAIN = 'main';
    case SECONDARY = 'secondary';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'main', => 'warning',
            'secondary'=> 'primary',
        };

    }

}

<?php

namespace App\DefaultPanel\Enum\Catalog;

use Filament\Support\Contracts\HasLabel;

enum OptionTypes: string implements  HasLabel {
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }
}

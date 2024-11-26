<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum ModelStatus: int implements HasLabel {
    case disabled = 0;
    case enabled = 1;


    public function getLabel(): ?string {
        return __("panel.enums.$this->name");
    }


}

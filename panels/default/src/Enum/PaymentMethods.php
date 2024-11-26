<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethods: string implements HasLabel {
    case CASH = 'cash';
    case VISA_MASTER = 'VISA/MASTER';
    case KNET = 'KNET';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'VISA&MASTER','KNET' => 'warning',
            'cash'=> 'success',
        };

    }

}

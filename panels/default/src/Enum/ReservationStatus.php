<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum ReservationStatus: string implements HasLabel {
    case PENDING = 'pending';
    case PROCESSING = 'processing';

    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'pending', => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'canceled' => 'danger',
        };

    }

}

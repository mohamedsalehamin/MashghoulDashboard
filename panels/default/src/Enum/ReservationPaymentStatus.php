<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum ReservationPaymentStatus: string implements HasLabel {
    case PENDING = 'pending';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case CANCELED = 'canceled';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }
    public function getColor(): string {
        return match ($this->value) {
            'pending' => 'warning',
            'paid' => 'success',
            'canceled' => 'danger',
            default=>'danger'
        };
    }
}

<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum WalletWithdrawEnum: string implements HasLabel
{
    case PENDING = 'pending';
    case TRANSFERRED = 'transferred';
    case WAITING_TRANSFER = 'waiting_transfer';
    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return __("panel.enums.$this->name");
    }

    public static function toArray(): array
    {
        return [
            self::PENDING->value => __("panel.enums.pending"),
            self::TRANSFERRED->value => __("panel.enums.transferred"),
            self::WAITING_TRANSFER->value => __("panel.enums.waiting_transfer"),
            self::REJECTED->value => __("panel.enums.REJECTED"),
        ];
    }
     public function getColor(): string {
        return match ($this->value) {
            'pending','created', => 'warning',
            'waiting_transfer' => 'info',
            'transferred' => 'success',
            'rejected' => 'danger',
            default => 'danger',
        };

    }
}
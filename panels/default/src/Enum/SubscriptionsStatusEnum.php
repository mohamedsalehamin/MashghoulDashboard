<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum SubscriptionsStatusEnum: string implements HasLabel {
    case PENDING = 'pending';
    // case RUNNING = 'running';
    case PROCESSING = 'processing';
    case EXIPRED = 'exipred';

    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            // 'running',
            'processing' => 'success',
            'pending' => 'primary',
            'exipred'=> 'danger',
        };

    }

}

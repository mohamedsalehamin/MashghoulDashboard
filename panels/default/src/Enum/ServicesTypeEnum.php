<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum ServicesTypeEnum: string implements HasLabel {
    case OFFLINE = 'offline';
    case VIDEO = 'video';
    case VOICE = 'voice';
    public function getLabel(): ?string {
        return __("panel.enums.$this->value");
    }

    public function getColor(): string {
        return match ($this->value) {
            'offline', => 'warning',
            'video',  => 'primary',
            'chatting','voice'=> 'danger',
        };

    }

}

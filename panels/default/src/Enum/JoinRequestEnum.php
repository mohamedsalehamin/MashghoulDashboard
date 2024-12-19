<?php

namespace App\DefaultPanel\Enum;

use Filament\Support\Contracts\HasLabel;

enum JoinRequestEnum: string implements HasLabel {
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';


    public function getLabel(): ?string {
        return __("panel.enums.$this->name");
    }


}

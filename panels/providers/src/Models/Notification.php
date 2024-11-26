<?php

namespace App\ProviderPanel\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Notification  extends \App\Models\Notification {
    protected function url(): Attribute {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $data = json_decode($attributes['data'], true)['viewData'];
                return match ($data['entity_type']) {
                    'reservation' => route('filament.lab-panel.resources.reservations.view', $data['entity_id']),
                    'wallet' => route('filament.lab-panel.resources.wallets.index',),
                    default => 'javascript:void(0)',
                };

            }
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification {

    protected function title(): Attribute {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => json_decode(json_decode($attributes['data'], true)['title'], true)[app()->getLocale()]
        );
    }

    protected function body(): Attribute {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => json_decode(json_decode($attributes['data'], true)['body'], true)[app()->getLocale()]
        );
    }

    protected function url(): Attribute {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $data = json_decode($attributes['data'], true)['viewData'];
                return match ($data['entity_type']) {
                    'branch' => route('filament.admin.resources.reservation.view', $data['entity_id']),
                    'branch' => route('filament.admin.resources.catalog.branches.edit', $data['entity_id']),
                    default => 'javascript:void(0)',
                };

            }
        );
    }

}

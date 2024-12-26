<?php

namespace App\Models;

use App\ProviderPanel\Filament\Resources\ReservationResource;
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
                    'reservation' =>provider()->id?ReservationResource::getUrl('view',[$data['entity_id']]):\App\CatalogModule\Resources\ReservationResource::getUrl('view',[$data['entity_id']]),
                    default => null,
                };

            }
        );
    }

}

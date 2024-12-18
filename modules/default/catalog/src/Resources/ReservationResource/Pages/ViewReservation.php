<?php

namespace App\CatalogModule\Resources\ReservationResource\Pages;

use App\CatalogModule\Resources\ReservationResource;
use App\CatalogModule\Resources\ReservationResource\Actions\ChangeReservationStatusAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord {
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array {
        return [
            ChangeReservationStatusAction::make(true),
        ];
    }
}

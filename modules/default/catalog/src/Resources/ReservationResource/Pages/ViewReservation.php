<?php

namespace App\CatalogModule\Resources\ReservationResource\Pages;

use App\CatalogModule\Resources\ReservationResource;
use App\CatalogModule\Resources\ReservationResource\Actions\ChangeReservationStatusAction;
use App\CatalogModule\Resources\ReservationResource\Actions\CaptureTabbyPaymentAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewReservation extends ViewRecord {
    protected static string $resource = ReservationResource::class;
    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
    protected function getHeaderActions(): array {
        return [
            ChangeReservationStatusAction::make(true),
            CaptureTabbyPaymentAction::make(),
        ];
    }
}

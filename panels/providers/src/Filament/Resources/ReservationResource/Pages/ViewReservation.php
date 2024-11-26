<?php

namespace App\ProviderPanel\Filament\Resources\ReservationResource\Pages;

use App\CatalogModule\Resources\ConsultingReservationResource\Actions\WritePrescriptionAction;
use App\ProviderPanel\Filament\Resources\ReservationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord {
    protected static string $resource = ReservationResource::class;

}

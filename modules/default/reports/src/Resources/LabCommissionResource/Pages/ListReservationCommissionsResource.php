<?php

namespace App\ReportsModule\Resources\LabCommissionResource\Pages;

use App\ReportsModule\Resources\LabCommissionResource;
use App\ReportsModule\Resources\ReservationCommissionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListReservationCommissionsResource extends ListRecords {
    protected static string $resource = ReservationCommissionResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

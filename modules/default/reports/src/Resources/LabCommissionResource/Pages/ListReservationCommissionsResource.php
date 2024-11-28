<?php

namespace App\ReportsModule\Resources\LabCommissionResource\Pages;

use App\ReportsModule\Resources\LabCommissionResource;
use App\ReportsModule\Resources\ReservationCommissionResource;
use Filament\Resources\Pages\ListRecords;

class ListReservationCommissionsResource extends ListRecords {
    protected static string $resource = ReservationCommissionResource::class;
}

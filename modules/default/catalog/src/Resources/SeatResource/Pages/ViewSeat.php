<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use App\CatalogModule\Resources\SeatResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewSeat extends ViewRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = SeatResource::class;


}

<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\CatalogModule\Resources\SeatResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewSeat extends ViewRecord {
    use Translatable;

    protected static string $resource = SeatResource::class;


}

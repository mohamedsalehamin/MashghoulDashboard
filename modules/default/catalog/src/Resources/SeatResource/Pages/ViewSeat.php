<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use App\CatalogModule\Resources\SeatResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewSeat extends ViewRecord
{
    protected static string $resource = SeatResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

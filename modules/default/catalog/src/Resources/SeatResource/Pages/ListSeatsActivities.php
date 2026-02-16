<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use App\CatalogModule\Resources\SeatResource;
use Filament\Support\Enums\Width;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListSeatsActivities extends ListActivities
{
    protected static string $resource = SeatResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

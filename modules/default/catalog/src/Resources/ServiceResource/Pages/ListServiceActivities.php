<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;

use App\CatalogModule\Resources\ServiceResource;
use Filament\Support\Enums\Width;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListServiceActivities extends ListActivities
{
    protected static string $resource = ServiceResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

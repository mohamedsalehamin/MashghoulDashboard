<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;

use App\CatalogModule\Resources\ServiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;

class ViewService extends ViewRecord
{
    use Translatable;

    protected static string $resource = ServiceResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use App\CatalogModule\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewService extends ViewRecord {
    use Translatable;

    protected static string $resource = ServiceResource::class;



}

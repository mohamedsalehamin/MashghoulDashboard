<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Resources\RateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRate extends ViewRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}


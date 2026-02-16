<?php

namespace App\ContentModule\Resources\CityResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

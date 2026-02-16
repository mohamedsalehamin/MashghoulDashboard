<?php

namespace App\ContentModule\Resources\CountryResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

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

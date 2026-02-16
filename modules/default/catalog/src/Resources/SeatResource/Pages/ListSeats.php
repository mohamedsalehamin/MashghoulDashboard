<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;


use Filament\Actions\CreateAction;
use App\CatalogModule\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSeats extends ListRecords
{
    protected static string $resource = SeatResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }

}

<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;


use Filament\Actions\CreateAction;
use App\CatalogModule\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

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

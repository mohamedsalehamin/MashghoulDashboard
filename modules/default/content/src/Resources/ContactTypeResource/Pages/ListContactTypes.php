<?php

namespace App\ContentModule\Resources\ContactTypeResource\Pages;

use Filament\Actions\CreateAction;
use App\ContentModule\Resources\ContactTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListContactTypes extends ListRecords
{
    protected static string $resource = ContactTypeResource::class;

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

<?php

namespace App\ContentModule\Resources\ContactTypeResource\Pages;

use App\ContentModule\Resources\ContactTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactTypes extends ListRecords
{
    protected static string $resource = ContactTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

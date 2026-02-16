<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use App\ContentModule\Resources\ContactResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}

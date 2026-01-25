<?php

namespace App\ContentModule\Resources\ContactResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\ContentModule\Resources\ContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\ContentModule\Resources\ContactTypeResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\ContentModule\Resources\ContactTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactType extends CreateRecord
{
    use Translatable;

    protected static string $resource = ContactTypeResource::class;
    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

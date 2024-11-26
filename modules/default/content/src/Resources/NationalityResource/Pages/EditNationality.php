<?php

namespace App\ContentModule\Resources\NationalityResource\Pages;

use App\ContentModule\Resources\NationalityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNationality extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = NationalityResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

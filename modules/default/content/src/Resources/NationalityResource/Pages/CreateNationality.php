<?php

namespace App\ContentModule\Resources\NationalityResource\Pages;

use App\ContentModule\Resources\LanguageResource;
use App\ContentModule\Resources\NationalityResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateNationality extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = NationalityResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

<?php

namespace App\ContentModule\Resources\LanguageResource\Pages;

use App\ContentModule\Resources\LanguageResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateLanguage extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

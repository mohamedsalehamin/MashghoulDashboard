<?php

namespace App\ContentModule\Resources\LanguageResource\Pages;

use App\ContentModule\Resources\LanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLanguage extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

<?php

namespace App\ContentModule\Resources\CountryResource\Pages;

use App\ContentModule\Resources\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCountry extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

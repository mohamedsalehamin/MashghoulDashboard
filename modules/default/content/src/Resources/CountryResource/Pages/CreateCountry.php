<?php

namespace App\ContentModule\Resources\CountryResource\Pages;
use App\ContentModule\Resources\CountryResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array {
        return [
           LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

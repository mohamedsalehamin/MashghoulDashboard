<?php

namespace App\ContentModule\Resources\CityResource\Pages;

use App\ContentModule\Resources\CityResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array {
        return [
           LocaleSwitcher::make(),
        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

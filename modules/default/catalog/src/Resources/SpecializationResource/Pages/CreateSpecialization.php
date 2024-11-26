<?php

namespace App\CatalogModule\Resources\SpecializationResource\Pages;

use App\CatalogModule\Resources\CategoryResource;
use App\CatalogModule\Resources\SpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialization extends CreateRecord {
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

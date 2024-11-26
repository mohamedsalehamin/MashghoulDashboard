<?php

namespace App\CatalogModule\Resources\SpecializationResource\Pages;

use App\CatalogModule\Resources\CategoryResource;
use App\CatalogModule\Resources\SpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSpecialization extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }

}

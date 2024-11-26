<?php

namespace App\CatalogModule\Resources\ServiceResource\Pages;

use App\CatalogModule\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\CatalogModule\Resources\PlanResource;

class CreateService extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}

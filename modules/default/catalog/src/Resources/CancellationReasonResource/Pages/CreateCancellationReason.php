<?php

namespace App\CatalogModule\Resources\CancellationReasonResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\CatalogModule\Resources\CancellationReasonResource;

class CreateCancellationReason extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = CancellationReasonResource::class;
    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

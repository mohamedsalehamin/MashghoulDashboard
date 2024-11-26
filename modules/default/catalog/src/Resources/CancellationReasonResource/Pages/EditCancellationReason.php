<?php

namespace App\CatalogModule\Resources\CancellationReasonResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\CatalogModule\Resources\CancellationReasonResource;

class EditCancellationReason extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = CancellationReasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl(): string {
        return $this->getResource()::getUrl("index");
    }
}

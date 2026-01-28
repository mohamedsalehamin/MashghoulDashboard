<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Resources\RateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRate extends EditRecord
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If approval status changed to approved
        if (($data['is_approved'] ?? false) && !$this->record->is_approved) {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
        }
        
        // If approval status changed to not approved
        if (!($data['is_approved'] ?? true) && $this->record->is_approved) {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


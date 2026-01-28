<?php

namespace App\CatalogModule\Resources\RateResource\Pages;

use App\CatalogModule\Resources\RateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRate extends CreateRecord
{
    protected static string $resource = RateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the source as manual for admin-created ratings
        $data['source'] = 'manual';
        
        // Set the user_id if not provided
        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id();
        }

        // Auto-approve manual ratings created by admin
        if ($data['is_approved'] ?? true) {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}


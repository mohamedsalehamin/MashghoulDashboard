<?php

namespace App\ProviderPanel\Filament\Resources\CustomerResource\Pages;

use App\ProviderPanel\Filament\Resources\CustomerResource;
use App\ProviderPanel\Filament\Resources\PatientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord {
    protected static string $resource = CustomerResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone_verified_at'] = now();

        return $data;
    }
}

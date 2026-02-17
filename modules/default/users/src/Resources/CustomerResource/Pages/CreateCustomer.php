<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateCustomer extends CreateRecord {
    protected static string $resource = CustomerResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone_verified_at'] = now();

        return $data;
    }

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

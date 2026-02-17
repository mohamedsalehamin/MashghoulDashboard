<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;

use App\UsersModule\Resources\ProviderResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateProvider extends CreateRecord {
    protected static string $resource = ProviderResource::class;
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

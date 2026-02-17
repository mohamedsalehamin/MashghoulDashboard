<?php

namespace App\UsersModule\Resources\ProviderResource\Pages;


use App\UsersModule\Resources\ProviderResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditProvider extends EditRecord {
    protected static string $resource = ProviderResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

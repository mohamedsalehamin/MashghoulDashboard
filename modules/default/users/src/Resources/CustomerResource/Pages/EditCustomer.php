<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditCustomer extends EditRecord {
    protected static string $resource = CustomerResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

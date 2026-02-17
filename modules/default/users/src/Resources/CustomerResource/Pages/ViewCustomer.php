<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewCustomer extends ViewRecord {
    protected static string $resource = CustomerResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

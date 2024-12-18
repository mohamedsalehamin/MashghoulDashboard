<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord {
    protected static string $resource = CustomerResource::class;

}

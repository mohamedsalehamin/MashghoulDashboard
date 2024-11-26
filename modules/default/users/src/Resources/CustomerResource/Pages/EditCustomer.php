<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\ProviderPanel\Filament\Resources\CustomerResource;
use App\ProviderPanel\Filament\Resources\PatientResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord {
    protected static string $resource = CustomerResource::class;

}

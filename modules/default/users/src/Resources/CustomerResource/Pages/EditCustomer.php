<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\UsersModule\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord {
    protected static string $resource = CustomerResource::class;

}

<?php

namespace App\ReportsModule\Resources\CustomerPaymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\ReportsModule\Resources\CustomerPaymentResource;

class CreateCustomerPayment extends CreateRecord
{
    protected static string $resource = CustomerPaymentResource::class;
}

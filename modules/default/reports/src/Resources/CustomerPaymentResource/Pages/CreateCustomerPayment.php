<?php

namespace App\ReportsModule\Resources\CustomerPaymentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use App\ReportsModule\Resources\CustomerPaymentResource;

class CreateCustomerPayment extends CreateRecord
{
    protected static string $resource = CustomerPaymentResource::class;

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

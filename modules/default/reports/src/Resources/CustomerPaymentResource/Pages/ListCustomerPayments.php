<?php

namespace App\ReportsModule\Resources\CustomerPaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\ReportsModule\Resources\CustomerPaymentResource;

class ListCustomerPayments extends ListRecords
{
    protected static string $resource = CustomerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }
}

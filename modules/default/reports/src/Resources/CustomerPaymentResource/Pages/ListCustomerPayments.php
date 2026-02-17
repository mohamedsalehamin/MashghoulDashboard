<?php

namespace App\ReportsModule\Resources\CustomerPaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
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

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

<?php

namespace App\ReportsModule\Resources\CustomerPaymentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\ReportsModule\Resources\CustomerPaymentResource;

class EditCustomerPayment extends EditRecord
{
    protected static string $resource = CustomerPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

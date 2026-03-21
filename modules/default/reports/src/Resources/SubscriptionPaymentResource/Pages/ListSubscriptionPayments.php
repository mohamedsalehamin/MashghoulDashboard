<?php

namespace App\ReportsModule\Resources\SubscriptionPaymentResource\Pages;

use App\ReportsModule\Resources\SubscriptionPaymentResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSubscriptionPayments extends ListRecords
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getMaxContentWidth(): Width
    {
        return static::getResource()::getMaxContentWidth();
    }
}

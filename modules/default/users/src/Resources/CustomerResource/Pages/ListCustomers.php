<?php

namespace App\UsersModule\Resources\CustomerResource\Pages;

use App\ProviderPanel\Filament\Resources\CustomerResource;
use App\ProviderPanel\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomers extends ListRecords {
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make()
        ];
    }
}

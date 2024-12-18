<?php

namespace App\ProviderPanel\Filament\Resources\CustomerResource\Pages;

use App\ProviderPanel\Filament\Resources\CustomerResource;
use App\ProviderPanel\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord {
    protected static string $resource = CustomerResource::class;

}

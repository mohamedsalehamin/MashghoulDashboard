<?php

namespace App\ProviderPanel\Filament\Resources\ServiceResource\Pages;

use App\ProviderPanel\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;

class ViewService extends ViewRecord {

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}

<?php

namespace App\ProviderPanel\Filament\Resources\ServiceResource\Pages;

use App\ProviderPanel\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}

<?php

namespace App\ProviderPanel\Filament\Resources\SeatResource\Pages;

use App\ProviderPanel\Filament\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeat extends EditRecord {
    use EditRecord\Concerns\Translatable;

    protected static string $resource = SeatResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}

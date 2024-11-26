<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use App\CatalogModule\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSeat extends CreateRecord {
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = SeatResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }


}

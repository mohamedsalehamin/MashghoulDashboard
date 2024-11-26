<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use App\CatalogModule\Resources\PlanResource;
use App\CatalogModule\Resources\SeatResource;
use App\CatalogModule\Resources\ServiceResource;
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

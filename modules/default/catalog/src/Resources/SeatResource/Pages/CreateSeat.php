<?php

namespace App\CatalogModule\Resources\SeatResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\CatalogModule\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSeat extends CreateRecord {
    use Translatable;

    protected static string $resource = SeatResource::class;

    protected function getHeaderActions(): array {
        return [
            LocaleSwitcher::make(),
        ];
    }


}

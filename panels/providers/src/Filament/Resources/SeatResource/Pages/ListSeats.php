<?php

namespace App\ProviderPanel\Filament\Resources\SeatResource\Pages;
use Filament\Actions\CreateAction;
use App\ProviderPanel\Filament\Resources\SeatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


class ListSeats extends ListRecords {
    protected static string $resource = SeatResource::class;


    protected function getHeaderActions(): array {
        return [
            CreateAction::make()
        ];
    }

}

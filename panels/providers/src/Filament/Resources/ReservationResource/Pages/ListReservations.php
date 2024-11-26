<?php

namespace App\ProviderPanel\Filament\Resources\ReservationResource\Pages;


use App\ProviderPanel\Filament\Resources\ReservationResource;
use App\ProviderPanel\Filament\Resources\ReservationResource\Widgets\CalendarWidget;
use Filament\Resources\Pages\ListRecords;


class ListReservations extends ListRecords {
    protected static string $resource = ReservationResource::class;

    protected function getHeaderWidgets(): array {
        return [
            CalendarWidget::make(),
        ];
    }


}

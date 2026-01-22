<?php

namespace App\CatalogModule\Resources\ReservationResource\Pages;


use App\CatalogModule\Models\Reservation;
use App\CatalogModule\Resources\ReservationResource;
use App\DefaultPanel\Enum\ReservationStatus;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;


class ListReservations extends ListRecords {
    protected static string $resource = ReservationResource::class;
    public function getTabs(): array {
        $tabs = [];


        foreach (ReservationStatus::cases() as $case) {
            $tabs[__("panel.enums.$case->value")] = Tab::make()
                ->badge(Reservation::where('status', $case->value)->count())
                ->badgeColor($case->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', $case->value));
        }
        return [
            __('panel.enums.all') => Tab::make()
                ->badge(Reservation::count()),
            ...$tabs
        ];
    }

    public function getDefaultActiveTab(): string|int|null {
        return __('panel.enums.all');
    }


}

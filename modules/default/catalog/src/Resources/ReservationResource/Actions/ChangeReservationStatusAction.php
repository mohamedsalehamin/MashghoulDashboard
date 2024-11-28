<?php

namespace App\CatalogModule\Resources\ReservationResource\Actions;

use App\CatalogModule\Models\Reservation;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;

class ChangeReservationStatusAction {
    static public function make() {
        return Action::make('changeStatus')
            ->label(__('panel.actions.change_status'))
            ->icon('heroicon-o-bolt')
            ->disabled(fn(Reservation $record) => !$record->getAvailableStatus()->count())
            ->form([
                Select::make('status')
                    ->live()
                    ->options(fn($record) => $record->getAvailableStatus()->pluck('label', 'value')->toArray())
                    ->required(),
            ])
            ->visible(fn(Reservation $record) => auth()->user()->can('update', $record))
            ->action(function (array $data, Reservation $record, $action): void {
                $record->update(['status' => $data['status']]);
            });
    }
}

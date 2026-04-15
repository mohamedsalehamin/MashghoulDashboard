<?php

namespace App\CatalogModule\Resources\ReservationResource\Actions;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\RefundTransaction;
use App\DefaultPanel\Enum\ReservationPaymentStatus;
use App\DefaultPanel\Enum\ReservationStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class ChangeReservationStatusAction
{
    public static function make($show = false)
    {
        $action = $show ? Action::make('changeStatus') : Action::make('changeStatus');

        return $action
            ->label(__('panel.actions.change_status'))
            ->icon('heroicon-o-bolt')
            ->disabled(fn (Reservation $record) => ! $record->status == ReservationStatus::COMPLETED)
            ->form([
                Select::make('status')
                    ->live()
                    ->options(fn ($record) => $record->getAvailableStatus()->pluck('label', 'value')->toArray())
                    ->required(),
                Textarea::make('meta_data.cancel_reason')
                    ->visible(fn ($get) => $get('status') == ReservationStatus::NOT_PERFORMED->value)
                    ->label(__('forms.fields.cancel_reason')),
                Checkbox::make('refund_customer_balance')
                    ->visible(fn ($get) => $get('status') == ReservationStatus::CANCELED->value),
            ])
            ->visible(fn (Reservation $record) => auth()->user()->can('update', $record) && $record->status != ReservationStatus::CANCELED)
            ->action(function (array $data, Reservation $record, $action): void {

                if ($data['status'] == ReservationStatus::CANCELED->value &&
                    $record->transaction?->status->value == ReservationPaymentStatus::PENDING->value &&
                    $record->transaction->meta_data['gateway'] == 'myfatoorah'
                ) {
                    $record->transaction->update(['status' => ReservationPaymentStatus::CANCELED->value]);
                }

                $record->update([
                    'status' => $data['status'],
                    'meta_data' => [...$record->meta_data, ...$data['meta_data'] ?? []],
                ]);

                if (data_get($data, 'refund_customer_balance', false)) {
                    RefundTransaction::run($record);
                }

            });
    }
}

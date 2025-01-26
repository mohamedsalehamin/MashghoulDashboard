<?php

namespace App\CatalogModule\Resources\ReservationResource\Actions;

use App\CatalogModule\Models\Reservation;
use App\DefaultPanel\Actions\AddPointToCustomerAction;
use App\DefaultPanel\Actions\RefundTransaction;
use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Settings\GeneralSettings;
use App\Notifications\AdminSendEntitlementsNotification;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\Action;

class ChangeReservationStatusAction {
    static public function make($show = false) {
        $action = $show ? \Filament\Actions\Action::make('changeStatus') : Action::make('changeStatus');
        return $action
            ->label(__('panel.actions.change_status'))
            ->icon('heroicon-o-bolt')
            ->disabled(fn(Reservation $record) => !$record->status == ReservationStatus::COMPLETED)
            ->form([
                Select::make('status')
                    ->live()
                    ->options(fn($record) => $record->getAvailableStatus()->pluck('label', 'value')->toArray())
                    ->required(),
                Textarea::make('meta_data.cancel_reason')
                    ->visible(fn($get) => $get('status') == ReservationStatus::NOT_PERFORMED->value)
                    ->label(__('forms.fields.cancel_reason')),
                Checkbox::make('refund_customer_balance')
                    ->visible(fn($get) => $get('status') == ReservationStatus::CANCELED->value),
            ])
            ->visible(fn(Reservation $record) => auth()->user()->can('update', $record) && $record->status != ReservationStatus::CANCELED)
            ->action(function (array $data, Reservation $record, $action): void {

                if ($data['status'] == ReservationStatus::COMPLETED->value && $record->commission?->amount->formatByDecimal() > 0) {
                    $description = [
                        'ar' => __("panel.messages.gift_for_reservation", ['id' => $record->id], 'ar'),
                        'en' => __("panel.messages.gift_for_reservation", ['id' => $record->id], 'en'),
                    ];
                    AddPointToCustomerAction::run($record->customer, GeneralSettings::getPointsOnAction('reserve'), ['description' => $description]);
                    if (!$record->commission->transferred) {
                        $record->reservable?->user?->notify(new AdminSendEntitlementsNotification());
                        $record->reservable?->deposit(
                            amount: $record->commission?->amount->formatByDecimal(),
                            meta: [
                                'description' => [
                                    'ar' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $record->commission->amount, 'ID' => $record->id], 'ar'),
                                    'en' => __('panel.messages.admin_transfer_lab_commission', ['AMOUNT' => $record->commission->amount, 'ID' => $record->id], 'en'),
                                ],
                            ]
                        );
                        $record->commission?->update(['transferred' => true, 'confirmed' => true]);
                    }
                }
                $record->update(['status' => $data['status'], 'meta_data' => [... $record->meta_data, ...$data['meta_data'] ?? []]]);
                if (data_get($data, 'refund_customer_balance', false)) {
                    RefundTransaction::run($record);
                }

            });
    }
}

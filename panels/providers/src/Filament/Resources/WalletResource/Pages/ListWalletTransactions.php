<?php

namespace App\ProviderPanel\Filament\Resources\WalletResource\Pages;

use App\DefaultPanel\Enum\ReservationStatus;
use App\DefaultPanel\Lib\Utils;
use App\Notifications\ProviderDuesNotification;
use App\ProviderPanel\Filament\Resources\WalletResource;
use App\ProviderPanel\Filament\Resources\WalletResource\Widgets\WalletSummary;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;


class ListWalletTransactions extends ListRecords {
    protected static string $resource = WalletResource::class;

    protected function getHeaderWidgets(): array {
        return [
            WalletSummary::class,
        ];
    }

    protected function getHeaderActions(): array {
        return [

            Action::make('request')
                ->label(__('panel.enums.withdraw_request'))
                ->visible(fn() => provider()->balance > 0)
                ->requiresConfirmation()
                ->modalHeading(__("panel.messages.make_sure_that_bank_account_info_is_right"))
                ->action(function ($data) {
                    \Notification::send(Utils::getAdministrationUsers(), new ProviderDuesNotification(provider()));
                    Notification::make()
                        ->title(__("panel.messages.success"))
                        ->success()
                        ->send();
                })

        ];
    }

    public function getTabs(): array {


        return [
            __('panel.enums.all') => Tab::make()->badge(provider()->wallet->transactions()->count()),

            __("panel.enums.deposit") => Tab::make()
                ->badge(provider()->wallet->transactions()->where('type', 'deposit')->count())
                ->badgeColor(ReservationStatus::PENDING->getColor())
                ->badgeColor(ReservationStatus::PENDING->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'deposit')),

            __("panel.enums.withdraw") => Tab::make()
                ->badge(provider()->wallet->transactions()->where('type', 'withdraw')->count())
                ->badgeColor(ReservationStatus::PROCESSING->getColor())
                ->badgeColor(ReservationStatus::PROCESSING->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('type', 'withdraw')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null {
        return __('panel.enums.all');
    }
}

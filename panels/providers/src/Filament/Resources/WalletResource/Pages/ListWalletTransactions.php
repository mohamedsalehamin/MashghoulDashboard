<?php

namespace App\ProviderPanel\Filament\Resources\WalletResource\Pages;

use App\DefaultPanel\Enum\ReservationStatus;
use App\ProviderPanel\Filament\Resources\WalletResource;
use App\ProviderPanel\Filament\Resources\WalletResource\Widgets\WalletSummary;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;


class ListWalletTransactions extends ListRecords {
    protected static string $resource = WalletResource::class;
    protected function getHeaderWidgets(): array
    {
        return [
           WalletSummary::class,
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

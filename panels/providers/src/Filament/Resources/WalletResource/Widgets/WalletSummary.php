<?php

namespace App\ProviderPanel\Filament\Resources\WalletResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WalletSummary extends BaseWidget {


    protected static ?int $sort = 1;

    protected function getStats(): array {

        return [

            Stat::make(__('panel.stats.balance'),provider()->balance),
            Stat::make(__('panel.stats.total_deposit'), provider()->transactions()->where('type', 'deposit')->sum('amount')),
            Stat::make(__('panel.stats.total_withdraw'), provider()->transactions()->where('type', 'withdraw')->sum('amount')),
        ];
    }


}

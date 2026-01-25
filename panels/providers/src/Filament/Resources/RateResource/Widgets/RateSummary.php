<?php

namespace App\ProviderPanel\Filament\Resources\RateResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RateSummary extends BaseWidget {
    protected string $view = 'filament-widgets::stats-overview-widget';
    protected function getStats(): array {

        return [
            Stat::make(__('panel.stats.rates_count'),(float)provider()->rate()->count()/2??0),
//
            Stat::make(__('panel.stats.avg_rate'),(float)provider()->rate()->avg('rate')??0),
        ];
    }


}

<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Reservation;
use App\UsersModule\Models\Provider;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class ProvidersCountriesCountChart extends ChartWidget {
    use HasWidgetShield;


    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 4;

    protected function getData(): array {
        $stats = Provider::get()
            ->map(fn($provider) => $provider->setAttribute('country_name', $provider->city->state->country->name))
            ->groupBy('country_name')
            ->mapWithKeys(fn($providers, $country) => [$country => count($providers)]);


        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => $stats->values(),
                    'backgroundColor' => [
                        '#ff9999',
                        '#66b3ff',
                        '#ffcc99'
                    ],
                ],
            ],
            'labels' => $stats->keys(),
        ];
    }

    protected function getType(): string {
        return 'doughnut';
    }

    public function getHeading(): string|Htmlable|null {
        return __('panel.stats.reservation_according_to_country');
    }

//    public function getDescription(): string|Htmlable|null {
//        return __('panel.stats.reservations_status_description');
//    }

    public function getTableHeading(): ?string {
        return $this->getHeading();
    }
}

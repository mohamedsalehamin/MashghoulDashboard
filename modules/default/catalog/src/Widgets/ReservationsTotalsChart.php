<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Reservation;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class ReservationsTotalsChart extends ChartWidget {
    use HasWidgetShield;

    protected ?string $heading = 'Chart';
    protected static ?int $sort = 4;

    protected function getData(): array {
        $stats = Reservation::
            paid()
            ->get()->groupBy('status')
            ->mapWithKeys(fn($reservations, $status) =>
            [__('panel.enums.' . $status) => $reservations->sum(fn($reservation) => $reservation->price->formatByDecimal())]);

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
        return __('panel.stats.total_reservations_status_title');
    }

    public function getDescription(): string|Htmlable|null {
        return __('panel.stats.total_reservations_status_description');
    }

    public function getTableHeading(): ?string {
        return $this->getHeading();
    }
}

<?php

namespace App\CatalogModule\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use App\UsersModule\Models\Users\Customer;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class CustomersChart extends ChartWidget {
    use HasWidgetShield;

    protected static ?int $sort = 2;
    public ?string $filter = "year";

    protected function getData(): array {

        $activeFilter = $this->filter;
        $filter = match ($activeFilter) {
            'today' => ['start' => now()->startOfDay(), 'end' => now()->endOfDay(), 'per' => "perHour"],
            'week' => ['start' => now()->startOfWeek(), 'end' => now()->endOfWeek(), 'per' => "perDay"],
            'month' => ['start' => now()->firstOfMonth(), 'end' => now()->lastOfMonth(), 'per' => "perDay"],
            'year' => ['start' => now()->startOfYear(), 'end' => now()->endOfYear(), 'per' => "perMonth"],
        };

        $data = Trend::query(
            Customer::query()
        )
            ->between(
                start: $filter['start'],
                end: $filter['end'],
            )
            ->{$filter['per']}()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => __('sections.customers'),
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),

                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string {
        return 'line';
    }

    public function getHeading(): Htmlable|string|null {
        return __('panel.stats.customers');
    }

    public function getColumnSpan(): int|string|array {
        return 2;
    }

    /**
     * @return string|null
     */
    public function getMaxHeight(): ?string {
        return "400px";
    }

    protected function getFilters(): ?array {
        return [
            'today' => __('panel.stats.today'),
            'week' => __('panel.stats.week'),
            'month' => __('panel.stats.last_month'),
            'year' => __('panel.stats.year'),
        ];
    }



    public function getTableHeading(): ?string {
        return __('panel.stats.customers');
    }

    protected function getOptions(): array {
        return [
            'scales' => [
                'y' => [
                    "ticks" => [
                        "stepSize" => 1
                    ]

                ],
            ]

        ];
    }
}

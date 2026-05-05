<?php

namespace App\ProviderPanel\Filament\Widgets;

use App\CatalogModule\Models\Subscription;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveSubscriptionOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $subscription = Subscription::query()
            ->with(['plan', 'planPrice'])
            ->active()
            ->where('user_id', auth()->id())
            ->latest('end_date')
            ->first();

        if (! $subscription) {
            return [
                Stat::make($this->asText(__('menu.subscription')), $this->asText(__('site.no_data'))),
            ];
        }

        $locale = app()->getLocale();
        $planNameResolved = $subscription->resolvedPlanName($locale);
        $planName = $this->asText($planNameResolved !== '' ? $planNameResolved : '-');
        $priceLabel = $this->asText($subscription->planPrice?->price?->format() ?? $subscription->price?->format() ?? '-');
        $periodResolved = $subscription->resolvedPeriodLabel();
        $periodLabel = $this->asText($periodResolved !== '-' ? $periodResolved : '-');
        $endDate = Carbon::parse($subscription->end_date);

        return [
            Stat::make($this->asText(__('menu.subscription')), $planName)
                ->description($priceLabel.' - '.$periodLabel),

            Stat::make($this->asText(__('forms.fields.end_date')), $this->asText($endDate->translatedFormat('Y-m-d h:i A'))),

            Stat::make(__('panel.stats.remaining'), $this->formatRemaining($endDate)),
        ];
    }

    protected function formatRemaining(Carbon $endDate): string
    {
        if ($endDate->isPast()) {
            return __('panel.enums.exipred');
        }

        $now = now();
        $days = (int) $now->diffInDays($endDate);
        $hours = (int) $now->copy()->addDays($days)->diffInHours($endDate);
        $minutes = (int) $now->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);

        return sprintf(
            '%d %s %d %s %d %s',
            $days,
            __('panel.stats.day'),
            $hours,
            __('panel.stats.hour'),
            $minutes,
            __('panel.stats.minute')
        );
    }

    protected function asText(mixed $value): string
    {
        if (is_array($value)) {
            return implode(' / ', array_map(fn ($item) => $this->asText($item), $value));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return '-';
    }
}

<?php

namespace App\CatalogModule\Widgets;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\Subscription;
use App\CatalogModule\Resources\PlanResource;
use App\CatalogModule\Resources\SubscriptionResource;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlanSubscriptionStats extends BaseWidget
{
    /**
     * Not using {@see \BezhanSalleh\FilamentShield\Traits\HasWidgetShield}: the widget would stay hidden
     * until permissions are generated (`php artisan shield:generate`) and assigned to roles.
     */
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $plansTotal = Plan::query()->count();
        $plansActive = Plan::query()->where('status', true)->count();

        $subscriptionsTotal = Subscription::query()->count();
        $subscriptionsActiveNow = Subscription::query()->active()->count();

        $pending = Subscription::query()->where('status', SubscriptionsStatusEnum::PENDING)->count();
        $running = Subscription::query()->where('status', SubscriptionsStatusEnum::RUNNING)->count();
        $processing = Subscription::query()->where('status', SubscriptionsStatusEnum::PROCESSING)->count();
        $expired = Subscription::query()->where('status', SubscriptionsStatusEnum::EXIPRED)->count();

        $expiringSoon = Subscription::query()
            ->where('status', SubscriptionsStatusEnum::PROCESSING)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(3))
            ->count();

        return [
            Stat::make(__('panel.stats.plans_total'), $plansTotal)
                ->description(__('panel.stats.plans_total_description'))
                ->url(PlanResource::getUrl('index'))
                ->icon('heroicon-o-rectangle-stack'),

            Stat::make(__('panel.stats.plans_active'), $plansActive)
                ->description(__('panel.stats.plans_active_description'))
                ->url(PlanResource::getUrl('index'))
                ->color('success')
                ->icon('heroicon-o-check-badge'),

            Stat::make(__('panel.stats.subscriptions_total'), $subscriptionsTotal)
                ->description(__('panel.stats.subscriptions_total_description'))
                ->url(SubscriptionResource::getUrl('index'))
                ->icon('heroicon-o-document-text'),

            Stat::make(__('panel.stats.subscriptions_active_now'), $subscriptionsActiveNow)
                ->description(__('panel.stats.subscriptions_active_now_description'))
                ->url(SubscriptionResource::getUrl('index'))
                ->color('success')
                ->icon('heroicon-o-bolt'),

            Stat::make(__('panel.stats.subscriptions_expiring_soon'), $expiringSoon)
                ->description(__('panel.stats.subscriptions_expiring_soon_description'))
                ->color($expiringSoon > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-clock'),

            Stat::make(__('panel.stats.subscriptions_by_status_pending'), $pending)
                ->url(SubscriptionResource::getUrl('index'))
                ->icon('heroicon-o-arrow-path'),

            Stat::make(__('panel.stats.subscriptions_by_status_running'), $running)
                ->url(SubscriptionResource::getUrl('index'))
                ->icon('heroicon-o-play'),

            Stat::make(__('panel.stats.subscriptions_by_status_processing'), $processing)
                ->description(__('panel.stats.subscriptions_by_status_processing_description'))
                ->url(SubscriptionResource::getUrl('index'))
                ->icon('heroicon-o-cog-6-tooth'),

            Stat::make(__('panel.stats.subscriptions_by_status_expired'), $expired)
                ->url(SubscriptionResource::getUrl('index'))
                ->color('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }
}

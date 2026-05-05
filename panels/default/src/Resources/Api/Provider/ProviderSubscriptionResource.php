<?php

namespace App\DefaultPanel\Resources\Api\Provider;

use App\CatalogModule\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Current subscription for provider app (aligned with provider dashboard widget).
 */
class ProviderSubscriptionResource extends JsonResource
{
    /**
     * @param  User  $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * @return array<string, mixed>
     */
    public static function subscriptionPayload(?User $user): array
    {
        if (! $user) {
            return [
                'has_active_subscription' => false,
                'subscription' => null,
            ];
        }

        $subscription = Subscription::query()
            ->with(['plan', 'planPrice'])
            ->active()
            ->where('user_id', $user->id)
            ->latest('end_date')
            ->first();

        if (! $subscription) {
            return [
                'has_active_subscription' => false,
                'subscription' => null,
            ];
        }

        $plan = $subscription->plan;
        $planPrice = $subscription->planPrice;
        $locale = app()->getLocale();

        $planNameDisplay = $subscription->resolvedPlanName($locale);
        $planName = $planNameDisplay !== '' ? $planNameDisplay : null;

        $priceFormatted = $planPrice?->price?->format()
            ?? $subscription->price?->format()
            ?? null;

        $resolvedPeriod = $subscription->resolvedPeriodLabel();
        $periodLabel = $planPrice?->period_label ?? (($resolvedPeriod !== '-') ? $resolvedPeriod : null);

        $endDate = Carbon::parse($subscription->end_date);
        $startDate = Carbon::parse($subscription->start_date);

        $planPayload = ($plan || $planName !== null || $subscription->plan_snapshot)
            ? [
                'id' => $plan?->id ?? $subscription->plan_id,
                'name' => $planName,
            ]
            : null;

        $periodFromSnapshot = data_get($subscription->plan_snapshot, 'plan_price.period');
        $planPricePayload = ($planPrice || $periodFromSnapshot)
            ? [
                'id' => $planPrice?->id,
                'period' => $planPrice?->period ?? $periodFromSnapshot,
                'period_label' => $periodLabel,
            ]
            : null;

        return [
            'has_active_subscription' => true,
            'subscription' => [
                'id' => $subscription->id,
                'status' => $subscription->status instanceof \BackedEnum
                    ? $subscription->status->value
                    : (string) $subscription->status,
                'plan' => $planPayload,
                'plan_price' => $planPricePayload,
                'price_formatted' => $priceFormatted,
                'price_and_period' => $priceFormatted && $periodLabel
                    ? $priceFormatted.' - '.$periodLabel
                    : ($priceFormatted ?? $periodLabel),
                'start_date' => $startDate->toIso8601String(),
                'end_date' => $endDate->toIso8601String(),
                'end_date_formatted' => $endDate->translatedFormat('Y-m-d h:i A'),
                'remaining' => self::remainingPayload($endDate),
            ],
        ];
    }

    /**
     * @return array{days: int, hours: int, minutes: int, label: string, is_expired: bool}
     */
    protected static function remainingPayload(Carbon $endDate): array
    {
        if ($endDate->isPast()) {
            return [
                'days' => 0,
                'hours' => 0,
                'minutes' => 0,
                'label' => __('panel.enums.exipred'),
                'is_expired' => true,
            ];
        }

        $now = now();
        $days = (int) $now->diffInDays($endDate);
        $hours = (int) $now->copy()->addDays($days)->diffInHours($endDate);
        $minutes = (int) $now->copy()->addDays($days)->addHours($hours)->diffInMinutes($endDate);

        $label = sprintf(
            '%d %s %d %s %d %s',
            $days,
            __('panel.stats.day'),
            $hours,
            __('panel.stats.hour'),
            $minutes,
            __('panel.stats.minute')
        );

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'label' => $label,
            'is_expired' => false,
        ];
    }

    /**
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return self::subscriptionPayload($this->resource);
    }
}

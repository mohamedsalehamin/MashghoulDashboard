<?php

namespace App\DefaultPanel\Actions;

use App\UsersModule\Models\Provider;
use Lorisleiva\Actions\Concerns\AsAction;

class AddReservationCommissionAction
{
    use AsAction;

    public function handle($reservation): void
    {
        $percentage = $this->getProviderCommissionPercentage($reservation);
        $amount = ($reservation->as_cart->getNetProfitTotal() / 100) * $percentage;

        $reservation->commission()->create([
            'percentage' => $percentage,
            'amount' => $amount,
        ]);
    }

    private function getProviderCommissionPercentage($reservation): float
    {
        $provider = $reservation->reservable;

        if (! $provider instanceof Provider) {
            return $this->fallbackPercentage();
        }

        $subscription = $provider->activeSubscription()->with('plan')->first();
        $commissionPercent = $subscription?->resolvedPlanCommissionPercent();

        if ($commissionPercent !== null) {
            return (float) (100 - $commissionPercent);
        }

        return $this->fallbackPercentage();
    }

    private function fallbackPercentage(): float
    {
        return (float) config('commission.default_provider_percentage', 80);
    }
}

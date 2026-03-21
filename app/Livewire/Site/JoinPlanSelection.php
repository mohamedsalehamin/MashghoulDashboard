<?php

namespace App\Livewire\Site;

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Component;

class JoinPlanSelection extends Component
{
    public string $selectedPeriod = 'monthly';

    public string $paymentMethod = 'myfatoorah';

    public function selectPeriod(string $period): void
    {
        $this->selectedPeriod = $period;
    }

    public function plans(): Collection
    {
        return Plan::query()
            ->enabled()
            ->with(['planPrices' => fn ($q) => $q->orderBy('period')])
            ->orderByDesc('is_free')
            ->orderBy('id')
            ->get();
    }

    /**
     * Resolve the price row for the current tab. Do not fall back to the first row
     * when multiple periods exist (that showed monthly price + شهري for every tab).
     * Legacy: a single plan_prices row still applies to all tabs.
     */
    public function resolvePriceForPlan(Plan $plan): ?PlanPrice
    {
        $period = strtolower(trim((string) $this->selectedPeriod));
        $prices = $plan->planPrices;

        $match = $prices->first(
            fn (PlanPrice $p) => strcasecmp(trim((string) $p->period), $period) === 0
        );

        if ($match) {
            return $match;
        }

        if ($prices->count() === 1) {
            return $prices->first();
        }

        return null;
    }

    /**
     * @return SupportCollection<int, array{plan: Plan, planPrice: PlanPrice}>
     */
    protected function mapPlansWithPrices(Collection $allPlans): SupportCollection
    {
        return $allPlans->map(function (Plan $plan) {
            $planPrice = $this->resolvePriceForPlan($plan);

            return $planPrice ? ['plan' => $plan, 'planPrice' => $planPrice] : null;
        })->filter()->values();
    }

    public function render()
    {
        $allPlans = $this->plans();
        $selectedPeriod = $this->selectedPeriod;

        return view('livewire.site.join-plan-selection', [
            'selectedPeriod' => $selectedPeriod,
            'paymentMethod' => $this->paymentMethod,
            'plansWithPrices' => $this->mapPlansWithPrices($allPlans),
            'totalPlansCount' => $allPlans->count(),
        ]);
    }
}

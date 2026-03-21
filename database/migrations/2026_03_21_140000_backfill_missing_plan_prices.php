<?php

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use Illuminate\Database\Migrations\Migration;

/**
 * Plans edited in admin without persisting plan_prices (repeater dehydrated issue) had no rows;
 * join page hides plans with no prices. This backfills default periods for affected plans.
 */
return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['period' => PlanPrice::PERIOD_MONTHLY, 'days_count' => 30],
            ['period' => PlanPrice::PERIOD_QUARTERLY, 'days_count' => 90],
            ['period' => PlanPrice::PERIOD_YEARLY, 'days_count' => 365],
        ];

        Plan::query()->whereDoesntHave('planPrices')->each(function (Plan $plan) use ($defaults) {
            foreach ($defaults as $row) {
                $plan->planPrices()->firstOrCreate(
                    ['period' => $row['period']],
                    [
                        'price' => 0,
                        'days_count' => $row['days_count'],
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        // Non-destructive: do not remove backfilled rows
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Seed plan_prices for plans that have none (e.g. after plan_prices migration ran
     * when plans.price was already dropped).
     */
    public function up(): void
    {
        $planIds = DB::table('plans')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('plan_prices')
                    ->whereColumn('plan_prices.plan_id', 'plans.id');
            })
            ->pluck('id');

        foreach ($planIds as $planId) {
            $now = now();
            DB::table('plan_prices')->insert([
                ['plan_id' => $planId, 'period' => 'monthly', 'price' => 99, 'days_count' => 30, 'created_at' => $now, 'updated_at' => $now],
                ['plan_id' => $planId, 'period' => 'quarterly', 'price' => 249, 'days_count' => 90, 'created_at' => $now, 'updated_at' => $now],
                ['plan_id' => $planId, 'period' => 'yearly', 'price' => 999, 'days_count' => 365, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    public function down(): void
    {
        // No-op: we don't know which rows we added
    }
};

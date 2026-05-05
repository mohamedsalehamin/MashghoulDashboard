<?php

use App\CatalogModule\Models\Plan;
use App\CatalogModule\Models\PlanPrice;
use App\CatalogModule\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->json('plan_snapshot')->nullable()->after('features');
        });

        Subscription::query()
            ->whereNull('plan_snapshot')
            ->chunkById(100, function ($subscriptions): void {
                foreach ($subscriptions as $subscription) {
                    $plan = Plan::query()->find($subscription->plan_id);
                    if (! $plan) {
                        continue;
                    }
                    $planPrice = $subscription->plan_price_id
                        ? PlanPrice::query()->find($subscription->plan_price_id)
                        : null;

                    $subscription->plan_snapshot = Subscription::buildPlanSnapshot($plan, $planPrice);
                    $subscription->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('plan_snapshot');
        });
    }
};

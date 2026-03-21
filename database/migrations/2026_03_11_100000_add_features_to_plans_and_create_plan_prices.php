<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->json('features')->nullable()->after('meta_data');
        });

        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('period', 20); // monthly, quarterly, yearly
            $table->float('price', 12, 3);
            $table->unsignedInteger('days_count');
            $table->timestamps();

            $table->unique(['plan_id', 'period']);
        });

        // Migrate existing plan prices to plan_prices (monthly default)
        if (Schema::hasColumn('plans', 'price')) {
            $plans = DB::table('plans')->get();
            foreach ($plans as $plan) {
                DB::table('plan_prices')->insert([
                    'plan_id' => $plan->id,
                    'period' => 'monthly',
                    'price' => $plan->price ?? 0,
                    'days_count' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('plan_price_id')->nullable()->after('plan_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['plan_price_id']);
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('features');
            $table->float('price', 8, 3)->nullable()->after('description');
        });

        Schema::dropIfExists('plan_prices');
    }
};

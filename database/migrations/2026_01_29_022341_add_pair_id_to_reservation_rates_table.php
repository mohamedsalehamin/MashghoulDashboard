<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            // UUID to link paired ratings (service + place) together
            $table->uuid('pair_id')->nullable()->after('parent_id');
            $table->index('pair_id');
        });

        // Update existing reservation-based ratings to share pair_id
        // Group by reservation_id and assign same pair_id
        $this->updateExistingRatings();
    }

    public function down(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            $table->dropIndex(['pair_id']);
            $table->dropColumn('pair_id');
        });
    }

    private function updateExistingRatings(): void
    {
        // Get all unique reservation_ids
        $reservationIds = \DB::table('reservation_rates')
            ->whereNotNull('reservation_id')
            ->distinct()
            ->pluck('reservation_id');

        foreach ($reservationIds as $reservationId) {
            $pairId = \Illuminate\Support\Str::uuid()->toString();
            \DB::table('reservation_rates')
                ->where('reservation_id', $reservationId)
                ->whereNull('parent_id') // Only top-level ratings, not replies
                ->update(['pair_id' => $pairId]);
        }
    }
};

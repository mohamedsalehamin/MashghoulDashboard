<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            // Make rate nullable to allow replies (which don't have ratings)
            $table->integer('rate')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            // Set default to 0 for non-nullable
            $table->integer('rate')->default(0)->change();
        });
    }
};

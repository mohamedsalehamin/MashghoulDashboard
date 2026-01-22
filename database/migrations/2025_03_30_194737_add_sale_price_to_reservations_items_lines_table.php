<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reservations_items_lines', function (Blueprint $table) {
            $table->float('sale_price', 8, 3)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('reservations_items_lines', function (Blueprint $table) {
            $table->dropColumn('sale_price');
        });
    }
}; 
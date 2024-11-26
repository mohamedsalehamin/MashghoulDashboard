<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('report_reasons', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->boolean('status');
            $table->timestamps();
        });
        Schema::create('reservations_cancellation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reservation_id");
            $table->foreignId("reason_id");
            $table->text("note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('report_reasons');
        Schema::dropIfExists('reservations_cancellation');
    }
};

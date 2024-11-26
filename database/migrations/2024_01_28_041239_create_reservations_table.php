<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->morphs('reservable');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('parent_id')->nullable();
            $table->dateTime('date');
            $table->string('period')->nullable();
            $table->string('service_type');
            $table->string('reserve_type');
            $table->string('status');
            $table->float('price', 8, 3);
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });

        Schema::create('reservations_cancellation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reservation_id");
            $table->foreignId("reason_id");
            $table->longText("comment")->nullable();
            $table->timestamps();
        });
        Schema::create('reservations_prescription', function (Blueprint $table) {
            $table->id();
            $table->longText("diagnosis");
            $table->foreignId("reservation_id");
            $table->boolean('has_visit_reservation');
            $table->timestamps();
        });
        Schema::create('reservations_prescription_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('type');
            $table->longText('notes');
            $table->foreignId("reservations_prescription_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('reservations_prescription');
        Schema::dropIfExists('reservations_prescription_items');
        Schema::dropIfExists('reservations_cancellation');
        Schema::dropIfExists('reservations');
    }
};

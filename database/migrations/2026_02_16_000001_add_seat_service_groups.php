<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('seat_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seat_id')->constrained()->cascadeOnDelete();
            $table->json('title');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::table('seat_service', function (Blueprint $table) {
            $table->foreignId('service_group_id')->nullable()->after('service_id')->constrained('seat_groups')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('seat_service', function (Blueprint $table) {
            $table->dropForeign(['service_group_id']);
        });
        Schema::dropIfExists('seat_groups');
    }
};

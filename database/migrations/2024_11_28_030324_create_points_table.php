<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('points_levels', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->integer('value');
            $table->decimal('price');
            $table->boolean('status');
            $table->string('duration');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId("level_id")->nullable();
            $table->integer('value');
            $table->boolean('transferred');
            $table->json('meta_data');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('points_levels');
        Schema::dropIfExists('points');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('api_token',255)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->json('settings')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->foreignId('gender')->default('male');
            $table->date('dob')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('users');
    }
};

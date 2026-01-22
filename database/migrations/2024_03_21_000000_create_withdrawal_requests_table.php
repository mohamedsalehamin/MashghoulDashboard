<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs('withdrawable'); // For both Customer and Provider
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('transfer_amount', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('bank_details')->nullable();
            $table->json('rejection_reason')->nullable();
            $table->string('receipt')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
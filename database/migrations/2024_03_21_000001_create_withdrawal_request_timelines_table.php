<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('withdrawal_request_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('withdrawal_request_id')->constrained('withdrawal_requests')->onDelete('cascade');
            $table->string('status');
            $table->json('title');
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_request_timelines');
    }
}; 
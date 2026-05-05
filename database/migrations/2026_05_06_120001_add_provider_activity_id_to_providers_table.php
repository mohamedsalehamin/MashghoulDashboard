<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->foreignId('provider_activity_id')
                ->nullable()
                ->after('category_id')
                ->constrained('provider_activities')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provider_activity_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'meta_description')) {
                $table->json('meta_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('pages', 'meta_keywords')) {
                $table->json('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('pages', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });
    }
};

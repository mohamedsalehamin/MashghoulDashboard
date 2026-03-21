<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->json('title');
                $table->json('slug')->nullable();
                $table->json('description');
                $table->timestamp('publish_date');
                $table->unsignedBigInteger('category_id')->nullable();
                $table->boolean('status')->default(1);
                $table->json('meta_description')->nullable();
                $table->json('meta_keywords')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'meta_description')) {
                $table->json('meta_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('posts', 'meta_keywords')) {
                $table->json('meta_keywords')->nullable()->after('meta_description');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('posts', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'slug')) {
                $table->json('slug')->nullable()->after('name');
            }
        });

        // Backfill slugs from name for existing categories
        $categories = DB::table('categories')->get();
        foreach ($categories as $row) {
            $name = json_decode($row->name, true);
            if (! is_array($name)) {
                continue;
            }
            $slug = $row->slug ? (json_decode($row->slug, true) ?: []) : [];
            foreach ($name as $locale => $title) {
                if (! empty(trim((string) $title)) && empty(trim((string) ($slug[$locale] ?? '')))) {
                    $slug[$locale] = Str::slug($title);
                }
            }
            if (! empty($slug)) {
                DB::table('categories')->where('id', $row->id)->update(['slug' => json_encode($slug)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};

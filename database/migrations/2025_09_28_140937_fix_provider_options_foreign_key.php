<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('provider_options', function (Blueprint $table) {
            // First, check if the foreign key exists and drop it
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'provider_options' 
                AND COLUMN_NAME = 'provider_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE provider_options DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
        });
        
        // Now add the correct foreign key constraint
        Schema::table('provider_options', function (Blueprint $table) {
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('providers')
                  ->onDelete('cascade')
                  ->name('provider_options_provider_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_options', function (Blueprint $table) {
            $table->dropForeign('provider_options_provider_id_foreign');
            
            // Restore the original (incorrect) foreign key
            $table->foreign('provider_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
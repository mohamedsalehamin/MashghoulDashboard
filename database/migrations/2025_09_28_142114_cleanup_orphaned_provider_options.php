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
        // First, let's see what we're dealing with
        echo "Checking for orphaned provider_options records...\n";
        
        // Find orphaned records where provider_id doesn't exist in providers table
        $orphanedRecords = DB::table('provider_options')
            ->leftJoin('providers', 'provider_options.provider_id', '=', 'providers.id')
            ->whereNull('providers.id')
            ->select('provider_options.*')
            ->get();
            
        echo "Found " . $orphanedRecords->count() . " orphaned records\n";
        
        if ($orphanedRecords->count() > 0) {
            echo "Orphaned provider_id values: " . $orphanedRecords->pluck('provider_id')->unique()->implode(', ') . "\n";
            
            // Option 1: Delete orphaned records (recommended if the data is truly orphaned)
            $deletedCount = DB::table('provider_options')
                ->leftJoin('providers', 'provider_options.provider_id', '=', 'providers.id')
                ->whereNull('providers.id')
                ->delete();
                
            echo "Deleted {$deletedCount} orphaned provider_options records\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This cleanup cannot be reversed
        echo "Cannot reverse orphaned data cleanup\n";
    }
};
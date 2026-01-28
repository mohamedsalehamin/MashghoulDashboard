<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds support for:
     * - Manual ratings (from admin dashboard)
     * - Provider replies to ratings
     * - Approval system for manual ratings
     */
    public function up(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            // Make reservation_id nullable (for manual ratings that aren't tied to reservations)
            $table->unsignedBigInteger('reservation_id')->nullable()->change();
            
            // Provider being rated (useful for manual ratings and quick queries)
            $table->foreignId('provider_id')->nullable()->after('reservation_id')
                ->constrained('users')->onDelete('cascade');
            
            // User who gave the rating (customer or admin for manual ratings)
            $table->foreignId('user_id')->nullable()->after('provider_id')
                ->constrained('users')->onDelete('cascade');
            
            // Parent rating ID for replies (self-referencing)
            $table->foreignId('parent_id')->nullable()->after('id')
                ->constrained('reservation_rates')->onDelete('cascade');
            
            // Source of the rating: reservation, manual, or reply
            $table->enum('source', ['reservation', 'manual', 'reply'])->default('reservation')->after('type');
            
            // Approval system for manual ratings
            $table->boolean('is_approved')->default(true)->after('source');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                ->constrained('users')->onDelete('set null');
            
            // Indexes for better query performance
            $table->index(['provider_id', 'is_approved', 'source'], 'rates_provider_approved_source_idx');
            $table->index(['parent_id'], 'rates_parent_idx');
            $table->index(['source'], 'rates_source_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_rates', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['approved_by']);
            
            // Drop indexes
            $table->dropIndex('rates_provider_approved_source_idx');
            $table->dropIndex('rates_parent_idx');
            $table->dropIndex('rates_source_idx');
            
            // Drop columns
            $table->dropColumn([
                'provider_id',
                'user_id', 
                'parent_id',
                'source',
                'is_approved',
                'approved_at',
                'approved_by'
            ]);
        });
    }
};


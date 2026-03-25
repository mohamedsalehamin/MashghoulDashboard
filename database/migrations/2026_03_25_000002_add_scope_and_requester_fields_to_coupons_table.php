<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->string('scope')->default('general')->after('code'); // general|providers
            $table->string('requested_by')->default('admin')->after('scope'); // admin|provider
            $table->foreignId('provider_id')->nullable()->after('requested_by')->constrained('providers')->nullOnDelete();
            $table->string('apply_target')->nullable()->after('provider_id'); // provider-requested only
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('provider_id');
            $table->dropColumn(['scope', 'requested_by', 'apply_target']);
        });
    }
};


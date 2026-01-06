<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            // Remove price_per_minute - price is now in price_config JSON
            if (Schema::hasColumn('vps_usages', 'price_per_minute')) {
                $table->dropColumn('price_per_minute');
            }
            
            // Calculated fee for this period (calculated from price_config and duration)
            // Duration is calculated dynamically from created_at and lastest_time_the_same
            $table->decimal('calculated_fee', 15, 4)->default(0)->after('price_config')
                ->comment('Calculated fee = (n_cpu_core_price * cpu + n_ram_gb_price * ram_gb + ...) * duration_minutes / 1440 (in thousands VND)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            // Add price_per_minute back
            $table->decimal('price_per_minute', 15, 4)->default(0)->after('full_info');
            
            // Drop calculated_fee column
            $table->dropColumn('calculated_fee');
        });
    }
};

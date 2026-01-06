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
            // Lưu bảng giá từ config tại thời điểm ghi nhận
            // Gồm: n_cpu_core.price, n_ram_gb.price, n_gb_disk.price, n_ip_address.price, n_network_dedicated_mbit.price
            if (!Schema::hasColumn('vps_usages', 'price_config')) {
                $table->json('price_config')
                    ->nullable()
                    ->after('price_per_minute')
                    ->comment('Pricing config snapshot từ config/vps_config.php');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['price_config']);
        });
    }
};

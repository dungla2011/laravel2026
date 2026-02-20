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
            $table->decimal('price_month', 18, 2)->nullable()->after('number_ip_address')
                ->comment('Fixed monthly price from vps_instances (null for per-minute billing)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn('price_month');
        });
    }
};

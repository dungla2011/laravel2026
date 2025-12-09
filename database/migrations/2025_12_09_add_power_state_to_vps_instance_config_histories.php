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
        Schema::table('vps_instance_config_histories', function (Blueprint $table) {
            if (!Schema::hasColumn('vps_instance_config_histories', 'power_state')) {
                $table->string('power_state')->nullable()->after('number_ip_address')->comment('Power state (POWERED_ON, POWERED_OFF, SUSPENDED)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_instance_config_histories', function (Blueprint $table) {
            $table->dropColumn(['power_state']);
        });
    }
};

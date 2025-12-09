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
            // Add config fields if they don't exist
            if (!Schema::hasColumn('vps_instance_config_histories', 'name')) {
                $table->string('name')->nullable()->after('instance_id')->comment('VM name');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'cpu')) {
                $table->integer('cpu')->default(0)->after('name')->comment('CPU count');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'ram_gb')) {
                $table->integer('ram_gb')->default(0)->after('cpu')->comment('RAM in GB');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'disk_gb')) {
                $table->integer('disk_gb')->default(0)->after('ram_gb')->comment('Disk in GB');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'number_ip_address')) {
                $table->integer('number_ip_address')->default(0)->after('disk_gb')->comment('Number of IP addresses/NICs');
            }
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
            $table->dropColumn([
                'name',
                'cpu',
                'ram_gb',
                'disk_gb',
                'number_ip_address',
                'power_state'
            ]);
        });
    }
};

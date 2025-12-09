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
            if (!Schema::hasColumn('vps_usages', 'cpu')) {
                $table->integer('cpu')->default(0)->after('vmware_vm_id')->comment('CPU count');
            }
            if (!Schema::hasColumn('vps_usages', 'ram_gb')) {
                $table->integer('ram_gb')->default(0)->after('cpu')->comment('RAM in GB');
            }
            if (!Schema::hasColumn('vps_usages', 'disk_gb')) {
                $table->integer('disk_gb')->default(0)->after('ram_gb')->comment('Disk in GB');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['cpu', 'ram_gb', 'disk_gb']);
        });
    }
};

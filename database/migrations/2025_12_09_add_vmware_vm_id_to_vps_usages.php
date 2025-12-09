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
            if (!Schema::hasColumn('vps_usages', 'vmware_vm_id')) {
                $table->string('vmware_vm_id')->nullable()->index()->after('instance_id')->comment('VMware VM ID from vCenter (e.g., vm-123)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['vmware_vm_id']);
        });
    }
};

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
            if (!Schema::hasColumn('vps_instance_config_histories', 'vmware_vm_id')) {
                $table->string('vmware_vm_id')->nullable()->after('instance_id')->comment('VMware VM ID from vCenter (e.g., vm-123)');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'bios_uuid')) {
                $table->string('bios_uuid', 64)->nullable()->after('vmware_vm_id')->comment('UUID from VM BIOS - persists across vCenter moves');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'instance_uuid')) {
                $table->string('instance_uuid', 64)->nullable()->index()->after('bios_uuid')->comment('UUID from vCenter - changes when moved');
            }
            if (!Schema::hasColumn('vps_instance_config_histories', 'full_info')) {
                $table->json('full_info')->nullable()->after('log')->comment('Complete VM hardware info snapshot from vCenter API');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_instance_config_histories', function (Blueprint $table) {
            $table->dropColumn(['vmware_vm_id', 'bios_uuid', 'instance_uuid', 'full_info']);
        });
    }
};

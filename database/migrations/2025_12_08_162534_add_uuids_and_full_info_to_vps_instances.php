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
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->string('bios_uuid', 64)->nullable()->unique()->after('vmware_vm_id')->comment('UUID from VM BIOS - persists across vCenter moves');
            $table->string('instance_uuid', 64)->nullable()->index()->after('bios_uuid')->comment('UUID from vCenter - changes when moved');
            $table->json('full_info')->nullable()->after('instance_uuid')->comment('Complete VM hardware info snapshot from vCenter API');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropColumn(['bios_uuid', 'instance_uuid', 'full_info']);
        });
    }
};

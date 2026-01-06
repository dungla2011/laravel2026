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
        Schema::table('vps_os_versions', function (Blueprint $table) {
            $table->string('vm_name', 256)->nullable()->comment('VM name template or identifier for this OS');
            $table->string('iso_name', 256)->nullable()->comment('ISO file name or path for OS installation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_os_versions', function (Blueprint $table) {
            $table->dropColumn(['vm_name', 'iso_name']);
        });
    }
};

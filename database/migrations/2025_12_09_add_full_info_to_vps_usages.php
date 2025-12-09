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
            if (!Schema::hasColumn('vps_usages', 'full_info')) {
                $table->json('full_info')->nullable()->after('power_state')->comment('Complete VM hardware info snapshot from vCenter API');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['full_info']);
        });
    }
};

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
            $table->string('init_mac_address', 64)->nullable()->after('init_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropColumn('init_mac_address');
        });
    }
};

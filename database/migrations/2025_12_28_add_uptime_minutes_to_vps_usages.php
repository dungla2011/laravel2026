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
            if (!Schema::hasColumn('vps_usages', 'uptime_minutes')) {
                $table->bigInteger('uptime_minutes')->default(0)->comment('VM uptime in minutes (from created_at to now)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            if (Schema::hasColumn('vps_usages', 'uptime_minutes')) {
                $table->dropColumn('uptime_minutes');
            }
        });
    }
};

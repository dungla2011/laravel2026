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
            $table->datetime('end_time_used')->nullable()->after('timestamp_minute')
                ->comment('End time of usage session - when reached, create new record for new session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn('end_time_used');
        });
    }
};

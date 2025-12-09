<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->bigInteger('count_update_status')->default(0)->comment('Number of times this record was updated without config change');
            $table->dateTime('lastest_time_the_same')->nullable()->comment('Latest time when config was the same (no change detected)');
        });
    }

    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['count_update_status', 'lastest_time_the_same']);
        });
    }
};

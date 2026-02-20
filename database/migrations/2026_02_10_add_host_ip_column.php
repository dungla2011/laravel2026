<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            if (!Schema::hasColumn('vps_usages', 'last_host_ip')) {
                $table->string('last_host_ip', 32)->nullable()->comment('Host IP address (32 chars max)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            if (Schema::hasColumn('vps_usages', 'last_host_ip')) {
                $table->dropColumn('last_host_ip');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->text('list_ip_address')->nullable()->comment('List of IP addresses separated by comma');
            $table->dateTime('last_found_ip')->nullable()->comment('Last time IP addresses were found/updated');
        });
    }

    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropColumn(['list_ip_address', 'last_found_ip']);
        });
    }
};

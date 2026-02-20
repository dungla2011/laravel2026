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
            $table->dateTime('last_billing_start_at')->nullable()->comment('Next billing period starts from this timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropColumn('last_billing_start_at');
        });
    }
};

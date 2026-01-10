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
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->string('mrc_order_id', 64)->nullable()->unique()->after('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->dropUnique(['mrc_order_id']);
            $table->dropColumn('mrc_order_id');
        });
    }
};

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
            // Add invoice_number field
            if (!Schema::hasColumn('user_recharges', 'invoice_number')) {
                $table->string('invoice_number', 64)->nullable()->comment('Số hóa đơn');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_recharges', function (Blueprint $table) {
            $table->dropColumnIfExists('invoice_number');
        });
    }
};

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
        Schema::table('vps_instance_config_histories', function (Blueprint $table) {
            // Make price_per_minute nullable with default 0 if it exists
            if (Schema::hasColumn('vps_instance_config_histories', 'price_per_minute')) {
                $table->decimal('price_per_minute', 10, 4)->nullable()->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};

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
            // Add index on created_at for efficient time-based queries
            $table->index('created_at');
            
            // Add composite index for bios_uuid and created_at to optimize the 5-minute check query
            $table->index(['bios_uuid', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['bios_uuid', 'created_at']);
        });
    }
};

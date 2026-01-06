<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->string('type', 64)->nullable()->comment('Instance type (e.g., VM, Container, etc.)');
        });
    }

    public function down(): void
    {
        Schema::table('vps_instances', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

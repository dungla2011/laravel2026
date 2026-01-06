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
        Schema::create('vps_os_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('OS name (e.g., Ubuntu 22.04, CentOS 8)');
            $table->string('slug', 100)->unique()->comment('URL-friendly slug');
            $table->text('description')->nullable()->comment('OS description and features');
            $table->integer('display_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Enable/disable OS option');
            $table->timestamps();
            $table->index('is_active');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_os_versions');
    }
};

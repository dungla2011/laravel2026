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
        Schema::create('cloud_transfer', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('userid', 50)->nullable();
            $table->string('file')->nullable();
            $table->bigInteger('bytes')->nullable();
            $table->string('host')->nullable();
            $table->string('ip', 20)->nullable();
            $table->string('cmd', 20)->nullable();
            $table->bigInteger('transfer_time')->nullable();
            $table->dateTime('time')->nullable();
            $table->string('status', 5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_transfer');
    }
};

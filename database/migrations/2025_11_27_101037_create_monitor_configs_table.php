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
        Schema::create('monitor_configs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 128);
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->default(1)->index();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('image_list', 256)->nullable();
            $table->text('log')->nullable();
            $table->string('alert_type', 64)->nullable();
            $table->text('alert_config')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_configs');
    }
};

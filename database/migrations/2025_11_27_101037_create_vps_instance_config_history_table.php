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
        Schema::create('vps_instance_config_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->nullable();
            $table->smallInteger('status')->nullable()->default(1);
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('instance_id')->index();
            $table->integer('cpu');
            $table->integer('ram_gb');
            $table->integer('disk_gb');
            $table->integer('network_mbit')->nullable()->default(0);
            $table->integer('number_ip_address')->nullable()->default(1);
            $table->decimal('price_per_minute', 18, 8);
            $table->string('change_type', 64)->nullable();
            $table->dateTime('changed_at')->nullable()->useCurrent();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->text('log')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_instance_config_history');
    }
};

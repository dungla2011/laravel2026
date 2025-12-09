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
        Schema::create('vps_usages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 64)->nullable();
            $table->smallInteger('status')->nullable()->default(1);
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('instance_id')->index();
            $table->dateTime('timestamp_minute')->index();
            $table->integer('number_ip_address')->nullable()->default(0);
            $table->decimal('price_per_minute', 18, 8);
            $table->string('power_state', 32)->nullable()->default('running');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->text('log')->nullable();

            // Composite index để tối ưu scan từng instance trong khoảng thời gian
            $table->index(['instance_id', 'timestamp_minute']);
            // Index để query tất cả record chưa aggregate
            $table->index(['timestamp_minute', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_usages');
    }
};

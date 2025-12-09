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
        Schema::create('vps_usage_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('instance_id')->index();

            // Thời gian tổng hợp (YYYY-MM-DD HH:00:00 để dễ query)
            $table->dateTime('period_start')->index();
            $table->enum('period_type', ['hourly', 'daily', 'monthly'])->default('hourly')->index();

            // Dữ liệu tổng hợp
            $table->integer('total_records')->default(0); // Số lần scan trong khoảng thời gian
            $table->decimal('avg_price_per_minute', 18, 8)->default(0);
            $table->decimal('total_price', 18, 2)->default(0);
            $table->string('power_state', 32)->nullable();
            $table->integer('avg_number_ip_address')->nullable()->default(0);

            // Metadata
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            // Composite index cho query nhanh (chỉ định tên ngắn để tránh vượt quá 64 ký tự)
            $table->unique(['user_id', 'instance_id', 'period_start', 'period_type'], 'uniq_vps_summary');
            $table->index(['period_start', 'period_type'], 'idx_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vps_usage_summaries');
    }
};

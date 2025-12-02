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
        Schema::create('monitor_settings', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id')->nullable()->unique();
            $table->bigInteger('status')->nullable()->default(1)->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->text('log')->nullable();
            $table->string('alert_time_ranges', 64)->nullable()->default('05:30-23:00');
            $table->smallInteger('timezone')->nullable()->default(7);
            $table->dateTime('global_stop_alert_to')->nullable();
            $table->smallInteger('max_quota_node')->default(5);
            $table->string('firebase_token', 512)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_settings');
    }
};

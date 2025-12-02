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
        Schema::create('monitor_items_del', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name');
            $table->tinyInteger('enable')->nullable()->default(1)->index();
            $table->smallInteger('last_check_status')->nullable();
            $table->string('url_check', 1024)->nullable();
            $table->string('type', 32)->nullable()->index();
            $table->bigInteger('maxAlertCount')->nullable();
            $table->bigInteger('user_id')->nullable()->default(0)->index();
            $table->dateTime('created_at')->nullable()->useCurrent()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();
            $table->bigInteger('check_interval_seconds')->nullable()->default(360);
            $table->string('result_valid', 1024)->nullable();
            $table->string('result_error', 1024)->nullable();
            $table->dateTime('stopTo')->nullable();
            $table->bigInteger('pingType')->nullable()->default(1);
            $table->text('log')->nullable();
            $table->dateTime('last_check_time')->nullable()->index();
            $table->text('queuedSendStr')->nullable();
            $table->boolean('forceRestart')->nullable()->default(false);
            $table->bigInteger('count_online')->default(0);
            $table->bigInteger('count_offline')->default(0);
            $table->smallInteger('allow_alert_for_consecutive_error')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_items_del');
    }
};

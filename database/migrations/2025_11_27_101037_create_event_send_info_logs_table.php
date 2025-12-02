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
        Schema::create('event_send_info_logs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('event_user_id')->nullable()->index();
            $table->bigInteger('event_id')->nullable();
            $table->string('type', 10)->nullable()->index()->comment('email,sms
');
            $table->smallInteger('status')->nullable()->default(0)->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->mediumText('log')->nullable();
            $table->string('title_email', 1024)->nullable();
            $table->text('content')->nullable();
            $table->string('content_sms', 2048)->nullable()->index();
            $table->string('session_id', 64)->nullable()->index()->comment('Mã duy nhất, có thể là time của sms');
            $table->string('sms_unique_session', 64)->nullable()->index();
            $table->text('comment')->nullable();
            $table->string('send_or_get', 10)->nullable();
            $table->bigInteger('count_success')->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->timestamp('last_app_sms_request_to_send')->nullable();
            $table->string('done_at', 50)->nullable();
            $table->string('phone_send', 20)->nullable();
            $table->bigInteger('count_retry_send')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_send_info_logs');
    }
};

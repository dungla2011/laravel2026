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
        Schema::create('event_send_actions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->mediumText('log')->nullable();
            $table->string('type', 10)->nullable()->comment('email,sms');
            $table->bigInteger('event_id')->nullable();
            $table->boolean('done')->nullable()->default(false);
            $table->bigInteger('count_send')->nullable();
            $table->string('pusher_chanel', 64)->nullable();
            $table->string('select_content', 32)->nullable();
            $table->string('select_user_type', 32)->nullable();
            $table->text('user_email_send_override')->nullable();
            $table->timestamp('last_force_send')->nullable();
            $table->text('content_raw_send')->nullable();
            $table->text('list_uid_send_done')->nullable();
            $table->string('count_success', 20)->nullable()->default('');
            $table->timestamp('pushed_all_sms_to_queue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_send_actions');
    }
};

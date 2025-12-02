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
        Schema::create('event_and_users', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('user_event_id')->nullable()->index();
            $table->bigInteger('event_id')->nullable();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->dateTime('sent_mail_at')->nullable();
            $table->dateTime('sent_sms_at')->nullable();
            $table->dateTime('confirm_join_at')->nullable();
            $table->dateTime('deny_join_at')->nullable();
            $table->dateTime('attend_at')->nullable();
            $table->text('note')->nullable();
            $table->string('extra_info1', 256)->nullable();
            $table->string('extra_info2', 256)->nullable();
            $table->string('extra_info3', 256)->nullable();
            $table->string('extra_info4', 256)->nullable();
            $table->string('extra_info5', 256)->nullable();
            $table->bigInteger('signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_and_users');
    }
};

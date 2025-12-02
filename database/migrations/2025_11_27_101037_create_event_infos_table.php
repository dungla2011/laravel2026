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
        Schema::create('event_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('id__', 32)->nullable()->unique();
            $table->string('name', 256)->nullable();
            $table->string('name_sub', 1024)->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->bigInteger('orders')->nullable()->default(0);
            $table->text('action')->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->string('image_register', 64)->nullable();
            $table->smallInteger('opacity')->nullable()->default(80);
            $table->text('log')->nullable();
            $table->string('location', 256)->nullable();
            $table->bigInteger('number_user')->nullable();
            $table->dateTime('time_start')->nullable()->index();
            $table->dateTime('time_end')->nullable();
            $table->string('mail_title1', 512)->nullable();
            $table->text('content1')->nullable();
            $table->text('content1_en')->nullable();
            $table->text('content3')->nullable();
            $table->text('content2')->nullable();
            $table->text('content2_en')->nullable();
            $table->text('content3_en')->nullable();
            $table->string('sms_content3', 1024)->nullable();
            $table->string('sms_content1', 1024)->nullable();
            $table->string('sms_content2', 1024)->nullable();
            $table->string('sms_content1_en', 1024)->nullable();
            $table->string('sms_content2_en', 1024)->nullable();
            $table->string('sms_content3_en', 1024)->nullable();
            $table->string('attached_files_email1', 128)->nullable();
            $table->string('attached_files_email1_en', 64)->nullable();
            $table->string('attached_files_email2', 64)->nullable();
            $table->string('attached_files_email2_en', 64)->nullable();
            $table->string('attached_files_email3', 64)->nullable();
            $table->string('attached_files_email3_en', 64)->nullable();
            $table->string('files', 128)->nullable();
            $table->string('mail_title2', 512)->nullable();
            $table->string('mail_title3', 512)->nullable();
            $table->string('mail_title1_en', 512)->nullable();
            $table->string('mail_title2_en', 512)->nullable();
            $table->string('mail_title3_en', 512)->nullable();
            $table->smallInteger('require_sign')->nullable()->default(0);
            $table->smallInteger('require_sign_this_event')->nullable();
            $table->smallInteger('allow_public_reg')->nullable()->default(1);
            $table->text('reg_mail_01_vi')->nullable();
            $table->text('reg_mail_02_vi')->nullable();
            $table->text('reg_mail_01_en')->nullable();
            $table->text('reg_mail_02_en')->nullable();
            $table->string('reg_mail_title_vi1', 512)->nullable();
            $table->string('reg_mail_title_vi2', 512)->nullable();
            $table->string('reg_mail_title_en1', 512)->nullable();
            $table->string('reg_mail_title_en2', 512)->nullable();
            $table->bigInteger('department')->nullable();
            $table->timestamp('time_start_check_in')->nullable();
            $table->smallInteger('user_need_image_to_reg')->nullable()->default(0);
            $table->bigInteger('limit_max_member')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_infos');
    }
};

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
        Schema::create('event_registers', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('user_event_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->bigInteger('event_id')->nullable()->index();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->string('organization')->nullable();
            $table->string('note', 10240)->nullable();
            $table->string('email', 256)->nullable()->index();
            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();
            $table->string('reg_code', 128)->nullable()->index();
            $table->timestamp('reg_confirm_time')->nullable();
            $table->string('lang', 4)->nullable();
            $table->smallInteger('gender')->nullable();
            $table->string('designation', 128)->nullable();
            $table->text('content_mail1')->nullable();
            $table->text('content_mail2')->nullable();
            $table->string('sub_event_list')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registers');
    }
};

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
        Schema::create('crm_messages', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->tinyInteger('type')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->bigInteger('msg_id')->nullable()->index();
            $table->string('cli_msg_id', 50)->nullable();
            $table->string('action_id', 50)->nullable();
            $table->string('msg_type', 20)->nullable();
            $table->string('uid_from', 50)->nullable()->index();
            $table->string('id_to', 50)->nullable()->index();
            $table->string('d_name')->nullable();
            $table->bigInteger('ts')->nullable()->index();
            $table->text('content')->nullable();
            $table->tinyInteger('notify')->nullable();
            $table->bigInteger('ttl')->nullable();
            $table->string('uin', 50)->nullable();
            $table->string('user_id_ext', 50)->nullable();
            $table->bigInteger('cmd')->nullable();
            $table->bigInteger('st')->nullable();
            $table->bigInteger('at')->nullable();
            $table->string('real_msg_id', 50)->nullable();
            $table->string('thread_id', 50)->nullable()->index();
            $table->boolean('is_self')->nullable();
            $table->json('property_ext')->nullable();
            $table->json('params_ext')->nullable();
            $table->string('channel_name', 32)->nullable()->index();
            $table->bigInteger('user_id_remote')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_messages');
    }
};

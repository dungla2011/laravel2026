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
        Schema::create('zalo_messages', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->dateTime('timestamp')->index();
            $table->string('account', 50)->index();
            $table->string('direction', 20);
            $table->string('type', 20);
            $table->string('thread_id', 100)->index();
            $table->text('content')->nullable();
            $table->string('action_id', 50)->nullable();
            $table->string('msg_id', 50)->nullable()->index();
            $table->string('cli_msg_id', 50)->nullable();
            $table->string('msg_type', 20)->nullable();
            $table->string('uid_from', 100)->nullable()->index();
            $table->string('id_to', 100)->nullable();
            $table->string('d_name', 100)->nullable();
            $table->bigInteger('ts')->nullable();
            $table->bigInteger('status')->nullable();
            $table->string('notify', 10)->nullable();
            $table->bigInteger('ttl')->nullable();
            $table->string('user_id_ext', 100)->nullable();
            $table->string('uin', 100)->nullable();
            $table->bigInteger('cmd')->nullable();
            $table->bigInteger('st')->nullable();
            $table->bigInteger('at')->nullable();
            $table->string('real_msg_id', 50)->nullable();
            $table->boolean('is_self')->nullable()->default(false);
            $table->string('user_id', 100)->nullable()->index();
            $table->json('property_ext')->nullable();
            $table->json('params_ext')->nullable();
            $table->string('channel_name', 100)->nullable()->index();
            $table->text('log')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['msg_id', 'account'], 'unique_msg_account');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zalo_messages');
    }
};

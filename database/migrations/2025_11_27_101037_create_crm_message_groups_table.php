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
        Schema::create('crm_message_groups', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('gid')->nullable()->unique();
            $table->string('avatar', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->string('link_group', 256)->nullable();
            $table->string('channel_name', 64)->nullable();
            $table->mediumText('full_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_message_groups');
    }
};

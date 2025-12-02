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
        Schema::create('conference_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->string('sub_title', 256)->nullable();
            $table->text('summary')->nullable();
            $table->string('images', 128)->nullable();
            $table->smallInteger('cat')->nullable();
            $table->text('key_notes')->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->default(1)->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->text('conf1_video')->nullable();
            $table->string('conf1_images', 1024)->nullable();
            $table->string('conf1_image_title', 256)->nullable();
            $table->text('conf1_timesheet')->nullable();
            $table->string('conf1_keynote', 1024)->nullable();
            $table->string('conf1_name', 256)->nullable();
            $table->string('conf2_name', 256)->nullable();
            $table->string('conf2_keynote', 1024)->nullable();
            $table->text('conf2_timesheet')->nullable();
            $table->string('conf2_images', 1024)->nullable();
            $table->string('conf2_image_title', 128)->nullable();
            $table->text('conf2_video')->nullable();
            $table->text('conf3_video')->nullable();
            $table->string('conf3_images', 1024)->nullable();
            $table->string('conf3_image_title', 128)->nullable();
            $table->text('conf3_timesheet')->nullable();
            $table->string('conf3_keynote', 1024)->nullable();
            $table->string('conf3_name', 256)->nullable();
            $table->string('video_bottom', 1024)->nullable();
            $table->string('supporters', 128)->nullable();
            $table->text('right_column')->nullable();
            $table->bigInteger('orders')->nullable()->default(0)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_infos');
    }
};

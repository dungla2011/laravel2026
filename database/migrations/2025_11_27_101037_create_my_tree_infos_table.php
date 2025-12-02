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
        Schema::create('my_tree_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('id__', 32)->nullable()->unique();
            $table->string('name', 256);
            $table->string('title', 256)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes()->index();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('tree_id')->nullable()->unique();
            $table->bigInteger('old_tree_id')->nullable()->index();
            $table->tinyInteger('status')->nullable();
            $table->string('image_list', 64)->nullable();
            $table->string('old_image_list', 64)->nullable()->index();
            $table->string('color_name', 12)->nullable();
            $table->string('color_title', 12)->nullable();
            $table->smallInteger('fontsize_name')->nullable();
            $table->smallInteger('fontsize_title')->nullable();
            $table->smallInteger('banner_name_margin_top')->nullable()->default(0);
            $table->smallInteger('banner_name_margin_bottom')->nullable()->default(0);
            $table->smallInteger('banner_title_margin_top')->nullable()->default(0);
            $table->smallInteger('banner_title_margin_bottom')->nullable()->default(0);
            $table->string('member_background_img', 256)->nullable();
            $table->string('member_background_img2', 256)->nullable();
            $table->mediumInteger('banner_width')->nullable();
            $table->mediumInteger('banner_height')->nullable();
            $table->string('banner_name_bold', 20)->nullable();
            $table->string('banner_name_italic', 20)->nullable();
            $table->string('banner_title_bold', 20)->nullable();
            $table->string('banner_title_italic', 20)->nullable();
            $table->mediumInteger('banner_title_curver')->nullable();
            $table->mediumInteger('banner_name_curver')->nullable();
            $table->string('banner_text_shadow_name', 30)->nullable();
            $table->string('banner_text_shadow_title', 30)->nullable();
            $table->smallInteger('banner_margin_top')->nullable();
            $table->tinyInteger('title_before_or_after_name')->nullable()->default(0);
            $table->mediumText('tree_nodes_xy')->nullable()->comment('các tọa độ từng node thuộc cây này được lưu riêng tại đây');
            $table->mediumText('old_tree_nodes_xy')->nullable()->index()->comment('các tọa độ từng node thuộc cây này được lưu riêng tại đây');
            $table->bigInteger('minX')->nullable();
            $table->bigInteger('minY')->nullable();
            $table->tinyInteger('show_node_name_one')->nullable()->default(1);
            $table->tinyInteger('show_node_title')->nullable()->default(1);
            $table->tinyInteger('show_node_birthday_one')->nullable()->default(1);
            $table->tinyInteger('show_node_date_of_death')->nullable()->default(1);
            $table->tinyInteger('show_node_image')->nullable()->default(1);
            $table->smallInteger('node_width')->nullable();
            $table->smallInteger('node_height')->nullable();
            $table->smallInteger('space_node_x')->nullable();
            $table->smallInteger('space_node_y')->nullable();
            $table->smallInteger('font_size_node')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_tree_infos');
    }
};

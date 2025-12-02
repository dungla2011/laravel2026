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
        Schema::create('gia_phas', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('id__', 32)->nullable()->unique();
            $table->bigInteger('parent_id')->nullable()->default(0)->index();
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->timestamp('created_at')->nullable()->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes()->index();
            $table->string('name', 256);
            $table->string('title', 64)->nullable();
            $table->string('home_address', 64)->nullable();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('orders')->nullable()->default(0)->index();
            $table->smallInteger('child_type')->nullable()->comment('=1 là con dâu, rể');
            $table->smallInteger('gender')->nullable()->default(1);
            $table->string('birthday', 30)->nullable();
            $table->string('date_of_death', 32)->nullable();
            $table->string('place_birthday', 64)->nullable();
            $table->string('place_heaven', 64)->nullable();
            $table->bigInteger('child_of_second_married')->nullable()->index();
            $table->bigInteger('old_child_of_second_married')->nullable()->index();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('last_name', 128)->nullable();
            $table->string('sur_name', 128)->nullable();
            $table->bigInteger('married_with')->nullable()->index();
            $table->bigInteger('old_married_with')->nullable()->index();
            $table->string('image_list', 1024)->nullable();
            $table->string('old_image_list', 1024)->nullable()->index();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('tmp_old_id')->nullable()->index();
            $table->bigInteger('tmp_old_pid')->nullable();
            $table->text('tmp_old_obj_json')->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('email_address', 64)->nullable();
            $table->mediumInteger('col_fix')->nullable();
            $table->mediumInteger('row_fix')->nullable();
            $table->text('link_remote')->nullable();
            $table->boolean('set_nu_dinh')->nullable();
            $table->mediumText('list_child_x_y')->nullable()->comment('vị trí x,y của từng lá, all lá con của cây
, để không phải sắp xếp tự động gây chậm');
            $table->mediumText('old_list_child_x_y')->nullable()->index()->comment('vị trí x,y của từng lá, all lá con của cây
, để không phải sắp xếp tự động gây chậm');
            $table->bigInteger('stepchild_of')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gia_phas');
    }
};

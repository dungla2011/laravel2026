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
        Schema::create('event_user_infos', function (Blueprint $table) {
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
            $table->text('log')->nullable();
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->string('parent_extra', 256)->nullable();
            $table->string('parent_all', 256)->nullable();
            $table->string('old_parent_all', 256)->nullable()->index();
            $table->string('title', 64)->nullable();
            $table->string('first_name', 64)->nullable();
            $table->string('last_name', 64)->nullable();
            $table->string('email', 64)->index();
            $table->string('phone', 16)->nullable()->index();
            $table->string('address', 128)->nullable();
            $table->text('organization')->nullable();
            $table->string('designation', 128)->nullable();
            $table->string('language', 3)->nullable()->default('vi')->comment('vi/en/fr....');
            $table->string('extra_info1', 1024)->nullable();
            $table->string('extra_info2', 1024)->nullable();
            $table->string('extra_info3', 1024)->nullable();
            $table->string('extra_info4', 256)->nullable();
            $table->string('extra_info5', 256)->nullable();
            $table->string('signature', 128)->nullable();
            $table->string('note', 512)->nullable();
            $table->smallInteger('gender')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_user_infos');
    }
};

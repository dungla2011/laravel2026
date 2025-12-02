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
        Schema::create('news', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256);
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->text('log')->nullable();
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->text('summary')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->smallInteger('status')->nullable()->default(0)->index();
            $table->text('meta_desc')->nullable();
            $table->bigInteger('options')->nullable()->index();
            $table->bigInteger('orders')->nullable()->index();
            $table->bigInteger('publish_status')->nullable();
            $table->bigInteger('count_view')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

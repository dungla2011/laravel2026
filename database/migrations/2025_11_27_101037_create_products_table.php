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
        Schema::create('products', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256);
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamp('created_at')->nullable()->useCurrent()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->smallInteger('status')->nullable()->index();
            $table->text('meta_desc')->nullable();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('price1')->nullable();
            $table->bigInteger('param1')->nullable();
            $table->bigInteger('param2')->nullable();
            $table->bigInteger('param3')->nullable();
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->string('parent_extra')->nullable();
            $table->string('parent_all')->nullable();
            $table->string('old_parent_all')->nullable()->index();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->smallInteger('orders')->nullable()->index();
            $table->text('meta')->nullable();
            $table->text('refer')->nullable();
            $table->text('tmp')->nullable();
            $table->string('type', 128)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

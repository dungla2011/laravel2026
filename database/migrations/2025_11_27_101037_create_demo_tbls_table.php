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
        Schema::create('demo_tbls', function (Blueprint $table) {
            $table->softDeletes()->index();
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->timestamps();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('number1')->nullable();
            $table->bigInteger('number2')->nullable();
            $table->string('string1')->nullable();
            $table->string('string2')->nullable();
            $table->string('textarea1')->nullable();
            $table->string('textarea2')->nullable();
            $table->text('tag_list_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->bigInteger('parent2')->nullable();
            $table->text('parent_multi')->nullable();
            $table->text('parent_multi2')->nullable();
            $table->text('image_list1')->nullable();
            $table->text('image_list2')->nullable();
            $table->bigInteger('orders')->nullable()->default(0);
            $table->string('name', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_tbls');
    }
};

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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->text('answer')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->tinyInteger('is_english')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes()->index();
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->text('content_vi')->nullable();
            $table->text('content_textarea')->nullable();
            $table->text('draft')->nullable();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('note')->nullable();
            $table->string('note_book', 64)->nullable();
            $table->smallInteger('type')->nullable();
            $table->text('explains')->nullable();
            $table->smallInteger('hard_level')->nullable();
            $table->string('class', 64)->nullable();
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->string('parent_list')->nullable();
            $table->string('old_parent_list')->nullable()->index();
            $table->string('refer')->nullable()->index();
            $table->text('tmp')->nullable();
            $table->text('obj_refer')->nullable();
            $table->text('log')->nullable();
            $table->bigInteger('cat1')->nullable();
            $table->bigInteger('cat2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

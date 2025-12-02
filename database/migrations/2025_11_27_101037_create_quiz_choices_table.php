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
        Schema::create('quiz_choices', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->string('value', 256)->nullable();
            $table->text('value_richtext')->nullable();
            $table->bigInteger('question_id')->nullable()->index();
            $table->tinyInteger('is_right_choice')->nullable();
            $table->text('choice')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->text('image_list')->nullable();
            $table->text('old_image_list')->nullable()->index();
            $table->text('note')->nullable();
            $table->smallInteger('orders')->nullable();
            $table->tinyInteger('enable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_choices');
    }
};

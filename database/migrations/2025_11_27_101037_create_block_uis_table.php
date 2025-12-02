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
        Schema::create('block_uis', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name_bak')->nullable();
            $table->longText('name')->nullable();
            $table->string('sname', 128)->nullable()->unique();
            $table->text('summary_bak')->nullable();
            $table->json('summary')->nullable();
            $table->text('summary2')->nullable();
            $table->string('module_table')->nullable();
            $table->string('idModule', 1024)->nullable();
            $table->softDeletes();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->text('log')->nullable();
            $table->bigInteger('siteid')->nullable();
            $table->text('extra_info_bak')->nullable();
            $table->json('extra_info')->nullable();
            $table->string('image_list', 1024)->nullable();
            $table->string('old_image_list', 1024)->nullable()->index();
            $table->string('tags_list', 1024)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->smallInteger('status')->nullable()->default(1);
            $table->text('content')->nullable();
            $table->text('guide_admin')->nullable();
            $table->string('extra_color_background', 10)->nullable();
            $table->string('extra_color_text', 10)->nullable();
            $table->string('group_name', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_uis');
    }
};

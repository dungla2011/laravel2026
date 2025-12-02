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
        Schema::create('file_refers', function (Blueprint $table) {
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
            $table->string('site', 8)->nullable()->index()->comment('tên, ví dụ 4s, fs, gg,...');
            $table->bigInteger('remote_id')->nullable()->index();
            $table->string('remote_url', 256)->nullable()->unique();
            $table->bigInteger('filesize')->nullable();
            $table->string('param1', 64)->nullable();
            $table->string('param2', 64)->nullable();
            $table->text('refer_obj')->nullable();
            $table->mediumInteger('price_k')->nullable();
            $table->bigInteger('count_dl')->nullable()->index();
            $table->smallInteger('make_torrent')->nullable();

            $table->unique(['remote_id', 'site'], 'remote_id_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_refers');
    }
};

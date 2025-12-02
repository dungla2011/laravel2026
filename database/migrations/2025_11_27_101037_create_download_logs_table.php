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
        Schema::create('download_logs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->text('log')->nullable();
            $table->double('sid_download')->nullable()->index();
            $table->bigInteger('file_refer_id')->nullable();
            $table->bigInteger('file_id')->nullable()->index();
            $table->string('file_id_enc', 64)->nullable()->index();
            $table->string('filename', 256)->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('ip_request', 64)->nullable();
            $table->string('ip_download_done', 64)->nullable();
            $table->timestamp('time_download_done')->nullable()->index();
            $table->bigInteger('count_dl')->nullable()->default(0)->index();
            $table->string('sid_encode', 64)->nullable();
            $table->bigInteger('price_k')->nullable();
            $table->bigInteger('user_id_file')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_logs');
    }
};

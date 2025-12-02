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
        Schema::create('tmp_download_sessions', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->string('token')->nullable()->index();
            $table->bigInteger('fid')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->bigInteger('done_bytes')->nullable()->default(0);
            $table->string('ip_address', 64)->nullable();
            $table->string('ip_download_list', 4096)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->softDeletes()->index();
            $table->timestamp('time_begin_update_byte')->nullable();
            $table->timestamp('time_end_update_byte')->nullable();
            $table->string('logs', 4096)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmp_download_sessions');
    }
};

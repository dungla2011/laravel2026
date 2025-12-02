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
        Schema::create('user_clouds', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id')->unique();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('quota_size')->nullable();
            $table->bigInteger('quota_file')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes()->index();
            $table->timestamp('created_at')->nullable();
            $table->string('location_store_file', 256)->nullable();
            $table->bigInteger('glx_bytes_in_used')->nullable()->default(0);
            $table->bigInteger('glx_files_in_used')->nullable()->default(0);
            $table->bigInteger('quota_daily_download')->nullable();
            $table->bigInteger('quota_limit_data')->nullable();
            $table->text('glx_download_his')->nullable();
            $table->string('glx_shell', 50)->nullable()->default('/sbin/nologin');
            $table->bigInteger('glx_uid')->nullable()->default(48);
            $table->bigInteger('glx_gid')->nullable()->default(48);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_clouds');
    }
};

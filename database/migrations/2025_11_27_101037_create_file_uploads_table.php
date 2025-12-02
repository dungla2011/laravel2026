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
        Schema::create('file_uploads', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('id__', 32)->nullable()->unique();
            $table->string('name', 256);
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->softDeletes()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->string('file_path', 512)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->text('log')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0)->index();
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->bigInteger('cloud_id')->nullable()->index();
            $table->bigInteger('old_cloud_id')->nullable()->index();
            $table->string('md5', 32)->nullable()->index();
            $table->string('crc32', 16)->nullable()->index();
            $table->text('comment')->nullable();
            $table->string('mime', 128)->nullable();
            $table->string('refer', 256)->nullable()->index();
            $table->bigInteger('count_download')->nullable()->default(0);
            $table->bigInteger('idlink')->nullable();
            $table->string('checksum', 32)->nullable();
            $table->string('link1', 32)->nullable()->index();
            $table->string('ip_upload', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_uploads');
    }
};

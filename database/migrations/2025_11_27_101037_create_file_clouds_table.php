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
        Schema::create('file_clouds', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 512);
            $table->bigInteger('size')->nullable();
            $table->string('file_path', 256)->nullable();
            $table->string('md5', 32)->nullable()->index();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->string('crc32', 16)->nullable()->index();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('location', 256)->nullable();
            $table->string('mime', 128)->nullable();
            $table->string('server1', 128)->nullable();
            $table->string('location1', 128)->nullable();
            $table->string('checksum', 64)->nullable();
            $table->text('log')->nullable();
            $table->timestamp('last_save_doc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_clouds');
    }
};

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
        Schema::create('cloud_servers', function (Blueprint $table) {
            $table->bigInteger('id', true)->index();
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 100)->default('')->unique();
            $table->string('domain', 100)->unique();
            $table->string('proxy_domain', 100)->nullable()->default('');
            $table->text('mount_list');
            $table->text('mount_list_disable_rep')->nullable();
            $table->tinyInteger('replicate_now')->default(0);
            $table->tinyInteger('iscache')->nullable();
            $table->text('comment')->nullable();
            $table->smallInteger('enable')->nullable()->default(0);
            $table->bigInteger('file_service_port')->nullable()->default(16868);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_servers');
    }
};

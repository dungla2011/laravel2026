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
        Schema::create('gia_pha_users', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id')->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->timestamp('created_at')->nullable();
            $table->softDeletes()->index();
            $table->timestamp('updated_at')->nullable();
            $table->bigInteger('max_quota_node')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->bigInteger('version_using')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gia_pha_users');
    }
};

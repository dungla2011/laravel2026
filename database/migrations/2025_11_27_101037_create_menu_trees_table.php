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
        Schema::create('menu_trees', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('old_parent_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->bigInteger('orders')->nullable()->default(0);
            $table->string('link', 512)->nullable()->default('');
            $table->string('gid_allow')->nullable();
            $table->tinyInteger('open_new_window')->nullable()->default(0);
            $table->string('icon', 256)->nullable();
            $table->bigInteger('id_news')->nullable()->index();
            $table->json('translations')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_trees');
    }
};

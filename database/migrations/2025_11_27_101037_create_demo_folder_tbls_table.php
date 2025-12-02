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
        Schema::create('demo_folder_tbls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->string('parent_id')->nullable()->default('0');
            $table->string('old_parent_id')->nullable()->default('0')->index();
            $table->string('summary')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('log')->nullable();
            $table->bigInteger('orders')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_folder_tbls');
    }
};

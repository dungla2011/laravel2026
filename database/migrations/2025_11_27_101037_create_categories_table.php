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
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->string('slug')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

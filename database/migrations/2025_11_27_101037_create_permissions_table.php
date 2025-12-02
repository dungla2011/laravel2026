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
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->string('route_name_code')->nullable()->unique();
            $table->string('display_name')->nullable();
            $table->timestamps();
            $table->bigInteger('parent_id')->default(0);
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->string('prefix');
            $table->string('url', 512)->nullable();
            $table->bigInteger('site_id')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

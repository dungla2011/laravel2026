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
        Schema::create('money_logs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('price');
            $table->timestamps();
            $table->softDeletes();
            $table->text('log')->nullable();
            $table->string('cat', 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_logs');
    }
};

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
        Schema::create('assets', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('id__', 32)->nullable();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->text('log')->nullable();
            $table->string('barcode')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('location_id')->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->decimal('value', 10, 0)->nullable();
            $table->bigInteger('orders')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

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
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes()->index();
            $table->bigInteger('order_id')->nullable()->index();
            $table->bigInteger('old_order_id')->nullable()->index();
            $table->bigInteger('sku_id')->nullable();
            $table->string('sku_string', 256)->nullable();
            $table->bigInteger('product_id')->nullable()->index();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('price_org')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->string('client_session_time', 16)->nullable();
            $table->timestamp('end_time')->nullable();
            $table->bigInteger('param1')->nullable();
            $table->bigInteger('used')->nullable()->default(0);
            $table->text('log')->nullable();
            $table->string('note', 1024)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

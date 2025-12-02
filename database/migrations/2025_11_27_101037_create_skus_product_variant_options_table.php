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
        Schema::create('skus_product_variant_options', function (Blueprint $table) {
            $table->bigInteger('sku_id');
            $table->bigInteger('product_variant_id')->index();
            $table->bigInteger('product_variant_options_id')->index();
            $table->bigInteger('id')->nullable()->index();
            $table->bigInteger('old_id')->nullable()->index();
            $table->softDeletes();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->primary(['sku_id', 'product_variant_options_id', 'product_variant_id']);
            $table->unique(['sku_id', 'product_variant_id'], 'unique_sku_id_product_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skus_product_variant_options');
    }
};

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
        Schema::create('partner_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->string('name', 128)->nullable()->comment('Tên thông tin');
            $table->softDeletes();
            $table->string('partner_name', 64)->nullable()->default('0')->comment('Tên parnet');
            $table->string('token_api')->nullable()->comment('Số tiền');
            $table->text('note')->nullable()->comment('Mô tả');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_infos');
    }
};

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
        Schema::create('transport_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable()->comment('Tên chuyến');
            $table->string('from_address', 256)->nullable()->comment('Đi từ');
            $table->string('to_address', 256)->nullable()->comment('Đi đến');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->bigInteger('user_id')->nullable()->default(0);
            $table->bigInteger('old_user_id')->nullable()->default(0)->index();
            $table->string('phone_request', 30)->nullable()->comment('phone khách nếu có');
            $table->string('email_request', 50)->nullable()->comment('email khách nếu có');
            $table->text('text_desc')->nullable()->comment('Mô tả text , copy từ chat..., voice');
            $table->bigInteger('user_id_post')->nullable();
            $table->bigInteger('user_id_get')->nullable();
            $table->bigInteger('service_require')->nullable();
            $table->timestamp('start_time')->nullable()->comment('Thời gian bắt đầu cần dịch vụ');
            $table->timestamp('end_time')->nullable();
            $table->bigInteger('money')->nullable();
            $table->timestamp('done_at')->nullable()->comment('Thành công');
            $table->smallInteger('status')->nullable()->comment('Trạng thái: thành công, hủy...');
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_infos');
    }
};

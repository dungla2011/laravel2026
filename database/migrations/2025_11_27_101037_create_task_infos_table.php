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
        Schema::create('task_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('user_id');
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->string('name');
            $table->text('description')->nullable()->comment('Mô tả chi tiết Task');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'pending', 'canceled'])->nullable()->default('not_started');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->nullable()->default('medium');
            $table->date('due_date')->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->dateTime('deleted_at')->nullable();
            $table->bigInteger('assigned_to')->nullable();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->bigInteger('old_parent_id')->nullable()->default(0)->index();
            $table->bigInteger('orders')->nullable();
            $table->string('file_list')->nullable()->comment('là các ID file, cách nhau bởi dấu , Link các file này sẽ có hàm lấy sau');
            $table->string('parent_extra')->nullable();
            $table->string('parent_all')->nullable();
            $table->string('old_parent_all')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_infos');
    }
};

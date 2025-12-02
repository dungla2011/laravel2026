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
        Schema::create('event_face_infos', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->string('name', 256)->nullable();
            $table->bigInteger('user_id')->nullable()->index();
            $table->bigInteger('old_user_id')->nullable()->index();
            $table->bigInteger('user_event_id')->nullable();
            $table->bigInteger('status')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
            $table->string('image_list', 256)->nullable();
            $table->string('old_image_list', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->string('extra_info', 1024)->nullable();
            $table->text('face_vector')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_face_infos');
    }
};

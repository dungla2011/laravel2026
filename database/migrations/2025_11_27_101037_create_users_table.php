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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->string('id__', 32)->nullable()->unique();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->unique();
            $table->bigInteger('phone_number')->nullable();
            $table->timestamps();
            $table->bigInteger('is_admin')->nullable()->default(0);
            $table->softDeletes();
            $table->string('token_user')->nullable()->index();
            $table->bigInteger('site_id')->nullable()->default(0);
            $table->string('name', 128)->nullable();
            $table->rememberToken();
            $table->timestamp('email_active_at')->nullable();
            $table->string('reg_str', 256)->nullable()->index();
            $table->text('log')->nullable();
            $table->string('reset_pw', 128)->nullable()->index();
            $table->string('avatar', 128)->nullable();
            $table->string('language', 3)->nullable()->default('en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

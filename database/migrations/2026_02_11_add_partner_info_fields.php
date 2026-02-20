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
        Schema::table('partner_infos', function (Blueprint $table) {
            // Add tax code (mã số thuế)
            if (!Schema::hasColumn('partner_infos', 'tax_code')) {
                $table->string('tax_code', 64)->nullable()->comment('Mã số thuế');
            }
            
            // Add address (địa chỉ)
            if (!Schema::hasColumn('partner_infos', 'address')) {
                $table->text('address')->nullable()->comment('Địa chỉ');
            }
            
            // Add phone (điện thoại)
            if (!Schema::hasColumn('partner_infos', 'phone')) {
                $table->string('phone', 20)->nullable()->comment('Điện thoại');
            }
            
            // Add email
            if (!Schema::hasColumn('partner_infos', 'email')) {
                $table->string('email')->nullable()->unique()->comment('Email');
            }
            
            // Add user_id (bigint)
            if (!Schema::hasColumn('partner_infos', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->comment('User ID');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_infos', function (Blueprint $table) {
            // Drop foreign key if it exists
            if (Schema::hasColumn('partner_infos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            // Drop other columns
            $table->dropColumnIfExists('tax_code');
            $table->dropColumnIfExists('address');
            $table->dropColumnIfExists('phone');
            $table->dropColumnIfExists('email');
        });
    }
};

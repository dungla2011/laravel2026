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
        Schema::create('user_balances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->decimal('balance', 15)->default(0);
            $table->decimal('total_recharged', 15)->default(0);
            $table->decimal('total_spent', 15)->default(0);
            $table->tinyInteger('status')->default(1)->index();
            $table->boolean('is_frozen')->default(false)->index();
            $table->string('frozen_reason')->nullable();
            $table->decimal('low_balance_threshold', 15)->default(10000);
            $table->timestamp('last_low_balance_alert')->nullable();
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_balances');
    }
};

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
        Schema::create('user_balance_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->index();
            $table->string('transaction_type')->index();
            $table->string('service_type')->nullable()->index();
            $table->string('reference_model')->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->bigInteger('related_recharge_id')->nullable();
            $table->decimal('amount', 15);
            $table->decimal('balance_before', 15);
            $table->decimal('balance_after', 15);
            $table->text('description')->nullable();
            $table->string('status')->default('completed')->index();
            $table->boolean('is_reversed')->default(false);
            $table->timestamp('reversed_at')->nullable();
            $table->string('reversed_reason')->nullable();
            $table->timestamp('transaction_date')->useCurrent()->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();

            $table->index(['reference_model', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_balance_transactions');
    }
};

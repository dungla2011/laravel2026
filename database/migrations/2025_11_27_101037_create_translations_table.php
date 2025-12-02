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
        Schema::create('translations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('language_code', 10)->index()->comment('FK to languages.code');
            $table->string('translation_key')->index()->comment('Translation key (e.g., appTitle)');
            $table->text('translation_value')->comment('Translated text');
            $table->boolean('is_active')->nullable()->default(true)->index()->comment('1 = active, 0 = inactive');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['language_code', 'translation_key'], 'unique_translation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};

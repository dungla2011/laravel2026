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
        Schema::table('demo_and_tag_tbls', function (Blueprint $table) {
            $table->foreign(['demo_id'], 'tag_id_and_demo_id_demo_id_foreign')->references(['id'])->on('demo_tbls')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['tag_id'], 'tag_id_and_demo_id_tag_id_foreign')->references(['id'])->on('tag_demos')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demo_and_tag_tbls', function (Blueprint $table) {
            $table->dropForeign('tag_id_and_demo_id_demo_id_foreign');
            $table->dropForeign('tag_id_and_demo_id_tag_id_foreign');
        });
    }
};

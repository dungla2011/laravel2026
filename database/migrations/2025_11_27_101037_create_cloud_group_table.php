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
        Schema::create('cloud_group', function (Blueprint $table) {
            $table->comment('Galaxy group table');
            $table->string('groupname', 16)->default('')->index();
            $table->smallInteger('gid')->default(5501);
            $table->string('members', 16)->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloud_group');
    }
};

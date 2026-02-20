<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            if (!Schema::hasColumn('vps_usages', 'image_list')) {
                $table->string('image_list', 512)->nullable()->comment('List of images (512 chars max)');
            }
        });

        Schema::table('vps_instances', function (Blueprint $table) {
            if (!Schema::hasColumn('vps_instances', 'image_list')) {
                $table->string('image_list', 512)->nullable()->comment('List of images (512 chars max)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vps_usages', function (Blueprint $table) {
            if (Schema::hasColumn('vps_usages', 'image_list')) {
                $table->dropColumn('image_list');
            }
        });

        Schema::table('vps_instances', function (Blueprint $table) {
            if (Schema::hasColumn('vps_instances', 'image_list')) {
                $table->dropColumn('image_list');
            }
        });
    }
};

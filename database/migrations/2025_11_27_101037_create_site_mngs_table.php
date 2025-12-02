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
        Schema::create('site_mngs', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('old_id')->nullable()->index();
            $table->bigInteger('userid')->nullable();
            $table->string('domain', 128)->nullable()->unique();
            $table->string('domain1', 100)->nullable()->index();
            $table->string('domain2', 100)->nullable()->index();
            $table->string('domain3', 50)->nullable();
            $table->string('domain4', 50)->nullable();
            $table->string('domain5', 50)->nullable();
            $table->string('templateName', 100)->nullable()->default('default_news');
            $table->string('MEMBER_APP_NAME', 50)->nullable()->default('DEFAULT NAME');
            $table->string('logo_image')->nullable();
            $table->string('logo_image2', 1024)->nullable();
            $table->string('logo_image3', 1024)->nullable();
            $table->string('logo_text', 30)->nullable();
            $table->string('color1', 12)->nullable();
            $table->string('color2', 12)->nullable();
            $table->string('color3', 12)->nullable();
            $table->text('metaTitle')->nullable();
            $table->text('metaTitleEn')->nullable();
            $table->text('metaDescription')->nullable();
            $table->text('metaKeyword')->nullable();
            $table->text('metaHeader')->nullable();
            $table->string('FACEBOOK_APP_ID', 200)->nullable()->default('1842535416029056');
            $table->string('FACEBOOK_APP_SECRET', 200)->nullable()->default('639330ee1b76aea59a5edab7ed3e171e');
            $table->string('GOOGLE_OAUTH2_CLIENT_ID', 200)->nullable()->default('211733424826-d7dns77hrghn70tugmlbo7p15ugfed4m.apps.googleusercontent.com');
            $table->string('GOOGLE_OAUTH2_CLIENT_SECRET', 200)->nullable()->default('BxHtGddkUIqQKPyEb957AjvY');
            $table->string('GOOGLE_SITE_VERIFICATION_CODE', 256)->nullable();
            $table->text('google_analytics_code')->nullable();
            $table->string('language', 3)->nullable();
            $table->bigInteger('siteid')->nullable();
            $table->string('admin_email', 128)->nullable();
            $table->string('admin_phone_support', 20)->nullable();
            $table->string('admin_name')->nullable();
            $table->string('address1', 1024)->nullable();
            $table->text('address2')->nullable();
            $table->bigInteger('cache_time_minute')->nullable()->default(0);
            $table->bigInteger('cache_disable_to_time')->nullable()->default(0);
            $table->smallInteger('useMongo')->default(0);
            $table->string('not_found_image_default', 512)->nullable();
            $table->string('facebook_message_appid', 30)->nullable();
            $table->string('facebook_message_link')->nullable();
            $table->string('og_image_default')->nullable();
            $table->text('maintain_text')->nullable();
            $table->text('remarketting')->nullable();
            $table->text('livechat')->nullable();
            $table->text('facebook_pixel')->nullable();
            $table->text('google_analytics_code2')->nullable();
            $table->string('metaTitle_en')->nullable();
            $table->string('metaTitle_jp')->nullable();
            $table->string('metaDescription_en', 512)->nullable();
            $table->string('metaDescription_jp', 512)->nullable();
            $table->string('metaKeyword_en', 512)->nullable();
            $table->string('metaKeyword_jp', 512)->nullable();
            $table->smallInteger('encode_id1')->nullable();
            $table->smallInteger('encode_id2')->nullable();
            $table->bigInteger('useMetaReserveOfData')->nullable();
            $table->bigInteger('useMetaReserveOfNews')->nullable();
            $table->bigInteger('maxSizeUploadWebMB')->nullable()->default(10);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
            $table->bigInteger('deleted_at')->nullable();
            $table->string('site_code', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_mngs');
    }
};

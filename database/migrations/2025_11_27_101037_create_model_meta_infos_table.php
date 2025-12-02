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
        Schema::create('model_meta_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('table_name_model')->index();
            $table->string('field')->nullable()->index();
            $table->string('sname')->nullable();
            $table->string('name')->nullable();
            $table->string('full_desc')->nullable();
            $table->bigInteger('order_field')->nullable()->default(0);
            $table->bigInteger('dataType')->nullable();
            $table->bigInteger('is_hiden_input')->nullable();
            $table->string('show_in_index')->nullable();
            $table->string('show_get_one')->nullable();
            $table->string('searchable')->nullable();
            $table->string('sortable')->nullable();
            $table->string('editable')->nullable();
            $table->string('editable_get_one')->nullable();
            $table->tinyInteger('show_index_mobile')->nullable()->default(0);
            $table->string('readOnly')->nullable();
            $table->string('limit_user_edit')->nullable();
            $table->string('limit_dev_edit')->nullable();
            $table->string('insertable')->nullable();
            $table->string('join_func')->nullable();
            $table->string('join_api')->nullable();
            $table->string('join_api_field')->nullable();
            $table->string('admin_url')->nullable();
            $table->string('func_foreign_key_insert_update')->nullable();
            $table->string('is_select')->nullable();
            $table->string('css_class')->nullable();
            $table->string('css_cell_class')->nullable();
            $table->string('css')->nullable();
            $table->string('link_to_view')->nullable();
            $table->string('link_to_edit')->nullable();
            $table->string('primary')->nullable();
            $table->string('is_multilangg')->nullable();
            $table->string('get_not_show', 50)->nullable();
            $table->string('join_relation_func')->nullable();
            $table->string('data_type_in_db', 128)->nullable();
            $table->bigInteger('opt_field')->nullable();
            $table->string('join_func_model', 128)->nullable();
            $table->mediumInteger('width_col')->nullable();
            $table->json('translations')->nullable();

            $table->unique(['table_name_model', 'field'], 'table_name_model_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_meta_infos');
    }
};

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                // $table->string('name');
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->uuid('deleted_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('created_by')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('restrict');
                $table->foreign('updated_by')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('restrict');
                $table->foreign('deleted_by')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
}

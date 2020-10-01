<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('product_category_id');
                $table->string('name');
                $table->string('slug');
                $table->string('image');
                $table->longText('description');
                $table->mediumText('short_description')->nullable();
                $table->boolean('on_sale')->default(false);
                $table->integer('price');
                $table->integer('sale_price')->default(0);
                $table->integer('stock');
                $table->boolean('status')->default(true);
                $table->uuid('created_by')->nullable();
                $table->uuid('updated_by')->nullable();
                $table->uuid('deleted_by')->nullable();
                $table->timestamps();

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
        Schema::dropIfExists('products');
    }
}

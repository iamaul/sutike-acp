<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('tag_id');
                $table->string('title');
                $table->string('slug');
                $table->string('header_image');
                $table->longText('body');
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
        Schema::dropIfExists('blogs');
    }
}

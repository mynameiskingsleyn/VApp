<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatvehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catvehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('clist_id')->unsigned();
            $table->mediumText('vins')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')
            ->onDelete('cascade');
            $table->foreign('clist_id')->references('id')->on('clists')
            ->onDelete('cascade');             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catvehicles');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeblogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weblogs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('session');
            $table->string('event_type',50)->nullable();
            $table->longText('json')->nullable();

            $table->timestamps();
           // $table->timestampTz('utc_created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weblogs');
    }
}

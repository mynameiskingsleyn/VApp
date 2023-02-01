<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealers', function (Blueprint $table) {
           // $table->increments('id'); 

            $table->string('dealerCode',8)->index();
            $table->text('dealerName')->nullable();
            $table->text('dealerAddress1')->nullable();
            $table->text('dealerAddress2')->nullable();
            $table->text('dealerCity')->nullable();
            $table->string('dealerState','2')->nullable();
            $table->string('dealerShowroomCountry','25')->nullable();

            $table->string('dealerZipCode','9')->nullable();
            $table->string('dealerShowroomLongitude','25')->nullable();
            $table->string('dealerShowroomLatitude','15')->nullable();
            $table->string('phoneNumber','12')->nullable();

            $table->text('brands')->nullable();
            $table->string('demail',64)->nullable();
            $table->string('hasQuote',2)->nullable();

           
            
            $table->timestamps();
            $table->softDeletes();

        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealers');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
           
            $table->text('codes')->nullable();
            $table->text('descriptions')->nullable();
           /* $table->string('TrimCode',20)->nullable();
            $table->string('TrimDescription',128)->nullable();
            $table->text('DriveType')->nullable(); 
            $table->string('EngineHorsepower',4)->nullable();     
            $table->string('Bodystyle',25)->nullable();
            $table->string('TowingCapacity',25)->nullable(); 
            
            $table->mediumText('ExteriorColorDescription')->nullable();    
            $table->mediumText('ExteriorColorCode')->nullable();  
            $table->mediumText('InteriorColorDescription')->nullable(); 
            $table->mediumText('InteriorColorCode')->nullable();
            $table->mediumText('EngineDescription')->nullable(); 
            $table->string('TransmissionDescription',50)->nullable();
            */

           // $table->string('VIN',20); 
            $table->increments('id'); 

           /* $table->foreign('VIN')->references('VIN')->on('vehicles')
            ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('DealerCode',8);
            $table->string('VIN',20)->index();
            $table->string('StockNumber',6)->nullable();
            $table->string('VehicleType',6)->nullable();
            $table->string('CertificationFlag',6)->nullable();
            $table->string('Year',6)->nullable();
            $table->string('Make',25)->nullable();
            $table->string('Model',25)->nullable();
            $table->text('ModelDescription')->nullable();
            $table->string('CityMPG',6)->nullable();
            $table->string('HwyMPG',6)->nullable();
            $table->string('MSRP',10)->nullable();
            $table->string('StatusCode',6)->nullable();            

           $table->foreign('DealerCode')->references('DealerCode')->on('dealers')
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
        Schema::dropIfExists('vehicles');
    }
}

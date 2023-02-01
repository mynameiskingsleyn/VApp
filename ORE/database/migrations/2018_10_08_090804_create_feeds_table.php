<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_vehicles', function (Blueprint $table) {
          
            $table->integer('DealerCode');
            $table->string('VIN',17)->unique();
            $table->string('StockNumber',4)->nullable();
            $table->string('VehicleType',3)->nullable();
            $table->string('CertificationFlag',5)->nullable();
            $table->string('Year',4)->nullable();
            $table->string('Make',25)->nullable();
            $table->string('Model',25)->nullable();
            $table->string('ModelDescription',128)->nullable();
            $table->string('TrimCode',20)->nullable();
            $table->string('TrimDescription',128)->nullable();
            $table->string('UpperLevelPackageCode',3)->nullable();
            
            $table->string('Bodystyle',25)->nullable();            
            $table->string('Classification',20)->nullable();
            $table->mediumText('OptionCodes')->nullable();
            $table->mediumText('OptionDescription')->nullable();
            $table->mediumText('ExteriorColorDescription')->nullable();            
            $table->mediumText('ExteriorMetaColorDescription')->nullable();
            $table->mediumText('ExteriorColorCode')->nullable();
            $table->mediumText('InteriorFabric')->nullable();
            $table->mediumText('InteriorColorDescription')->nullable();            
            $table->mediumText('InteriorMetaColorDescription')->nullable();
            $table->mediumText('InteriorColorCode')->nullable();
            $table->mediumText('EngineDescription')->nullable();            
            $table->string('EngineHorsepower',4)->nullable();
            $table->string('TransmissionDescription',50)->nullable();
            $table->string('DriveType',4)->nullable();
            $table->string('TowingCapacity',25)->nullable();

            $table->string('SeatingCapacity',4)->nullable();
            $table->string('WheelBase',25)->nullable();
            $table->string('Cab',25)->nullable();
            $table->string('Box',10)->nullable();
            $table->string('CityMPG',6)->nullable();
            $table->string('HwyMPG',6)->nullable();            
            $table->string('ModifiedDate',10)->nullable();
            $table->text('PhotoURL')->nullable();
            $table->string('MSRP',10)->nullable();
            $table->string('StatusCode',4)->nullable();  
           
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('feed_dealers', function (Blueprint $table) {
            /*1-20*/  
              $table->string('___dlr_code',8)->unique();
              $table->string('dlr_cash_st',5);
              $table->string('dlr_active_date',10);
              $table->string('dlr_term_date',20);
              $table->string('dlr_selling_dlr',25);
              $table->text('dlr_dba_name');
              $table->text('dlr_shw_addr1');
              $table->text('dlr_shw_addr2');
              $table->text('dlr_shw_city');
              $table->string('dlr_shw_state',2);
              $table->string('dlr_shw_zip',10);
              $table->string('dlr_shw_zip4',10);
              $table->string('dlr_shw_lat',25);
              $table->string('dlr_shw_long',25);
              $table->string('dlr_shw_phone',25);
              $table->string('dlr_shw_fax',25);
              $table->string('dlr_prin_first_name',62);
              $table->string('dlr_prin_last_name',62);
              $table->string('dlr_sls_mgr_first_na',62);
              $table->string('dlr_sls_mgr_last_nam',62);
          /*20-40*/  
          $table->string('dlr_gen_mgr_first_na',62);
          $table->string('dlr_gen_mgr_last_nam',62);
          $table->string('dlr_office_mgr_first',62);
          $table->string('dlr_office_mgr_last_',62);
          $table->string('dlr_bc',2);
          $table->string('dlr_zone',3);
          $table->char('dlr_fran_chr',1);
          $table->string('dlr_c_active_date',8);
          $table->string('dlr_c_term_date',8);
          $table->string('dlr_c_selling_dlr',50);
          $table->char('dlr_fran_dgd',1);
          $table->string('dlr_d_active_date',8);
          $table->string('dlr_d_term_date',8);
          $table->string('dlr_d_selling_dlr',8);
          $table->enum('dlr_fran_truck',['Y', 'N']);
          $table->string('dlr_t_active_date',8);
          $table->string('dlr_t_term_date',8);
          $table->string('dlr_t_selling_dlr',8);
          $table->enum('dlr_fran_jeep',['Y', 'N']);
          $table->string('dlr_j_active_date',8);    
          /*40-60*/  
              $table->string('dlr_j_term_date',8);
              $table->string('dlr_j_selling_dlr',8);
              $table->string('dlr_email_dlr',62);
              $table->string('dlr_dlr_prin',50);
              $table->string('dlr_email_genmgr',62);
              $table->string('dlr_sls_group_size',2);
              $table->string('dlr_internet_appt',2);
              $table->string('dlr_customer_interne',2);
              $table->string('reserved',50);
              $table->string('dlr_pp_lvl',2);
              $table->string('dlr_c_grp',2);
              $table->string('dlr_cpov',2);
              $table->string('dlr_5star',2);
              $table->string('dlr_5star_anniv',10);
              $table->string('dlr_5star_prob_start',15);
              $table->string('dlr_5star_prob_end',15);
              $table->string('dlr_web_addr',120);
              $table->string('dlr_xpr_lube',120);
              $table->string('dlr_xlube',2);
              $table->string('dlr_qlube',2);
          /*60-80*/  
              $table->string('dlr_smlnk',2);
              $table->string('dlr_sh_serv',2);
              $table->string('dlr_dist_sls',2);
              $table->string('dlr_bl',2);
              $table->string('dlr_fran_fiat',4);
              $table->string('sls_dist_name',62);
              $table->text('showroom_hours');
              $table->string('dlr_fran_ram',2);
              $table->string('dlr_dom_sls',2);
              $table->string('dlr_sc_rec',2);
              $table->string('dlr_free_est',2);
              $table->string('dlr_shuttle',2);
              $table->string('dlr_rental',2);
              $table->string('dlr_early_bird',2);
              $table->string('dlr_24hr_service',2);
              $table->string('dlr_sat_svc',2);
              $table->string('dlr_cert_tech',2);
              $table->string('dlr_gy_tires',2);
              $table->string('dlr_mich_tires',2);
              $table->string('dlr_mopar_perf_parts',2);
          /*80-100*/  
            $table->string('dlr_state_insp',2);
            $table->string('dlr_mopar_acc',2);
            $table->string('dlr_mob_svc',2);
            $table->string('dlr_comp_prices',2);
            $table->string('dlr_mopar_speedshop',2);
            $table->string('dlr_spanish',2);
            $table->string('dlr_workout_center',2);
            $table->string('dlr_play_area',2);
            $table->string('dlr_bbb',2);
            $table->string('dlr_sunday_serice',2);
            $table->string('dlr_free_wifi',2);
            $table->string('dlr_fran_alfa',2);
            $table->string('dlr_cust1',2);
            $table->string('dlr_dist_svc',2);
            $table->string('showroom_open_sunday',2);
             $table->string('showroom_close_sunda',2);
             $table->string('showroom_open_monday',2);
             $table->string('showroom_close_monda',2);
             $table->string('showroom_open_tuesda',2);
            $table->string('showroom_close_tuesd',2);    
            /*100-122*/  
           $table->string('showroom_open_wednes',1);
            $table->string('showroom_close_wedne',1);
            $table->string('showroom_open_thursd',1);
            $table->string('showroom_close_thurs',1);
            $table->string('showroom_open_friday',1);
            $table->string('showroom_close_frida',1);
             $table->string('showroom_open_saturd',1);
            $table->string('showroom_close_satur',1);
            $table->string('service_open_sunday',1);
            $table->string('service_close_sunday',1);
            $table->string('service_open_monday',1);
            $table->string('service_close_monday',1);
             $table->string('service_open_tuesday',1);
            $table->string('service_close_tuesda',1);
            $table->string('service_open_wednesd',1);
            $table->string('service_close_wednes',1);
            $table->string('service_open_thursda',1);
            $table->string('service_close_thursd',1);
            $table->string('service_open_friday',1);
            $table->string('service_close_friday',1);
            $table->string('service_open_saturda',1);
            $table->string('service_close_saturd',1);  
             
              $table->increments('id');  
  
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_vehicles');
        Schema::dropIfExists('feed_dealers');
    }
}


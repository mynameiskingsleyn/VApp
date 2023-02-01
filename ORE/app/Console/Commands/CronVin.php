<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Fcaore\Databucket\Facade\Databucket;
use App\Dealer;
use App\Vehicle;
use App\Catvehicle; 
use Carbon\Carbon; 
use DB;
use Cache;
use App\Zipcode; 
 

class CronVin extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:CronVin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All Individual Vin Loaded';
	
	/**
     * ORE Redis Cache - Initilized Brands.
     *
     * @var array
     */
    protected $brand_array = array('alfa_romeo'); 
	//,'fiat','chrysler','dodge','jeep','ram'
	
	/**
     * ORE Redis Cache - Initilized Vehicle Types.
     *
     * @var array
     */
    protected $vehicle_type_array = array('new','cpo','used'); 
	
	
	protected $general_enabled = false;



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(); 
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
	
		try {	 
			//Vehicle Details - IndividualVin
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;		
					 
						$this->IndividualVin($params_make,$params_vechType);
					 
				}
			} 
		} catch (Exception $e) {
			report($e);
			return false;
		}	 
    	 
    } 	
	public function IndividualVin($params_make, $params_vechType){	
			try{
				$this->logAppend('Start',' Individual VIN: '.$params_vechType.' @'.$params_make); 
				$data=Databucket::vinSet($params_make, $params_vechType);
				 	
			} catch (Exception $e) {
				report($e);
				return false;
			}
			$this->logAppend('End',' Individual VIN: '.$params_vechType.' @'.$params_make); 
		  return true;
	}
	 
	
	
	function logAppend($time, $cronName){
		$dots = '-----------------------------------------------------------------------------';
		$timings = Carbon::now('America/New_York').': *** '.$time.':  '.$cronName.' ****';
		
		if($time == 'Start'){
				$this->info($dots);
				Log::info($dots);
		}
		
		$this->info($timings);
		Log::info($timings); 
		
		if($time == 'End'){
				$this->info($dots);
				Log::info($dots); 
		}
	}
}
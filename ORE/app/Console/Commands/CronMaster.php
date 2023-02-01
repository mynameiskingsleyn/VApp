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
 

class MasterSummary extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:MasterSummary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load summary data from category, subcategory and cat_veh table which will be used for default loading. Page: SNI page';
	
	/**
     * ORE Redis Cache - Initilized Brands.
     *
     * @var array
     */
   // protected $brand_array = array('alfa_romeo','fiat','chrysler','dodge','jeep','ram');
 	//protected $brand_array = array('alfa_romeo','fiat');
	protected $brand_array = array('jeep');
	
	/**
     * ORE Redis Cache - Initilized Vehicle Types.
     *
     * @var array
     */
    protected $vehicle_type_array = array('new','cpo'); 
	
	
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
			if($this->general_enabled){
				// Flush All Cache Systems 
				$this->FlushAll();
				
				// Summary 
				$this->SNI2SummaryCacheSystems();
				
				// Zipcode with Latitude and Lontitdue
				$this->ZipcodeCacheSystems(); 		
				
				// All Dealers
				$this->AllDealersCacheSystems();
			}
			
			// Catid and Subcatid
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				$this->CategoriesSubCategoryIDInititlizedCacheSystems($vehicle_type_array_val);
			}
			
			// Dealers by Make model and year
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;			
					$this->SNI2DealersCacheSystems($params_vechType, $params_make);
				}
			}
			
			// Package and Options
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;			
					$this->package_and_options($params_vechType, $params_make);
				}
			}
			
			//SNI Page-1
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;			
					$this->SNIPage1CacheSystems($params_make, $params_vechType);
				}
			}
			
			 
			//SNI Page-2
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;			
					$this->splitMasterVehicle( $params_vechType,$params_make);
				}
			}
			
			//Vehicle Details - IndividualVin
		/*	foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;			
					$this->IndividualVin($params_make,$params_vechType);
				}
			}
			*/

			
		} catch (Exception $e) {
			report($e);
			return false;
		}	 
    	 
    }
	
	public function FlushAll(){
		
		$this->logAppend('Start', 'Flush Entire Cache');
					Databucket::CacheFlushAll(); 
			 $this->logAppend('End', 'Flush Entire Cache');
			 return true;
	}
	/*
	Getall int<Cat id> and int<Subcat id> BY string{brand} string{model} int{year}
	Page: SNI page
	*/
	public function CategoriesSubCategoryIDInititlizedCacheSystems($params_vechType){
		try {
			  $this->logAppend('Start', 'Categories SubCategory Inititlized ID CacheSystems  @'.$params_vechType);
					$result =  array();		 
					$result = Databucket::sniCatSubCatQuery($params_vechType); 
					Databucket::catsubcat_pipes(json_decode($result, true), "catsubcat:", $params_vechType);
			$this->logAppend('End', 'Categories SubCategory Inititlized ID CacheSystems  @'.$params_vechType);	
		} catch (Exception $e) {
			report($e);
			return false;
		}		
			return true;
    }
	 
	
	function ZipcodeCacheSystems(){
			 $this->logAppend('Start', 'Zipcodes');			 
				$zip_latlon = Zipcode::get(); 		
				foreach($zip_latlon as $key=>$val){
					$zipcode 	= $val['zipcode'];					 
					$latitude 	= number_format(str_replace('+-','-',$val['latitude']),2);
					$longitude 	= number_format($val['longitude'],2);					  
					if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
						Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
					}					
				}
			 $this->logAppend('End', 'Zipcodes');
			 return true;
	}
	/*
	 
	*/
	public function AllDealersCacheSystems(){
		try {
			  $this->logAppend('Start', 'All Dealers CacheSystems');
					Databucket::dealerCollection();
			 $this->logAppend('End', 'All Dealers CacheSystems');	
		} catch (Exception $e) {
			report($e);
			return false;
		}	
		return true;		
    }
	function SNI2DealersCacheSystems($type, $make){
				$this->logAppend('Start', 'SNI2 DEALER BY MAKE-MODEL-YEAR for '.$type.' @'.$make); 
				$lists = Databucket::makeTypeYearModel($type, $make);
				$this->ymk_type = $type;
				$this->ymk_make = $make; 
				$this->VariableForYearMakeModel  = $lists; 
		 
				$cache_array = array();
				 Redis::pipeline(function ($pipe) {
									foreach($this->VariableForYearMakeModel as $lkey => $lval){
									$cache = '';
									$cache = 'dealersByModel:'.strtolower($this->ymk_type).':'.strtolower(str_replace(' ', '_',$this->ymk_make)).':'.strtolower(str_replace(' ', '_',$lval->model)).':'.$lval->year;  
									 $cache_array[] = $cache;
									 $specifList = Databucket::DealerTypeYearModel($this->ymk_type, $this->ymk_make, $lval->year, $lval->model);	 
									 if(!Databucket::isCacheExists($cache)){
												$pipe->set($cache, json_encode($specifList));
									 } 
									}
				 }); 
			$this->logAppend('End',  'SNI2 DEALER BY MAKE-MODEL-YEAR for '.$type.' @'.$make);
			return true;
	}
	
	function SNI2SummaryCacheSystems(){
			$this->logAppend('Start', 'SNI2 Summary'); 
				$getAllInventory = Databucket::sniSummaryQuery();
				Databucket::isCacheSet("summary:", json_encode($getAllInventory)); 
			$this->logAppend('End', 'SNI2 Summary');
			return true;
	}
	
	public function package_and_options($vehicle_type, $make){ 
		$this->logAppend('Start', 'package_and_options '.$vehicle_type.' @'.$make); 
		$data=Databucket::makeTypeYearModel($vehicle_type, $make);
		foreach($data as $d){ 
			$this->package_and_options_split($vehicle_type, $make,$d->year,$d->model);
		}
	
		$this->logAppend('End','package_and_options '.$vehicle_type.' @'.$make); 
		return true;
	}
	
	public function package_and_options_split($params_vechType, $params_make, $params_year, $params_model){ 
		
		try{
				$package_and_options_key = Databucket::makeCache('package_and_options_'.$params_make.$params_vechType.'_'.$params_year.strtolower(str_replace(' ', '_', $params_model)));
				
				if(!Databucket::isCacheExists($package_and_options_key)){
					$chunk_result =	Databucket::PackageAndOptionChunk($params_vechType, $params_make, $params_year, $params_model);				 
					 if(config('databucket.have_chunk')){
						$total_chunk = 0;
						foreach($chunk_result as $key => $value){ 
							Databucket::isCacheSet($package_and_options_key.'_chunk'.$key,json_encode($value));
							$total_chunk++;
					  	} 
					 	 Databucket::isCacheSet($package_and_options_key,$total_chunk);
					}else{
						Databucket::isCacheSet($package_and_options_key,json_encode($chunk_result));
					} 
					    
				 } 
			} catch (Exception $e) {
				report($e);
				return false;
			} 
	}
	
	function SNIPage1CacheSystems($params_make, $params_vechType){
		 try {
			$this->logAppend('Start',' SNI PAGE-1 for '.$params_vechType.' @'.$params_make); 
				
				$landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.$params_vechType);
				if(!Databucket::isCacheExists($landingParamsCacheKey)){
					$land = json_encode(Databucket::sniLandingQuery($params_vechType, $params_make));
					 
					$products = json_decode($land, true);  
					$grouped = collect($products)->mapToGroups(function ($item, $key){  
						return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'], 'cat_id' => $item['cat_id'], 'vehicle_type' => $item['vehicle_type'], 'subcat_id' => $item['subcat_id'], 'msrp_price' => $item['msrp_price'], 'maxs_msrp' => $item['maxs_msrp'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['hwy_mpg'] , 'trim_code' => $item['trim_code'] , 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'], 'body_style' => $item['body_style']  ] ];
					});
					 
					foreach($grouped as $key => $val){
						Databucket::price_pipes($val, $params_vechType, $params_make);  
					} 
					
					Databucket::isCacheSet($landingParamsCacheKey, json_encode($grouped)); 
				} 		 
				
			$this->logAppend('End',' SNI PAGE-1 for '.$params_vechType.' @'.$params_make); 
		 
		 return true;
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
	
	public function splitMasterVehicle($params_vechType, $params_make){		
		try{
			$this->logAppend('Start',' SNI PAGE-2 for '.$params_vechType.' @'.$params_make); 
			$data=Databucket::makeTypeYearModel($params_vechType, $params_make);
			$keymodel='';
			foreach($data as $d){
				$this->MasterVehicle($params_vechType, $params_make,$d->year,$d->model); 
			}			
		} catch (Exception $e) {
			report($e);
			return false;
		}
		$this->logAppend('End',' SNI PAGE-2 for '.$params_vechType.' @'.$params_make); 
		 return true;
	}
	
	public function MasterVehicle($params_vechType, $params_make, $params_year, $params_model){ 
		 
		try{
				$vehicleParamsCacheKey = Databucket::makeCache('vehicle_table_'.$params_make.$params_vechType.'_'.$params_year.strtolower(str_replace(' ', '_', $params_model)));
			 
				
				if(!Databucket::isCacheExists($vehicleParamsCacheKey)){
					$chunk_result =	Databucket::MasterVehicleRelation($params_vechType, $params_make, $params_year, $params_model);				 
					 if(config('databucket.have_chunk')){
						$total_chunk = 0;
						foreach($chunk_result as $key => $value){ 
							Databucket::isCacheSet($vehicleParamsCacheKey.'_chunk'.$key,json_encode($value));
							$total_chunk++;
					  	} 
					 	 Databucket::isCacheSet($vehicleParamsCacheKey,$total_chunk);
					}else{
						Databucket::isCacheSet($vehicleParamsCacheKey,json_encode($chunk_result));
					} 
					    
				 } 
				 
			} catch (Exception $e) {
				report($e);
				return false;
			} 
			
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
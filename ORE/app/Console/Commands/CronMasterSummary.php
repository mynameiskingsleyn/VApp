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
use App\Vmodel;   
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CronMasterSummary extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:CronMasterSummary';

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
  
	protected $brand_array; 
	/**
     * ORE Redis Cache - Initilized Vehicle Types.
     *
     * @var array
     */
   
	protected $vehicle_type_array ; 
	protected $vehicle_type_array_new;  
	
	
	protected $general_enabled = true;

	protected $individual_vin_load = false;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(); 
		
		$this->brand_array = config('ore.etl.make');
		$this->vehicle_type_array = config('ore.etl.vehicle_type');
		$this->vehicle_type_array_new = config('ore.etl.vehicle_type');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
	 
		try {	
		 
			if(env('APP_ENV') != 'local' ){  
				$currenttime =  Carbon::now('America/Vancouver')->format('Y-m-d H:i:s');	  
				 DB::insert('INSERT INTO `fca_ore_audit` (date_sid,`varchar_col_2`,`varchar_col_1`) values ("'.$currenttime.'", ?, ?)', ['redis_load','Start process']);
			}
					 
			if($this->general_enabled){
				
				if(env('APP_ENV') != 'local' ){ 
					$this->landingModalView();
				}
			
				
				// Flush All Cache Systems 
				$this->FlushAll();  
			 /*if(env('APP_ENV') != 'local' ){	
				// Model Table Loading
				$this->vehicle(); 
			 } */
				
				// Summary 
				$this->SNI2SummaryCacheSystems();
				
				// Zipcode with Latitude and Lontitdue
				$this->ZipcodeCacheSystems(); 		
				
				// All Dealers
				$this->AllDealersCacheSystems();
				
				// All Options codes and desc
				$this->optionCodes();
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
						/* if($params_vechType=='used' && ($params_make=='fiat' || $params_make=='chrysler' || $params_make=='dodge')){}else{	
							$this->SNI2DealersCacheSystems($params_vechType, $params_make);
						} */
				}
			} 
			
			//SNI Page-1
			foreach($this->vehicle_type_array_new as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;	
					//$this->SNIPage1CacheSystems($params_make, $params_vechType);
					
					
						/* if($params_vechType=='used' && ($params_make=='fiat' || $params_make=='chrysler' || $params_make=='dodge')){}else{		
							$this->SNIPage1CacheSystems($params_make, $params_vechType);
						} */
				}
			}
			
			 
			//SNI Page-2
			foreach($this->vehicle_type_array as $vehicle_type_array_key => $vehicle_type_array_val){
				foreach($this->brand_array as $brand_array_key => $brand_array_val){
					$params_make = $brand_array_val;
					$params_vechType = $vehicle_type_array_val;	
					$this->splitMasterVehicle( $params_vechType,$params_make);
					
						/* if($params_vechType=='used' && ($params_make=='fiat' || $params_make=='chrysler' || $params_make=='dodge')){}else{		
							$this->splitMasterVehicle( $params_vechType,$params_make);
						} */
				}
			} 
			
			
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
			
					if(env('APP_ENV') != 'local' ){
					 $currenttime =  Carbon::now('America/Vancouver')->format('Y-m-d H:i:s');	  
					 DB::insert('INSERT INTO `fca_ore_audit` (date_sid,`varchar_col_2`,`varchar_col_1`) values ("'.$currenttime.'", ?, ?)', ['redis_load','End process']);
					}
					 
			
		} catch (Exception $e) {
			report($e);
			return false;
		}	 
    	 
    }
	
	public function FlushAll(){
		
		$this->logAppend('Start', 'Flush DB2 Cache');
					
		//Databucket::CacheFlushCurrentRedisDB();
		Databucket::CacheFlushAll(); 
		Databucket::isCacheSet('cache_date', Carbon::now('America/New_York'));
		$this->logAppend('End', 'Flush DB2 Cache');
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
				
				$landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.':'.$params_vechType.':'.config('ore.default_zipcode'));
				if(!Databucket::isCacheExists($landingParamsCacheKey)){
					$land = json_encode(Databucket::sniLandingQuery($params_vechType, $params_make, config('ore.default_zipcode')));
					 
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
			 if(env('APP_ENV') != 'local' ){
					 if($this->individual_vin_load){ 
						 try{
								$this->logAppend('Start',' Individual VIN: '.$params_vechType.' @'.$params_make); 			 
				
								$data=Databucket::vinSet($params_make, $params_vechType);
					
				
							} catch (Exception $e) {
								report($e);
								return false;
							}
						$this->logAppend('End',' Individual VIN: '.$params_vechType.' @'.$params_make); 
					}
			}
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
				$vehicleParamsCacheKey = Databucket::makeCache('vehicle_table_'.$params_make.$params_vechType.'_'.$params_year.Databucket::customModel($params_model));
			 
				
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
	
	public function optionCodes(){	
			try{
				$this->logAppend('Start',' Option Codes '); 
				$data=Databucket::optionCodes();
				 	
			} catch (Exception $e) {
				\Log::info($e);
			}
			$this->logAppend('End',' Option Codes '); 
		  return true;
	}

	public function landingModalView(){
			try{
				$this->logAppend('Start',' landingModalView '); 
				 Databucket::queryLandingModalView();
				 	
			} catch (Exception $e) {
				 
				\Log::info($e);
			}
			$this->logAppend('End','landingModalView '); 
		  return true;
	}
	
	
		public function vehicle(){
					$divisionCode = ['C','D','J','T','X']; 
					$this->logAppend('Start',' Start Model table'); 
				$vehicle = [];	
				try{
						 
					foreach($divisionCode as $key_divisionCode => $value_divisionCode){
						$GetVehicleDetails = 'https://www.chrysler.com/hostd/getvehicles.xml?divisionCode='.$value_divisionCode;
					
						$client = new \GuzzleHttp\Client();
						$res = $client->request('GET', $GetVehicleDetails); 
						$xml_string = $res->getBody();
						$xml = simplexml_load_string($xml_string);
						$json = json_encode($xml);
						$array = json_decode($json, true); 
						
							if(Arr::has($array['data'],'vehicles')) { 
								$bulk = $array['data']['vehicles']['vehicle'];
								$special_character_array = ['Â','Ã',"Â","Ã"];
										foreach($bulk as $bulk_key=>$bulk_value){ 
											 
											//  $new_trim = $bulk_value['@attributes']['trimDesc'];
											 // $new_desc = $bulk_value['@attributes']['description'];
											
											$new_trim = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['trimDesc']));
											//$modelDesc = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['modelDesc']));		
											$new_desc = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['description'])); 
											
											$new_trim = trim(str_replace('®', ' ', $new_trim));
											//$modelDesc = trim(str_replace('®', ' ', $modelDesc)); $new_desc = trim(str_replace('®', ' ', $new_desc)); 
											
											 
											$modelDesc = trim($bulk_value['@attributes']['modelDesc']);
											
											$new_year =  $bulk_value['@attributes']['modelYearDescription'];
											
											if($new_year <= date('Y') && $new_year > date('Y')-3){
											
													$vehicle[] = [	'description' => str_replace($special_character_array , '', $new_desc),
														'franchiseDescription' => str_replace($special_character_array , '', $bulk_value['@attributes']['franchiseDescription']),
														'modelDesc' => str_replace($special_character_array , '', $modelDesc) ,
														'trimDesc' => str_replace($special_character_array , '', $new_trim),
														'modelYearDescription' =>$new_year,	
														'franchiseCode' => $bulk_value['@attributes']['franchiseCode'],
														'baseVehicleMsrp' => $bulk_value['@attributes']['baseVehicleMsrp']]; 
											}
										}
							}
						} 
						//	\Log::info($vehicle);
						   Vmodel::truncate(); 
						   Vmodel::insert($vehicle);
						$this->logAppend('End',' End Model table'); 
						
				}catch (\Exception $e) {
					\Log::info( $e->getMessage());
						return $e->getMessage();
				}  
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
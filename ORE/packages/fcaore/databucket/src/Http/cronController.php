<?php
namespace Fcaore\Databucket\Http;

use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
//use App\Libs\Filter\FilterInterface as Filter;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Dealer;
use App\Vehicle;
use App\Catvehicle;
use App\Zipcode;
use DB;
use Log;
 
use App\Facades\OreDSClass; 
use Fcaore\Databucket\Facade\Databucket;

class cronController extends Controller
{
	private $getAllVehicle;
	private $getAllInventory;
	private $NewVehcileLoad;
	private $today_date;
	private $tableNameCategories 	= 'fca_ore_categories';
	private $tableNameSubcategories = 'fca_ore_subcategories';
	private $tableNameCatvehicle 	= 'fca_ore_category_vehicle';
	//private $tableNameInput 		= 'fca_ore_input_18feb';
	private $tableNameInput 		= 'fca_ore_input';
	private $tableNameDealer 		= 'fca_ore_dealer_info';
	private $tableNameZipcode 		= 'fca_ore_zipcodes';
	private $ExecuteSettings 		= true;
	
	private $VariableForYearMakeModel;
	private $ymk_type;
	private $ymk_make;
	
	
	public function __construct(){
       $this->today_date = \Carbon\Carbon::today()->toDateString(); 
	}

	/*
	    Flush all Cache Key's
	*/
	public function flushAllCache(){
		try {
			 Log::info(' *** Cron: begin flushing all cache ****'); 
			 //Redis::flushall();
			 Databucket::CacheFlushAll();
			 Log::info(' *** Cron: end flushing all cache ****');
			dd('*** done ****');
		} catch (Exception $e) {
			report($e);
			return false;
		}
	}

	/*
	    Flush Specific Cache Key's
	*/
	public function flushkey(REQUEST $request){
		try {
			$key = $request->get('key');
			 Log::info(' *** Cron: begin flushing '.$key.' cache ****'); 
			// Redis::del($key);
			 Databucket::DelKey($key);
			 Log::info(' *** Cron: end flushing all cache ****'); 
			dd('*** done ****');
		} catch (Exception $e) {
			report($e);
			return false;
		}
	}

	/*
	    Cache Key's List
	*/
	public function ViewKeys(){
		try {
			$allkeys =  Databucket::Keys();
			//$allkeys = Redis::keys('*');
			dd($allkeys);
			echo (" ---- DONE ---- ");
			
		} catch (Exception $e) {
			report($e);
			return false;
		}
	}
	
	/*
	    Manual Cron Executed from Browser
	*/
	public function cron_list_development_purpose(){	 
		return view('databucket::sni.cron');
	}
	
	
	
	/*
	Getall int<Cat id> and int<Subcat id> BY string{brand} string{model} int{year}
	Page: SNI page
	*/
	public function dbmodifierschecker(){
		
		dd('CURRENT DB GETTING:'.Databucket::isCacheGet('currentDB'));
	}
	public function dbmodifiers(){
			 
			  $index = 3;
			// Log::info('Modified and Going to Use dB: '.$index);
			// $flushdb = 2;
			// Log::info('Flush dB: '.$flushdb);
			
			//Databucket::isCacheSet('currentDB', $index);  
			  
			 Databucket::CacheSelect($index); 
			 Databucket::isCacheSet('currentDB', $index);  
			 
			// Databucket::swapdb($flushdb,$index);
			// Databucket::FlushDB($flushdb);
				
			//  Log::info('Swap DB: FROM:'.$flushdb.' TO '.$index );
			  Log::info('CURRENT DB GETTING:'.Databucket::isCacheGet('currentDB'));
			  dd('*** done ****');
	}
	
	/*
	Getall int<Cat id> and int<Subcat id> BY string{brand} string{model} int{year}
	Page: SNI page
	*/
	public function getCatSubCat($vehicle_type){
		try {
			 
			$params_vechType = ($vehicle_type == 'new') ?  'New' : "CPO";
			Log::info(' *** Cron: begin getCatSubCat query '.$params_vechType.' ****');  
			$sniParamsCacheKey = config('databucket.isCacheWithDate') ? "catsubcat:".$this->today_date : "catsubcat:"; 
			$result =  array();		 
		 	$result = Databucket::sniCatSubCatQuery($params_vechType); 
			Databucket::catsubcat_pipes(json_decode($result, true), $sniParamsCacheKey, $params_vechType);
		
			
			Log::info(' *** Cron: end getCatSubCat query '.$params_vechType.' ****');
			dd('*** done ****');
		} catch (Exception $e) {
			report($e);
			return false;
		}			
    }
	
		/*
	Load summary data from category, subcategory and cat_veh table which will be used for default loading
	Page: SNI page
	*/
	public function summaryQuery(){
		try {
			Log::info(' *** Cron: begin summary query ****');  
			$sniParamsCacheKey = config('databucket.isCacheWithDate') ? "summary:".$this->today_date : "summary:"; 
						 
			$this->getAllInventory = Databucket::sniSummaryQuery();
			Databucket::isCacheSet($sniParamsCacheKey, json_encode($this->getAllInventory));
			 
			 
			Log::info(' *** Cron: end summary query ****');
			dd('*** done ****');
		} catch (Exception $e) {
			report($e);
			return false;
		}			
    }
	
	/*
	Load data for NEW vehicle on landing page
	Page: Landing page
	*/
	public function new_vehcile_load($params_vechType, $params_make){
	 
		try {
			Log::info(' *** Cron(new_vehcile_load): begin make:'.$params_make.', condition:'.$params_vechType.' landing page models ****'); 
				
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
		Log::info(' *** Cron: begin make:'.$params_make.', condition:'.$params_vechType.'  landing page models ****'); 
		dd('*** done ****');
	} catch (Exception $e) {
			report($e);
			return false;
		}	
			
	} 
	/*
	Load All Vehicle data (10 lac approx) in memory only one time
	Table used: category, subcategory and cat_veh
	*/
	public function vehicle_init_load($params_make,$params_vechType){ 
		try {
			 
			Log::info(' *** Cron(vehicle_init_load): begin make:'.$params_make.', condition:'.$params_vechType.' sni all vehicle loads ****'); 
			
			$vehicleParamsCacheKey = Databucket::makeCache('vehicle_table_'.$params_make.$params_vechType);
			$chunk_result = $this->splitMasterVehicle($params_vechType, $params_make);
			
			Log::info(' *** Cron(vehicle_init_load): end make:'.$params_make.', condition:'.$params_vechType.' sni all vehicle loads ****'); 
			dd('*** done ****');
		 } catch (Exception $e) {
			report($e);
			return false;
		}
	}
	public function package_and_options($vehicle_type, $make){ 
		Log::info(' *** Cron: begin '.$vehicle_type.' package_and_options  ****');
		$data=Databucket::makeTypeYearModel($vehicle_type, $make);
		foreach($data as $d){
			//$chunk_result =	Databucket::PackageAndOptionChunk($vehicle_type, $make, $d->year, $d->model);	
			$this->package_and_options_split($vehicle_type, $make,$d->year,$d->model);
		}
	
		Log::info(' *** Cron: end '.$vehicle_type.' package_and_options  ****'); 
		dd('*** done ****');
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
	
	public function dealerForYearMakeModel($type, $make){
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
			  
		 
		
		\Log::info('**************************   END    *************************************');	
			 
		
		dd('*** done ****');
	}

	public function feeddealers(Request $request){
		Log::info(' *** Cron: begin Dealer Master Records  ****'); 
		Databucket::dealerCollection();
		Log::info(' *** Cron: End Dealer Master Records  ****');
		dd('*** done ****');
	}
	 
	public function splitMasterVehicle($params_vechType, $params_make){		
		try{
			$data=Databucket::makeTypeYearModel($params_vechType, $params_make);
			$keymodel='';
			foreach($data as $d){
				$this->MasterVehicle($params_vechType, $params_make,$d->year,$d->model); 
			}			
		} catch (Exception $e) {
			report($e);
			return false;
		}
		
		Log::info(' *** Cron: end Cond:'.$params_vechType.',Make:'.$params_make.' Master Memory  ****'); 
		dd('*** done ****');
	}
	
	public function MasterVehicle($params_vechType, $params_make, $params_year, $params_model){ 
		Log::info(' *** Cron: begin Cond:'.$params_vechType.',Make:'.$params_make.' Master Memory  ****'); 
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
	} 
	
	
	public function Zipcode(){
		$zip_latlon = Zipcode::get(); 
		
		foreach($zip_latlon as $key=>$val){
			$zipcode 	= $val['zipcode'];
			 
			$latitude 	= number_format(str_replace('+-','-',$val['latitude']),2);
			$longitude 	= number_format($val['longitude'],2);
			  
		 	if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
				Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
		 	}
			
		}
			dd('*** done ****');
		foreach($latlon_zip as $key=>$val){
			$latlon_zip 	= $val['latlon_zip'];
			$latitude 	= $val['latitude'];
			$longitude 	= $val['longitude'];
			if(!Databucket::isCacheExists('cord:latlan:zip:'.$latitude.':'.$longitude)){
				Databucket::isCacheSet('cord:latlan:zip:'.$latitude.':'.$longitude, $latlon_zip);
			}
			
		}
		
	
		
	}
}
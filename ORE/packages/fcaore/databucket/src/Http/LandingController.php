<?php

namespace Fcaore\Databucket\Http;

use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller; 
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request; 
use DB; 
use App\Facades\OreDSClass; 
use Fcaore\Databucket\Facade\Databucket;
use App\Zipcode;
use Illuminate\Support\Arr; 


class LandingController extends Controller
{
    /**
     * @model $dealer
     */
    private $dealer;

    /**
     * @model $vehicle
     */
    private $vehicle;
  
    /*
    *   @api_key Google Key obtain from .env file
    */
    protected $googleKey; 

     /**
     * Collection of entire Landing model information
     * @FilterQuery: $getLanding
     * @type : Json (String)
     */
    private $getLanding;

    /**
     * Collection of entire vehicle details
     * @FilterQuery: $getAllVehicle
     * @type : Array
     */
    private $getAllVehicle;


    /**
    *
    *
    */
    private $tier1_host;


    /**
     * Constructor
     *
     * @param $Models 
     */
    public function __construct(){ 
        $this->googleKey= env("GOOGLE_APIKEY", "");
        $this->tier1_host = array('jeep', 'ramtrucks', 'fiatusa', 'dodge', 'chrysler', 'jeep.com', 'ramtrucks.com', 'fiatusa.com', 'dodge.com', 'chrysler.com','www.jeep.com', 'www.ramtrucks.com', 'www.fiatusa.com', 'www.dodge.com', 'www.chrysler.com'); 
    } 
    
     /**
     * FCA-ORE First screen. This screen loads first request of HTTP Request  
     *
     * @return Default Web Page.
     * 
     */
    public function on_load(Request $request){
        $http_referer = "ore";
        \Log::info('LandingPage:::'. $http_referer);
        $request->session()->put('tiers', $http_referer);
        return view('databucket::sni.master_frame'); 
    } 

    /**
     * FCA-ORE brand load screen. This screen loads first request of HTTP Request  
     *
     * @return Default Web Page.
     * 
     */
    public function on_brand_load(Request $request, $make,$tiers = 'ore'){
       
        $make = strtolower($make);
        $tiers_array = array('ore','t1');
        $http_referer = in_array($tiers, $tiers_array) ? $tiers : 'ore';
        $request->session()->put('tiers', $http_referer); 
		$zipcode = '';        
        $zipcode = !empty($zipcode) ? str_pad($zipcode, 5, 0, STR_PAD_LEFT) : ''; 
        return view('databucket::sni.brand_selection',['params_make' =>$make, 'tier' =>$http_referer,'zipcode' =>$zipcode]); 
    } 

    /**
     * Find Zipcode using latitude and longitude
     *
     * @var Illuminate\Http\Request $request
     * 
     * @return array.
     * 
     */
    public function on_zip_by_cord(Request $request){
        $params = $request->all();  
        
        $param_latitude =$params['latitude']; 
        $param_longitude =$params['longitude']; 

        $cache_latlong = Databucket::makeCache($param_latitude.':'.$param_longitude);  
        if(Databucket::isCacheExists($cache_latlong)){  
            $zipcode = Databucket::isCacheGet($cache_latlong); 
			return ['status' => 'success', 'message' => $zipcode];
        }else{ 
            $array = Databucket::findZipByLatLon($param_latitude, $param_longitude);  
			if(Arr::has($array,'status')) {
				if($array['status'] == 'success'){
					$zipcode = $array['message'];
					Databucket::isCacheSet($cache_latlong, json_encode($zipcode));
					return ['status' => 'success', 'message' => $array['message']];
				}else{
					return ['status' => 'error', 'message' => $array['message']];
				}
			}
            return ['status' => 'error', 'message' => 'Unknown Issues'];
        } 
         
     }

     /**
     * Find latitude and longitude using zipcode
     *
     * @var Illuminate\Http\Request $request
     * 
     * @return  array.
     * 
     */
    public function on_cord_by_zip(Request $request){
        
        $zipcode = $request->get('zipcode');   
		$cords = Databucket::getLatLonByZipcode($zipcode);
		 
		return $cords;
		
         $cache_cords = Databucket::makeCache('latlan:'.$zipcode);
         
        if(Databucket::isCacheExists($cache_cords)){             
            $cords = json_decode(Databucket::isCacheGet($cache_cords), true);            
        }else{      
            $cords = Databucket::getCordinatesUsingZipcode($zipcode);
                                  
            Databucket::isCacheSet($cache_cords, json_encode($cords));            
        } 
        
        return $cords;
      }

    /**
     * Find dealers which are available in certain range(from 25mi upto 150mi) of zip code
     *
    * @var Illuminate\Http\Request $request
     * 
     * @return array.
     * 
     */
    public function on_cpo_dealers(Request $request){
        $latitude   = $request->get('latitude'); 
        $longitude  = $request->get('longitude'); 
        $zipcode    = $request->get('zipcode');
        $radius     = $request->get('radius'); 
        try{
            $cache_cords = Databucket::makeCache('latlan:'.$zipcode);
            if($latitude=='undefined' || $longitude=='undefined'){
                    $cords = Databucket::getCordinatesUsingZipcode($zipcode);
                    $latitude   = $cords['lat']; 
                    $longitude  = $cords['lon'];                    
            }
             $cache_searchWithIn = Databucket::makeCache($zipcode.$radius);

             if(Databucket::isCacheExists($cache_searchWithIn)){ //string
                $dealers = Databucket::isCacheGet($cache_searchWithIn);
            }else{  //string
                $dealer = Databucket::radius($latitude,$longitude,$zipcode,$radius);  
                Databucket::isCacheSet($cache_searchWithIn,  $dealer['withmiles']); 
            }  
        }catch(\Exception $e){
            dd($e->getMessage());
        }  

       if(gettype($dealers) == "string"){
        $dealer_array  =json_decode($dealers, true);
       }else $dealer_array  =$dealers; 
         
        $result['string'] = $dealers;
        $result['html'] = view('databucket::sni.filter.render.dealer_list',['dealers' =>$dealer_array])->render();
        return $result;
    } 
    
    
    /**
     * if Vehicle Condition New and then updated into Default loading
     *
     * @var Illuminate\Http\Request $request
     * 
     * @return Passing Models.
     * 
     */
    public function on_new_vehicle(Request $request,$params_make, $zipcode){
       
         $radius = 150;
         $isT1tier = 'ore';
         if(!$request->session()->exists('tiers')){ 
            $isT1tier = 'ore';
         }else{
            $isT1tier = $request->session()->get('tiers');
         }
         //\Log::info('Tiers SessionVal::'.$isT1tier);

        $params_make = strtolower($params_make);
		$params_vechType = 'new';
        //$landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.':'.$params_vechType.':'.$zipcode.':'.$radius);  
        $landingParamsCacheKey = Databucket::makeCache('landing:'.$params_make.':'.$params_vechType);  
		 
		 if(!Databucket::isCacheExists($landingParamsCacheKey)){
					$land = json_encode(Databucket::sniLandingQuery($params_vechType, $params_make, $zipcode));
					 
					$products = json_decode($land, true);  
					$grouped = collect($products)->mapToGroups(function ($item, $key){  
						//return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'], 'cat_id' => $item['cat_id'], 'vehicle_type' => $item['vehicle_type'], 'subcat_id' => $item['subcat_id'], 'msrp_price' => $item['msrp_price'], 'maxs_msrp' => $item['maxs_msrp'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['hwy_mpg'] , 'trim_code' => $item['trim_code'] , 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'], 'body_style' => $item['body_style']  ] ];
                        return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'],  'msrp_price' => $item['msrp_price'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['city_mpg'] , 'trim_code' => $item['trim_code'] , 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'] ] ];
					});
					 $grouped = Databucket::mergeGroup($grouped);
					foreach($grouped as $key => $val){
						Databucket::price_pipes($val, $params_vechType, $params_make);  
					} 
					
					Databucket::isCacheSet($landingParamsCacheKey, json_encode($grouped)); 
				} else{
            $grouped = Databucket::isCacheGet($landingParamsCacheKey);
        } 
     
        $products = json_decode($grouped, true);  

        switch($params_make) { 
            case 'chrysler': $make_code='C'; $make_url='chrysler'; break;
            case 'dodge': $make_code='D'; $make_url='dodge'; break;
            case 'fiat': $make_code='X'; $make_url='fiatusa'; break;
            case 'jeep': $make_code='J'; $make_url='jeep'; break;
            case 'ram': $make_code='R'; $make_url='ramtrucks'; break;
            case 'alfa romeo': $make_code='Y'; $make_url='alfaromeousa'; break; 
			case 'alfa_romeo': $make_code='Y'; $make_url='alfaromeousa'; break; 
        }

        /*
        * Skipping 2018 year for all brands
        */
        //\Log::info('============Skipping 2018 for all brand======');
        //\Log::info($products);
        $filtered_products = array();
         foreach ($products as $model => $model_list) {
            if(!empty($model_list)){
                $model_list_array = array();
                foreach ($model_list as $key => $value) {
                    if($value['year'] == '2018'){
                        continue;
                    }
                    array_push($model_list_array,$value);
                }
                if(!empty($model_list_array)){
                    $filtered_products[$model] = $model_list_array;                
                }
            }
         }
        //\Log::info('============Skipping 2018 for all brand filtered models======');
        //\Log::info($filtered_products);
         
        return view('databucket::sni.landing.model_list',['products' => $filtered_products, 'make' => $params_make, 'make_code'=>$make_code, 'make_url'=>$make_url, 'condition' => 'new', 'zipcode' => $zipcode, 'tier_platform' => $isT1tier]);

    }  

    /**
     * if Vehicle Condition CPOV and then updated into Default loading
     *
     * @var Illuminate\Http\Request $request
     * 
     * @return Passing Models.
     * 
     */
    public function on_cpo_vehicle(Request $request){	 
        $params = $request->all();  
        $params_vechType =$params['vechType']; 
        $params_make =$params['make'];   
        $params_zipcode =$params['zipcode'];  
        $alldealers = $params_dealers = array(); 
		switch($params_make) { 
            case 'chrysler': $make_code='C'; $make_url='chrysler'; break;
            case 'dodge': $make_code='D'; $make_url='dodge'; break;
            case 'fiat': $make_code='X'; $make_url='fiatusa'; break;
            case 'jeep': $make_code='J'; $make_url='jeep'; break;
            case 'ram': $make_code='R'; $make_url='ramtrucks'; break;
            case 'alfa romeo': $make_code='Y'; $make_url='alfaromeousa'; break; 
			case 'alfa_romeo': $make_code='Y'; $make_url='alfaromeousa'; break; 
			default: $make_code='J'; $make_url='jeep'; break;
        }
    
    
		 $latlan_zipcode = Databucket::makeCache('cord:zip:latlan:'.$params_zipcode);
		 if(!Databucket::isCacheExists($latlan_zipcode)){
			 Databucket::LatLongZipcode($params_zipcode);
			 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$params_zipcode);
			 $getCordinates = explode("@",$latlan_zipcode);
		 }else{
			 $latlan_zipcode = Databucket::isCacheGet('cord:zip:latlan:'.$params_zipcode);
			 $getCordinates = explode("@",$latlan_zipcode);
		 } 
        
		$params_dealers =  Databucket::radiusCPOByQuery($params_make,$params_vechType,$params_zipcode,'25');
     
      
        $dealerCode = [];
       
        if(count($params_dealers) > 0){
            foreach($params_dealers as $key => $value ){
                $dealerCode[] = $key;
            }  
           
 
          $landingCpoParamsCacheKey = Databucket::makeCache('landing:'.$params_vechType.':'.$params_make.':'.$params_zipcode); 
 
            if(!Databucket::isCacheExists($landingCpoParamsCacheKey)){ 
                 $findCpoLanding = json_encode(Databucket::CpoQuery($params_vechType, $params_make, $dealerCode));
                 $products = json_decode($findCpoLanding, true);  
                 $grouped = collect($products)->mapToGroups(function ($item, $key){  
                    return [$item['models'] => ['models'=>$item['models'],'year' => $item['year'], 'count' => $item['cnt'], 'cat_id' => 0, 'vehicle_type' => $item['vehicle_type'], 'subcat_id' => 0, 'msrp_price' => $item['msrp_price'], 'maxs_msrp'=>$item['maxs_msrp'], 'city_mpg' => $item['city_mpg'], 'hwy_mpg' => $item['hwy_mpg'] , 'trim_code' => $item['trim_code'], 'interior_fabric' => $item['interior_fabric'], 'exterior_color_code' => $item['exterior_color_code'], 'towing_capacity_count' => $item['towing_capacity_count'], 'upper_level_pkg_cd' => $item['upper_level_pkg_cd'], 'body_style' => $item['body_style'], 'max_year' => $item['max_year'], 'min_year' => $item['min_year'] ] ];
                 });
				 
				 foreach($grouped as $key => $val){
						Databucket::price_pipes($val, $params_vechType, $params_make);  
					}
					
                 Databucket::isCacheSet($landingCpoParamsCacheKey, json_encode($grouped));
             }else{
                 $grouped = Databucket::isCacheGet($landingCpoParamsCacheKey);
             } 
            
             $products = json_decode($grouped, true);  
            return view('databucket::sni.landing.model_list_cpo',['products' => $products, 'make' => $params_make, 'make_code'=>$make_code, 'make_url'=>$make_url, 'condition' => $params_vechType, 'zipcode' => $params_zipcode]);

        }else{
            return view('databucket::sni.landing.model_list_cpo',['products' => [], 'make' => $params_make, 'make_code'=>$make_code, 'make_url'=>$make_url, 'condition' => $params_vechType, 'zipcode' => $params_zipcode]);
        } 
		 
    }

  
        
}

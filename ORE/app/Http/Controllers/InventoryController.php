<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis; 
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;  
use App\Vmodel;
use DB;
 
use App\Facades\OreDSClass; 
use Fcaore\Databucket\Facade\Databucket;

class InventoryController extends Controller
{
    /**
     * Dealer Model: $dealer
     */
    private $dealer;

    /**
     * Vehicle Model: $vehicle
     */
    private $vehicle;

    /*
    *   Carbon Today Day
    */
    private $today;

    /*
    *   Filter Interface
    */
    protected $filter;

    /*
    *   Google Key obtain from .env file
    */
    protected $googleKey;

    /**
     * FilterQuery: $getAllInventory
     */
    private $getAllInventory;

    /**
     * Collection of Filter Attributes
     * @FilterQuery: $colFilterAttr
     * @type : Array
     */
    private $colFilterAttr;

    private $getSegregateInventory;

    


    /**
     * Constructor
     *
     * @param $Models 
     */
    public function __construct(){
        
        $this->googleKey= env("GOOGLE_APIKEY", "");        
        $this->today = substr(Carbon::today('America/Vancouver'), 0 ,10);         
    }
	
	public function vehicle(){
				$divisionCode = ['J','D','T','C','X','Y']; 
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
								 
								$bulk = $array['data']['vehicles']['vehicle'];
								
								foreach($bulk as $bulk_key=>$bulk_value){	
								$vehicle[] = [	'description' => $bulk_value['@attributes']['description'],
												'franchiseDescription' => $bulk_value['@attributes']['franchiseDescription'],
												'modelDesc' => $bulk_value['@attributes']['modelDesc'],
												'trimDesc' => $bulk_value['@attributes']['trimDesc'],
												'modelYearDescription' => $bulk_value['@attributes']['modelYearDescription'],	
												'franchiseCode' => $bulk_value['@attributes']['franchiseCode'],					
											]	;					
										/*	$vehicle[$bulk_key]['description'] = $bulk_value['@attributes']['description'];
											$vehicle[$bulk_key]['franchiseDescription'] = $bulk_value['@attributes']['franchiseDescription'];
											$vehicle[$bulk_key]['modelDesc'] = $bulk_value['@attributes']['modelDesc'];
											$vehicle[$bulk_key]['trimDesc'] = $bulk_value['@attributes']['trimDesc'];
											$vehicle[$bulk_key]['modelYearDescription']= $bulk_value['@attributes']['modelYearDescription'];		
										*/							
								}
						}
						
						// dd($vehicle);
						Vmodel::insert($vehicle);
				}catch (\Exception $e) {
						return $e->getMessage();
				}  
		}
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('inventory.layout');
    } 

     public function getGeocoding($zip){  
        //GOOGLE_APIKEY=AIzaSyCwVVLqNxEtThgRPc__TkhnG30mttuiz20
		
		if( \Ore::isCacheExists('cord:zip:latlan:'.$zip) ){
			$array[0] = 'success';
			$availzips = explode("@",\Ore::cacheGet('cord:zip:latlan:'.$zip));			
			$array[1] = $availzips[0];
			$array[2] = $availzips[1]; 
			
			 return $array;
		}else{			
			$json = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$zip.'&key='.$this->googleKey;
			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', $json);
			$res->getStatusCode(); 
			$res->getHeaderLine('content-type'); 
			$output = $res->getBody();  
			$result = json_decode($output, true);
			//dd($result);
			if(array_key_exists("status", $result)){
				if($result["status"] == 'OK'){
					$array[0] = 'success';
					$array[1] = $result['results'][0]['geometry']['lat'];
					$array[2] = $result['results'][0]['geometry']['lng']; 
					
					\Ore::cacheSet('cord:zip:latlan:'.$zip, $array[1].'@'.$array[2]);
				}else{
					$array[0] = 'error';
					if(array_key_exists("error_message", $result)){
						$array[1] = $result['error_message'];
					}else{
						$array[1] = 'UNKNOWN ERROR';
					}  
				}
			}  
			return $array;
		}
    }

    public function getLocator(Request $request){ 

         $lat = $request->get('lat');
         $lon = $request->get('lon');
         
		if( \Ore::isCacheExists('cord:latlan:zip:'.$lat.':'.$lon) ){
			$array[0] = 'success';
			$array['1'] = \Ore::cacheGet('cord:latlan:zip:'.$lat.':'.$lon);
			 return $array;
		}else{
			 $glink = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lon . "&sensor=false&key=".$this->googleKey;//.$googleKey;

			$client = new \GuzzleHttp\Client();
			$res = $client->request('GET', $glink);
			$res->getStatusCode(); 
			$res->getHeaderLine('content-type'); 
			$output = $res->getBody();  
			$result = json_decode($output, true);
			if($result['error_message']){
				$array[0] = 'error';
				$array[1] = $result['error_message'];
			}else if(count($result['results']) > 0){
				$array[0] = 'success';
				$array['1'] = $result['results'][0]['address_components']; 
				\Ore::cacheSet('cord:latlan:zip:'.$lat.':'.$lon, $result['results'][0]['address_components']);
			}else{
				$array[0] = 'error';
				$array[1] = 'Cant fetch zipcode';
			}
			
			return $array;//response()->json($output); 
		}
         
    }

    public function filterByZipcode(Request $request){
        $zipcode = $request->get('zipcode'); 
        $radius = $request->get('radius');

        try{
             $cache_searchWithIn = md5($zipcode.$radius);
            if(Redis::exists($cache_searchWithIn)){ 
              // echo "Cache";
                $dealers = Redis::get($cache_searchWithIn);

            }else{ 
                //echo "DB"; 
                if($zipcode == '48301'){                   
                    $lat = '40.4474';
                    $lng = '-74.6715';
                }else  if($zipcode == '48308'){
                    $lat = '42.6626';
                    $lng = '-83.1837';
                }else if($zipcode == '48308'){
                    $lat = '43.2435';
                    $lng = '-82.5301';
                }else{
                    $lat = '42.587';
                    $lng = '-83.2359';
                }

               
               $resGeocoding = $this->getGeocoding($zipcode);
               if($resGeocoding[0] == "success"){
                   $lat = $resGeocoding[1];
                   $lng = $resGeocoding[2];
               }else{
                  //  dd($resGeocoding[1]);
               }
               $dealers = $this->expandeRadius($lat, $lng, $radius);               
               // Redis::set($cache_searchWithIn, $dealers);
            }  
        }catch(\Exception $e){
            dd($e->getMessage());
        }  
        return view('inventory.render.dealer',['dealers'=>json_decode($dealers, true)])->render();
    }

    public function expandeRadius($lat, $lng, $radius){
        
       /* $dealers = $this->dealer
                        ->distance($lat, $lng, $radius)                
                        ->get()
                        ->toJson(); 

       if(env('NEED_EXPAND_RADIUS','Y') == 'Y'){
                if($radius <= env('EXPAND_RADIUS_LIMIT','150')){
                    if((array)$dealers == 0){
                        $this->expandeRadius($lat, $lng,  $radius+ env('EXPAND_RADIUS_INCREMENT','25'));
                    }else{
                        return $dealers;
                    }
                }else{
                    return $dealers;
                }
       }else{
             return $dealers;
       }        
*/	   
        
        
    }

    public function filterByDealer(Request $request){ 
       

        try{
            $cache_attributes = md5('filterAttributes');
            if(Redis::exists($cache_attributes)){ 
               $collection = Redis::get($cache_attributes);
            }else{ 
              /*  $collection = DB::table('categories')
                        ->rightJoin('subcategories', 'categories.id', '=', 'subcategories.category_id')
                        
                        ->select('categories.id as catid',
                                DB::raw("(GROUP_CONCAT(subcategories.sid)) as `subid`"),
                                DB::raw("(GROUP_CONCAT(subcategories.title)) as `stitle`"),
                                DB::raw("(GROUP_CONCAT(categories.title)) as `ctitle`") 
                                )  
                                ->where('subcategories.parent_id','=',1) 
                                ->orWhereRaw ('subcategories.parent_id is NULL')
                        ->groupBy('catid')          
                        ->get()
                        ->toJson();
                        */
                        
                Redis::set($cache_attributes, $collection);
            }  
        }catch(\Exception $e){
            dd($e->getMessage());
        }  
            
            //return view('inventory.render.attributes',['collection'=>json_decode($collection, true)])->render(); 
             
       } 

       public function mysql_escape($inp){
           if(is_array($inp)) return array_map(__METHOD__, $inp);
           if(!empty($inp) && is_string($inp)){
            return str_replace(array('\\',"\0","\n","\r","'".'"',"\x1a"),array('\\\\','\\0','\\n','\\r','\\"',"\\'",'\\z'), $inp);

           }
           return $inp;
       }
	   
	    

        
}

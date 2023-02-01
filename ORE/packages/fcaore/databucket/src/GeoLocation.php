<?php namespace Fcaore\Databucket; 
/*
     |--------------------------------------------------------------------------
     | Geolocation Finder
     |--------------------------------------------------------------------------
     |
     | Geolocation finder by Zipcode and Latitude&Longtitude
     |  
     | Google Api key is need.
     | 
     | Developed By V2soft on 31-Dec-2018
     |
     */
use Fcaore\Databucket\Databucket; 
use Illuminate\Support\Facades\Redis;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Zipcode;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait GeoLocation
{  
    /**
     * Google API Key. This Api key should be free for limited access. 
	 * For uninterrupt access should be get paid version apikey.
     *
     * @return String.
     */
    protected function googleKey(){
        return config('databucket.GOOGLE_API_KEY');//'AIzaSyCwVVLqNxEtThgRPc__TkhnG30mttuiz20';
       
    }

    /**
     * find latitude and longitude by given zipcode using google service provider. 
     *
	 * @Params: Zipcode. String or Number.
	 *
     * @return Array.
	 *	Array's First Element: Status of the function.
	 *	Array's Second Element: Success then Latitude, Failure then status err message
	 *	Array's Third Element: Success then longitude, Failure then none.
	 *
     */
    public function getLatLonByZipcode($zip){   
	 	$array = array();
        if( Databucket::isCacheExists('cord:zip:latlan:'.$zip) ){
			$array[0] = 'success';
			$availzips = explode("@",Databucket::isCacheGet('cord:zip:latlan:'.$zip));			
			$array[1] = $availzips[0];
			$array[2] = $availzips[1]; 
			
			 return $array;
		}else{			
			$zip_latlon = \App\Zipcode::select('latitude','longitude')->where(['zipcode' => $zip])->get();
			if(!$zip_latlon->isEmpty()){
				foreach($zip_latlon as $key=>$val){
						$zipcode 	= $val['zipcode'];					 
						$latitude 	= number_format(str_replace('+-','-',$val['latitude']),2);
						$longitude 	= number_format($val['longitude'],2);					  
						if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
							Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
						} 				
					}
				$array[0] = 'success';			 	
				$array[1] = $latitude;
				$array[2] = $longitude;
				return $array;
			}

			/*
			* Get Lat Lng by zipcode on calling google service provider.
			*/
			$result = $this->findLatLonByZip($zip);
			if($result && $result['status'] == 'success'){
				$cordinates = $result['message'];
				$create_array = array();
				$create_array['zipcode'] = $zip;
				$create_array['latitude'] = '+'.$cordinates['lat'];
				$create_array['longitude'] = $cordinates['lng'];
				\App\Zipcode::insert($create_array);
				$latitude 	= number_format(str_replace('+-','-',$create_array['latitude']),2);
				$longitude 	= number_format($create_array['longitude'],2);					  
				if(!Databucket::isCacheExists('cord:zip:latlan:'.$zip)){
					Databucket::isCacheSet('cord:zip:latlan:'.$zip, $latitude.'@'.$longitude);
				}
				$array[0] = 'success';			 	
				$array[1] = $latitude;
				$array[2] = $longitude;
				return $array; 				
			}
		}
    }

    /**
     * find Zipcode by given latitude and longitude using google service provider. 
     *
	 * @Params: latitude and longitude.
	 *
     * @return Array.
	 *	Array's First Element: Status of the function.
	 *	Array's Second Element: Success then Zipcode, Failure then status err message 
	 *
     */
    public function findZipByLatLon($lat, $lon){  
       \Log::info('cord:latlan:zip:'.$lat.':'.$lon);
		 if( Databucket::isCacheExists('cord:latlan:zip:'.$lat.':'.$lon) ){
			$array['status'] = 'success';
			$array['message'] = Databucket::isCacheGet('cord:latlan:zip:'.$lat.':'.$lon);
			 return $array;
		}else{ 
			
				 $glink = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lon . "&sensor=false&key=".$this->googleKey();//.$googleKey;
 
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $glink);
				$res->getStatusCode(); 
				$res->getHeaderLine('content-type'); 
				$output = $res->getBody();  
				$result = json_decode($output, true);
			//	\Log::info($result);
				 
				if(Arr::has($result,'error_message')){
					$array['status'] = 'error';
					$array['message'] = $result['error_message'];
				}else if(Arr::has($result,'results')){
					if(count($result['results']) > 0){
						
						 
						$google_geo = [];
						 foreach($result['results'][0]['address_components'] as $key => $val){ 								   
								   if($val['types'][0] == 'postal_code' || $val['types'][0] == 'country'){
									   $google_geo[$val['types'][0]] = $val['short_name'];
									   $google_geo[$val['types'][0].'_long_name'] = $val['long_name']; 
								   }
						 }
						 
						 
						 if($google_geo['country'] == 'US'){
							 
							 $bbooleanValue = \Databucket::zipValidation($google_geo['postal_code']);
							if(!$bbooleanValue){
									 $array['status'] = 'error';
									$array['message'] = 'Invalid USA region ';
							}else{
								$array['status'] = 'success';
								//$array['message'] = $google_geo['postal_code'];
								
								$zips = $google_geo['postal_code'];
								$zips = trim($google_geo['postal_code'],'"');
								$zips = str_replace('"','',$zips);
								$array['message'] = $zips;
								  
								Databucket::isCacheSet('cord:latlan:zip:'.$lat.':'.$lon,$array['message']);
								//$db_zip_array['zipcode'] 	= '';
								//$db_zip_array['latitude'] 	= $lat;
								//$db_zip_array['longitude'] 	= $lon;
								//$db_zip_array['latlon_zip'] = $array['message'];
								//Zipcode::create($db_zip_array);
							}
							
						 }else{ 
							 $array['status'] = 'error'; 
							/* $zips = $google_geo['postal_code'];
								$zips = trim($google_geo['postal_code'],'"');
								$zips = str_replace('"','',$zips);
								$array['message'] = $zips;
								*/
								
							 $array['message'] = 'You are in '.$google_geo['country_long_name'].' but application only support on USA region only.';
						 } 
						
					}else{
						$array['status'] = 'error';
						$array['message'] = 'Cant fetch zipcode';
					}
				}else{
					$array['status'] = 'error';
					$array['message'] = 'Cant fetch zipcode';
				}				
				return $array; 
		}         
    }

    /**
     * find latitude and longitude by given Zipcode using google service provider. 
     *
	 * @Params: Zipcode
	 *
     * @return Array.
	 *	Array's First Element: Status of the function.
	 *	Array's Second Element: Success then latitude and longitude as array, Failure then status err message 
	 *
     */
    public function findLatLonByZip($zipcode){  
		$array = array();
		$glink = "https://maps.googleapis.com/maps/api/geocode/json?address=" .$zipcode."&sensor=false&key=".$this->googleKey();//.$googleKey;

		$client = new \GuzzleHttp\Client();
		$res = $client->request('GET', $glink);
		$res->getStatusCode(); 
		$res->getHeaderLine('content-type'); 
		$output = $res->getBody();  
		$result = json_decode($output, true);
	//	\Log::info($result);
		 
		if(Arr::has($result,'error_message')){
			$array['status'] = 'error';
			$array['message'] = $result['error_message'];
		}else if(Arr::has($result,'results')){
			if(count($result['results']) > 0){				 
				$google_geo = [];
				foreach($result['results'][0]['address_components'] as $key => $val){ 								   
				   if($val['types'][0] == 'postal_code' || $val['types'][0] == 'country'){
					   $google_geo[$val['types'][0]] = $val['short_name'];
					   $google_geo[$val['types'][0].'_long_name'] = $val['long_name']; 
				   }
				 }
				$google_geo['location'] = $result['results'][0]['geometry']['location'];
			 
				if($google_geo['country'] == 'US'){
					$array['status'] = 'success';
					$array['message'] = $google_geo['location'];				
				 }else{ 
					 $array['status'] = 'error'; 
					 $array['message'] = 'You are in '.$google_geo['country_long_name'].' but application only support on USA region only.';
				 }				
			}else{
				$array['status'] = 'error';
				$array['message'] = 'Cant fetch zipcode';
			}
		}
		return $array;          
    }

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles   
/*::    'raduis' : Radius nearest                                                                    :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
public function distance($lat1, $lon1, $lat2, $lon2, $unit) {
    $lat2 = (float)$lat2;
    $lon2 = (float)$lon2;

  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else { 
    $theta = $lon1 - $lon2;
    
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);
   
    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}

public function distanceCalculation($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
	// Calculate the distance in degrees
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));
 
	// Convert the distance in degrees to the chosen unit (kilometres, miles or nautical miles)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 degree = 111.13384 km, based on the average diameter of the Earth (12,735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 degree = 69.05482 miles, based on the average diameter of the Earth (7,913.1 miles)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 degree = 59.97662 nautic miles, based on the average diameter of the Earth (6,876.3 nautical miles)
	}
	return round($distance, $decimals);
}


}

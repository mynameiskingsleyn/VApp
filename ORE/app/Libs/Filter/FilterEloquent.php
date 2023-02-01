<?php
//namespace App\Libs\Filter;

use App\Libs\Car\FilterInterface;
use App\Dealer;
use App\Vehicle;

class FilterEloquent implements FilterInterface {
    protected $dealer, $vehicle, $today;

    function __construct(Dealer $dealer, Vehicle $vehicle) {
        $this->dealer = $dealer;
        $this->vehicle = $vehicle;
        $this->today = substr(Carbon::today('America/Vancouver'), 0 ,10);
    }
    public function getAllDealer(){
        return $this->dealer->all();
    }
    public function getAllVehicle(){
        return $this->vehicle->all();
    }

    /**
     * Get the Geolocation by Navigator
     *
     * @request array 
     * @return array
     */
    public function geoLocation($request){
        $lat = $request->get('lat');
        $lon = $request->get('lon');
        $googleKey= env("GOOGLE_APIKEY", "");  
        
        $glink = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lon . "&sensor=false";//&key=".$googleKey;//.$googleKey;

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $glink);
        $res->getStatusCode(); 
        $res->getHeaderLine('content-type');
        $output = $res->getBody();  
        $result = json_decode($output, true);
        if(count($result['results']) > 0){
            $array = $result['results'][0]['address_components']; 
        }else{
            $array = array();
        }

        return $array;
    }
}
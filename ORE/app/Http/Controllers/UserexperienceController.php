<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis; 
use App\Facades\OreDSClass;   
use Illuminate\Support\Arr;
use Illuminate\Support\Str;  

use App\Leadsession;
use App\Stage;
use App\Lead;
use DB;

class UserexperienceController extends Controller
{
    private $Stage;		
	private $Leadsession;
	
	public function __construct(Stage $Stage, Leadsession $Leadsession)
    {  
		 $this->stage = $Stage;
		 $this->leadsession = $Leadsession;
    }
	
	
	 /**
     * Data Component: Vehicle
     *
     * @return JsonArray
     */
    public function vehicle(Request $request){          
		$this->update_pageinfo($request->get('curPage')); 
        $vin = $request->get('vin');
        $params_make = $request->get('params_make');
        $params_vechType = $request->get('params_vechType'); 
        $params_year = $request->get('params_year'); 
        $params_model = $request->get('params_model');         
      $data = \Ore::vehicleDataComponent($vin, $params_make, $params_vechType,$params_year,$params_model); 
        $outCache = \Ore::JsonManager('userinfo','VehicleInfo','addJson', $data); 
        $outCacheMerge = $this->merge($outCache);  
		 	
		\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'vehicle_info',$outCacheMerge);
        return $outCacheMerge;
    }
	
		/**
		 * Data Component: Vehicle
		 *
		 * @return JsonArray
		 */
		public function tradein(Request $request){
			$data = $request->all();   
			\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'tradein',json_encode($data));
			$outCache = \Ore::JsonManager('userinfo','TradeIn','addJson', $data);
			return $outCache;
		}

		 /**
		 * Data Component: Vehicle
		 *
		 * @return JsonArray
		 */
		public function service_protection(Request $request){
			$lease = $request->get('lease');
			$finance = $request->get('finance');
			$vin = $request->get('vin');
			$data = json_encode( ['lease' => $lease, 'finance' => $finance,'vin'=>$vin]); 
		  
			$service_protection = json_decode(\Ore::serviceJson(), true);

			$service_protection=$this->serviceprotectiondata();
		   // dd($service_protection ,$dataset);
			$toCacheMophar = 'Customer is interested in following Service & Protection plans:-  ';
			if(count($lease) > 0){
					//$toCacheMophar .= '';
					foreach($lease as $key => $val){
						$toCacheMophar .= 'Lease - '.$service_protection['lease'][$val]->package_name.',';
					}
					$toCacheMophar = trim($toCacheMophar, ',');
			}
			
			if(count($finance) > 0){
					$toCacheMophar .= ',';
					foreach($finance as $key => $val){
						$toCacheMophar .= 'Finance - '.$service_protection['finance'][$val]->package_name.',';
					}
					$toCacheMophar = trim($toCacheMophar, ',');
			}
			// dd($toCacheMophar);
			\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'mophar',$toCacheMophar);
			$outCache = \Ore::JsonManager('userinfo','Service','addJson', $data);
			 
		} 

	public function serviceprotectiondata()
	{

		$serviceprotectionCachkey = \Databucket::makeCache('serviceprotectiondata:' );
		if (!\Databucket::isCacheExists($serviceprotectionCachkey)) {
			$qry = \DB::table('fca_ore_mopar_plans')->where('varient', 'plan')->get();
			\Databucket::isCacheSet($serviceprotectionCachkey, json_encode($qry));
		} else {
			$qry = \Databucket::isCacheGet($serviceprotectionCachkey);
			$qry = json_decode($qry);
		}

		if (!empty($qry))

			$finance = [];
		$lease = [];
		foreach ($qry as $key => $value) {
			if ($value->finance == 1) {
				$finance[$value->id] = $value;
			}

			if ($value->lease == 1) {
				$lease[$value->id] = $value;
			}
		}
		 

		return array('finance' => $finance, 'lease' => $lease);
	}
		
		//updating current page info
		public function update_pageinfo($curPage){
			$data=json_decode(json_encode(array("curPage"=>$curPage))); 
			$testCache=\Ore::JsonManager('userinfo','PageInfo','addJson',$data);
			return 1;
		}
		
		//load previous session
		public function load_prev_session(Request $request){
			$data=$request->all();
			$getData =\Ore::JsonManager('userinfo','','addCookieJson',$data);
		 
			return $getData;		
		}
		
		
		public function merge($outCache){
				$array = json_decode($outCache, true);
				$array['userinfo']['dealerInfo'] = [];

				if(array_key_exists('VehicleInfo',$array['userinfo'])){
					$VehicleInfoArray = json_decode($array['userinfo']['VehicleInfo'], true);  
				   $dealerCode =  $VehicleInfoArray['dealer_code'];
				   if($dealerCode!=""){
						$array['userinfo']['dealerInfo'] = \Databucket::dealerInfoByDealerCode($dealerCode) ;
				   }
				} 
				return json_encode($array);
		}
}

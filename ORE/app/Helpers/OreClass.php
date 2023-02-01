<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Redis;
use File;  
use Carbon\Carbon;
use App\Dealer; 
use App\Catvehicle;
use App\Vehicle;
use App\Leadsession;
use App\Stage;
use App\Lead;
use App\Prelead;
use App\MerkleOffline;
use App\MoparPlans;
use DB;
use Cache;
use App\Helpers\CalculatorMagic;
use Fcaore\Databucket\Facade\Databucket;
use Fcaore\Databucket\JsonQueriable;
use Fcaore\Databucket\OreQueriable;
use Fcaore\Databucket\CacheValidator;
use Fcaore\Databucket\SqlQueries;
use Fcaore\Databucket\GeoLocation;
use Session;
use Illuminate\Support\Arr;
use App\Dealereliminate;	
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Storage;

class OreDSClass {

    protected $cache;
    protected $currentSession; 
    private $session_id;
    private $service_protection;
    public $data = array();
    private $isActive_dlr_discount; 
    use JsonQueriable;
    use OreQueriable;
    use CacheValidator;
    use SqlQueries;
    use GeoLocation;

    public function getUserImage($path) {
        if(!File::exists($path)){
            $path = '/image/default.png';
        }
    }
    
    public function getSessionID(){
        return $this->session_id = Session::getId();
    }

    public function cacheGet($CacheName){
        return Redis::get($CacheName);
    }

    public function cacheSet($CacheName, $value){
        return Redis::set($CacheName, $value);
    }

  /* Service and Protection */
    public function serviceJson(){
       //$this->service_protection = $this->getOutlineStructure('service');
       $this->service_protection = '
       {
        "lease":{
            "1":{
                "toptext":"",
                "title":"Road Hazard Tire & Wheel Protection",
                "subtitle":"",
                "desc":"This one-of-a-kind plan provides full repair or replacement coverage for all four tires and wheels damaged due to road hazard conditions plus cost of mounting, balancing and stems for both original equipment manufacturers and aftermarket tires and wheels."			
            },
            "2":{
                "toptext":"",
                "title":"Lease Wear & Tear",
                "subtitle":"",
                "desc":"Provides protection against excess wear and tear damage on your lease vehicle that you would otherwise be responsible to cover the cost of repairs. With a $0 deductible, excess wear and tear charges are covered up to $5,000."			
            },
            "3":{
                "toptext":"",
                "title":"Auto Appearance Care",
                "subtitle":"",
                "desc":"Covers dents and dings on your vehicle’s exterior surfaces. Repairs can be performed on any size dent and ding on metal panels where the paint has not been broken and the dent can be completely repaired using the PDR process (as determined by the field technician)."			
            },
            "4":{
                "toptext":"",
                "title":"Scheduled Maintenance",
                "subtitle":"",
                "desc":"Exclusive to Alfa Romeo, this offering is designed to keep your vehicle perfectly maintained and stunningly beautiful during your entire lease experience. Includes Road Hazard Tire & Wheel, Auto Appearance Care, Key fob and by the book factory required maintenance."			
            },
            "5":{
                "toptext":"",
                "title":"Premium Care",
                "subtitle":"",
                "desc":"Exclusive to Alfa Romeo, this offering is designed to keep your vehicle perfectly maintained and stunningly beautiful during your entire lease experience. Includes Road Hazard Tire & Wheel, Auto Appearance Care, Key fob and by the book factory required maintenance."			
            },
            "6":{
                "toptext":"",
                "title":"Scheduled Maintenance",
                "subtitle":"",
                "desc":"Provides most scheduled and recommended maintenance services as described in the owner\'s manual. Available with a variety of terms with oil changes at 10,000 mile intervals."			
            },
			"7":{
                "toptext":"",
                "title":"Premium Care",
                "subtitle":"",
                "desc":"Exclusive to Alfa Romeo, this offering is designed to keep your vehicle perfectly maintained and stunningly beautiful during your entire lease experience. Includes Road Hazard Tire & Wheel, Auto Appearance Care, Key fob and by the book factory required maintenance."			
            }
        },
        "finance":{
            "1":{
                "toptext":"",
                "title":"Maximum Care",
                "subtitle":"",
                "desc":"Factory-Backed mechanical protection beyond the factory warranty on over 5,000 components. If it\'s mechanical, it\'s covered. Available with a variety of terms and deductibles. Service provided by certified, expert technicians. Also includes car rental allowance, towing, roadside assistance and trip interruption coverage."			
            },
            "2":{
                "toptext":"",
                "title":"Scheduled Maintenance",
                "subtitle":"",
                "desc":"Provides most scheduled and recommended maintenance services as described in the owner\'s manual. Available with a variety of terms with oil changes at 10,000 mile intervals."			
            },
            "3":{
                "toptext":"",
                "title":"Road Hazard Tire & Wheel Protection",
                "subtitle":"",
                "desc":"This one-of-a-kind plan provides full repair or replacement coverage for all four tires and wheels damaged due to road hazard conditions plus cost of mounting, balancing and stems for both original equipment manufacturers and aftermarket tires and wheels."			
            },
            "4":{
                "toptext":"",
                "title":"Auto Appearance Care",
                "subtitle":"",
                "desc":"Covers dents and dings on your vehicle’s exterior surfaces. Repairs can be performed on any size dent and ding on metal panels where the paint has not been broken and the dent can be completely repaired using the PDR process (as determined by the field technician)."			
            },
            "5":{
                "toptext":"",
                "title":"GAP Plans",
                "subtitle":"",
                "desc":"Guaranteed Automotive Protection (GAP) picks up where your Automobile insurance falls short by bridging the \"financial gap\" between what you owe on your vehicle and what your insurance will pay."			
            },
            "6":{
                "toptext":"",
                "title":"Scheduled Maintenance",
                "subtitle":"",
                "desc":"Provides most scheduled and recommended maintenance services as described in the owner\'s manual. Available with a variety of terms with oil changes at 10,000 mile intervals."			
            }
        }
        
    } ';
	
	return $this->service_protection;
    }

    public function serviceCompare($pLease, $pFinance,$cSession){
	  //dd($pLease, $pFinance);
	  //dd($this->serviceprotectionjson());	
	  $this->serviceJson();
       $serviceArray = json_decode($this->service_protection, true); 
	   // dd( $serviceArray,$this->serviceprotectionjson());
	   $serviceArray=$this->serviceprotectionjson($cSession);
       $lease = $finance = array(); 
		
	if(Arr::has($serviceArray, 'lease')) $lease = $serviceArray['lease'];	
	if(Arr::has($serviceArray, 'finance')) $finance = $serviceArray['finance'];
      // if(array_key_exists("lease", $serviceArray))  $lease = $serviceArray['lease']; 
       //if(array_key_exists("finance", $serviceArray))  $finance = $serviceArray['finance']; 

       $serviceResponse = array();
       if( count($pLease) > 0)
       $serviceResponse['lease'] = array_intersect_key($lease,array_flip($pLease));
       if( count($pFinance) > 0)
       $serviceResponse['finance'] = array_intersect_key($finance,array_flip($pFinance));
       
       return $serviceResponse;
	}
	
	public function serviceprotectionjson($cSession)
	{

		$serviceprotectionCachkey = \Databucket::makeCache('serviceprotectiondata:'.$cSession );
		if (!\Databucket::isCacheExists($serviceprotectionCachkey)) {
			$qry = MoparPlans::where('varient', 'plan')->get();
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
				$finance[$value->id]['toptext'] ='';  
				$finance[$value->id]['title'] =$value->package_name;  
				$finance[$value->id]['subtitle'] ='';  
				$finance[$value->id]['desc'] =$value->package_description;
				
			}

			if ($value->lease == 1) {
				$lease[$value->id]['toptext'] ='';  
				$lease[$value->id]['title'] =$value->package_name;  
				$lease[$value->id]['subtitle'] ='';  
				$lease[$value->id]['desc'] =$value->package_description;
			}
		}
		 

		return array('finance' => $finance, 'lease' => $lease);
	}


    /* END SERVICE and PROTECTION */

    public function cacheValidate($CacheName){
        if(Redis::exists($CacheName)) return true; else return false;
    }

    public function JsonManager($templateName, $attributeName, $action, $data){  
        // Template Name, Attribute name, Action, Data
        $this->getSessionID();
        return $this->CacheManager($templateName,$attributeName,$action,$data);
    }

    public function CacheManager($jsonOutlineTemplate,$attribute, $services, $data, $update=""){
        if($services == 'addJson'){           
            try{  
			$templateCacheName = md5($jsonOutlineTemplate.$this->session_id); 
			 
            if($this->cacheValidate($templateCacheName)){         
                $jsonAssocArray = json_decode($this->cacheGet($templateCacheName), true); 
                $jsonData = json_decode(json_encode($jsonAssocArray), true);  
                $jsonData[$jsonOutlineTemplate][$attribute] = $data;  
             }else{                  
                $jsonAssocArray =  $this->getOutlineStructure($jsonOutlineTemplate);               
                $jsonData = json_decode($jsonAssocArray, true);     
                $jsonData[$jsonOutlineTemplate][$attribute] = $data;  
             }
            }catch(\Exception $e){ 
                dd($e);
            }
            $encodeCache = json_encode($jsonData);  
            $this->cacheSet($templateCacheName,  $encodeCache);
            return $encodeCache; 
			
        }else if($services == 'addCookieJson'){
			try{  
				$templateCacheName = md5($jsonOutlineTemplate.$this->session_id);                   
            }catch(\Exception $e){ 
                dd($e);
            }
            $encodeCache = json_encode($data);  
            $this->cacheSet($templateCacheName,  $encodeCache);
            return $encodeCache;
		}         
    }	
	
    public function getOutlineStructure($templateName){
        $structure = [];
        if(!$this->cacheValidate($templateName)){  
            $filePath =storage_path('app\\public\\skeleton\\json\\'.$templateName.'.json');
            try{ 
                    if ( file_exists($filePath) ){
                        $oStruct = file_get_contents($filePath); 
                        $structure = json_decode($oStruct, true); 
                    } 
                    $structure = json_encode($structure);
                    $this->cacheSet($templateName, $structure);
            }catch(\Exception $e){ 
                dd("Error: File not available"); 
            }  
        }else{
            $structure = $this->cacheGet($templateName); 
        } 
        return $structure; 
    } 

    public function vehicleDataComponent($params_vin, $params_make, $params_vechType,$params_year,$params_model){
        try{  
             $encryptCacheName = $params_vin;   
            if($this->cacheValidate($encryptCacheName)){    
                $vehicleDetails = $this->cacheGet($encryptCacheName); 
                return $vehicleDetails;
            }else{  
                $vehicleDetails = Databucket::getVinInfo($params_vin, $params_make, $params_vechType,$params_year,$params_model);
                $this->cacheSet($encryptCacheName, json_encode($vehicleDetails));
                
                return  json_encode($vehicleDetails);  
            } 
        }catch(\Exception $e){
           // dd($e);
        }    
    } 

    private function CalcParams($params, $name, $value=""){
      return  isset($params[$name]) ? $params[$name] : $value;
    }

    /*
    *   Finance Calculator
    *
    *   @Params<array> : msrp, features, destination, downpayment, tradein
    *                    dealerdiscount, incentives, n, rate
    *   
    *   @Response<float>: value
    */
    public function financeCalculator($params){
         
        // Params
            $msrp = $this->CalcParams($params, 'msrp', 43895);
            $addedFeatures = $this->CalcParams($params, 'features', 0); 
            $destination = $this->CalcParams($params, 'destination', 1395);
            $downPayment = $this->CalcParams($params, 'downpayment', 4529);
            $tradeInValue =$this->CalcParams($params, 'tradein', 0);
            $dealerDiscount=$this->CalcParams($params, 'dealerdiscount', 0);
            $incentives=$this->CalcParams($params, 'incentives', 5000);
            $N = $this->CalcParams($params, 'n', 60);                // Term (N)
            $rate_apr = $this->CalcParams($params, 'rate', 3.84);       // Rate           
            
            // Pre-Calculation            
            $vehiclePrice = $msrp + $addedFeatures;
            // Estimate Price (P)  
            $P = ($vehiclePrice+$destination)-($downPayment+$tradeInValue+$dealerDiscount+$incentives);
            $I= $rate_apr/1200; // Interest (I)
        
        // Monthly Calculation
            $apr =($P*$I*pow((1+$I),$N)/(pow((1+$I),$N) -1)); 

            return $apr;
    }

    /*
    *   LEASE CALCULATOR
    *
    *   @Params<array> : dealerDiscount,destination,downpayment,othercapcosts,rate
    *             residual, tradein,msrp,incentives,term,yearlymileage
    *   @Response<float>: value
    */
    public function leaseCalculator($params){ 
            $dealerDiscount	= $this->CalcParams($params, 'dealerDiscount', 0);
            $destination	= $this->CalcParams($params, 'destination', 1395);
            $downPayment	= $this->CalcParams($params, 'downpayment', 2198);
            $otherCapCosts	= $this->CalcParams($params, 'othercapcosts', 0);
            $rate	        =  $this->CalcParams($params, 'rate', 0.168);
            $residual	    = $this->CalcParams($params, 'residual', 51);
            $tradeInValue	= $this->CalcParams($params, 'tradein', 0);
            $MSRP	        = $this->CalcParams($params, 'msrp', 43895);
            $Incentives	    = $this->CalcParams($params, 'incentives', 5000);
            $term	        = $C12  =$this->CalcParams($params, 'term', 36);
            $YearlyMileage	= $this->CalcParams($params, 'yearlymileage', 10000);

            
            $vehiclePrice	= $MSRP - $Incentives;
            $pv     = $C21  = ($vehiclePrice + $destination + $otherCapCosts) - ($downPayment + $tradeInValue + $dealerDiscount);
            $fv     = $C22  = ($residual/100)*($vehiclePrice + $destination + $otherCapCosts);
            $A      = $C23  = 100;
            $i      = $C24  = $rate/1200; 

            return ($C21-($C22*pow( (1+$C24),(-1*$C12) )))/(((1- pow((1+$C24),(-1*($C12-$C23))) )/$C24)+$C23);
 
    }

    public function serviceArraychecker($JsonToArray, $serviceType){

        //if(array_key_exists($serviceType,$JsonToArray['userinfo'])){
		if(Arr::has($JsonToArray['userinfo'], $serviceType)){
			
          if(!is_array($JsonToArray['userinfo'][$serviceType])) {
              return json_decode($JsonToArray['userinfo'][$serviceType] , true); 
           }else return $JsonToArray['userinfo'][$serviceType];
        } else{
          $JsonToArray['userinfo'][$serviceType] = [];
        }   
      }
	 /********************* INBOUNDS **********************************************/
	  /**
	  *     Totally 4 inbounds operation
	  *		Routeone
	  *		700Credit
	  * 	Carnow
	  *		Ore Experience
	  */
	  
	  
	  
	  /* 
	  * Method Name: Ore User Experience
	  * Method Desc: ORE user experience form data mapping with array in Table coloum index 
	  * Date: 24-4-2019
	  *  
	  * @request ore_session<string>, user<string>, tradeIn<string>
	  *  	  
	  * @return data<array>
	  */
	  
	  public function ore_user_experience($session_id, $user, $tradeIn, $vehicle, $leadServices, $lead_id, $types){
		
		$v_arr = $vehicle; 
		
		if($types=='auto') {
			$cacheVerifiedSessionID =  $session_id; 
			$session_id = \Ore::getRefSessionID('ore_session',$cacheVerifiedSessionID);
		}else{ 
			$cacheVerifiedSessionID =  \Ore::getSessionID();
		} 
		
		$json_calc = array();
		$dealerid = $dealername = "";
		try{
		if(Arr::has($v_arr, 'vin')){
				if(!\Databucket::isCacheExists($v_arr['vin'])){  
						$datass = \App\Vehicle::where(['vin' => $v_arr['vin']])->first(); 
						$val1 = (object)$datass;
						 if(\Databucket::hexists($val1->dealer_code, 'dealer_code')){
							$dealerid = $val1->dealer_code;
							$dealername =\Ore::dealerFetchByID($dealerid);
						 } 
				}else{
					$datass = \Ore::isCacheGetAll($v_arr['vin']); 
					$dealerid = $datass['dealer_code'];
					$dealername =\Ore::dealerFetchByID($dealerid);
				}
		}else{
			if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'vehicle_info') ){ 
					$json_calc32 = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'vehicle_info');
					$json_calc33 =	json_decode($json_calc32[0], true); 	
						$datass = \Ore::isCacheGetAll($json_calc33['vin']); 
						$dealerid = $datass['dealer_code'];
						$dealername =\Ore::dealerFetchByID($dealerid);
					$vehicle['vin'] = $json_calc33['vin'];
			} 		
		} 
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator') ){ 
			$json_calc = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator');
			$json_calc = json_decode($json_calc[0], true); 
			 
		}

		$estimated_owed = 0;
        $tradein_price = 0;
        if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein') ){ 
            $json_tradein = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein');  
            $array_tradein = json_decode($json_tradein[0], true);
            $tradein_price = \Ore::is_array_key($array_tradein,'price');
            $tradein_remainValue = \Ore::is_array_key($array_tradein,'remainingvalue');
            if(!empty($tradein_price)){
                $tradein_price = str_replace('$', '', $tradein_price);
                $tradein_price = str_replace(',', '', $tradein_price);
                $tradein_price = intval($tradein_price);
                $estimated_owed = !empty($tradein_remainValue) ? ($tradein_price - $tradein_remainValue) : $estimated_owed;
            }
        }
 
		 }catch(\Exception $e){
			\Log::info(" == JSON CALC issues so catch blocked ==");	 
		}
		 
		try{  
		
				/*********  Price Comments Attibute - Entire Payment Calculation ******************/
				$pricecomments = '';
				 
				$calc_type=  Arr::has($json_calc, 'type') ?  Arr::get($json_calc,'type') : "";
				$calc_msrp=  Arr::has($json_calc, 'xmlvariables.msrp_results') ?  Arr::get($json_calc,'xmlvariables.msrp_results') : "";
				$calc_downpayment=  Arr::has($json_calc, 'downpayment') ?  Arr::get($json_calc,'downpayment') : (Arr::has($json_calc, 'xmlvariables.developer.arraybuilder.cashDown') ?  Arr::get($json_calc,'xmlvariables.developer.arraybuilder.cashDown') : 0);                
				$calc_tradein=  Arr::has($json_calc, 'xmlvariables.developer.arraybuilder.tradeInValue') ?  Arr::get($json_calc,'xmlvariables.developer.arraybuilder.tradeInValue') : 0;
				
				$calc_incentive=  Arr::has($json_calc, 'xmlvariables.incentiveNames.0') ?  Arr::get($json_calc,'xmlvariables.incentiveNames.0') : "";
				$calc_incentiveamount=  Arr::has($json_calc, 'xmlvariables.incentiveAmount') ?  Arr::get($json_calc,'xmlvariables.incentiveAmount') : "";
				//$calc_rebateDetails=  Arr::has($json_calc, 'xmlvariables.rebateDetails') ?  Arr::get($json_calc,'xmlvariables.rebateDetails') : "";
				
				$calc_rebateDetailsfinalamount=0;
				
				 
				/*********************/
 				 $calc_rebateDetailsfinalamount +=  Arr::has($json_calc, 'xmlvariables.rebateDetailsfinalamount') ?  Arr::get($json_calc,'xmlvariables.rebateDetailsfinalamount') : 0;
 				//$calc_rebateDetailsfinalamount +=  Arr::has($json_calc, 'xmlvariables.incentivesBonusCash_amount') ?  Arr::get($json_calc,'xmlvariables.incentivesBonusCash_amount') : 0;

				/***********************/
				$calc_rebateDetailsfinalamount +=  Arr::has($json_calc, 'xmlvariables.dlrDiscAmount') ?  Arr::get($json_calc,'xmlvariables.dlrDiscAmount') : 0;
				
				$calc_total=  Arr::has($json_calc, 'total') ?  Arr::get($json_calc,'total') : "";
				$calc_terms=  Arr::has($json_calc, 'comments') ?  Arr::get($json_calc,'comments') : "";
				$calc_monthlypayment=  Arr::has($json_calc, 'monthly') ?  Arr::get($json_calc,'monthly') : "";

				$calc_rebateDetailsid=  Arr::has($json_calc, 'xmlvariables.rebateDetailsid') ?  Arr::get($json_calc,'xmlvariables.rebateDetailsid') : "";
				$rebateDetailsid = explode(",",$calc_rebateDetailsid);
				//$results = $calc_incentive.' - Incentive Value $'.$calc_incentiveamount.' ,';				
				
				$results  = '';
				$AllIncentiveIds=  Arr::has($json_calc, 'xmlvariables.AllIncentiveIds') ?  Arr::get($json_calc,'xmlvariables.AllIncentiveIds') : "";
                if(Arr::has($json_calc, 'xmlvariables.incentiveNames')){
    				foreach ($json_calc['xmlvariables']['incentiveNames'] as $key => $val) { 
    					if(array_key_exists($key,$json_calc['xmlvariables']['incentiveNames']) && array_key_exists($key,$json_calc['xmlvariables']['man_incentives_sin_name'])){
    						$results .= $json_calc['xmlvariables']['incentiveNames'][$key].' Incentive Value $'. $json_calc['xmlvariables']['man_incentives_sin_name'][$key].', ';								
    					}
    				}
                }

				//\Log::info($json_calc['xmlvariables']['explores']);

                if(Arr::has($json_calc, 'xmlvariables.incentivesBonusCashList')){
                    foreach ($json_calc['xmlvariables']['incentivesBonusCashList'] as $key => $val) {   
					    $results .= $key; 
						if(Arr::has($val, 'discount')){
								$results .= ' Amount $'.$val['discount'].' ProgramID-'.$val['program_id'];								
						}
						$results .= ', ';						
                    } 
                } 
					
				if(Arr::has($json_calc, 'xmlvariables.explores')){
					foreach ($json_calc['xmlvariables']['explores'] as $key => $val) {  
							if(in_array($val['ids'], $rebateDetailsid)){ 
								$results .= $val['name'][0].' Incentive Value $'.$val['amount'].', ';
							} 
					} 
				}              
				
				if(Arr::has($json_calc, 'xmlvariables.dlrDiscCmts')){
					$results .= $json_calc['xmlvariables']['dlrDiscCmts'];
				}

				
				$pricecomments  = ''.'Type: '.$calc_type.' | '.'MSRP: $'.$calc_msrp.' | '.'Trade-in Value: $'.$calc_tradein.' | '.$results.' Total Incentives Amount: $'.$calc_rebateDetailsfinalamount.' | '; 
              
				if($estimated_owed > 0){
                    $pricecomments  = ''.'Type: '.$calc_type.' | '.'MSRP: $'.$calc_msrp.' | '.'Trade-in Value: $'.$tradein_price.' | '.'Estimate owed: $'.$estimated_owed.' | '.$results.' Total Incentives Amount: $'.$calc_rebateDetailsfinalamount.' | '; 
                }

				/* Additional Offer Attached */
				if (config('ore.calc.additioanal_offer')) {
					if(Arr::has($json_calc, 'xmlvariables.additional_discount')){
							$pricecomments  .= 'Additional Discounts: $'.$json_calc['xmlvariables']['additional_discount']. ' | ';
					}					
				}	
				
				if(Arr::has($json_calc, 'type')){   
                    $isActive_dlr_discount = config('ore.calc.dealer_discount');  
					if($json_calc['type'] == 'Lease')
					   {
							$calc_milesPerYear=  Arr::has($json_calc, 'xmlvariables.developer.arraybuilder.milesPerYear') ?  Arr::get($json_calc,'xmlvariables.developer.arraybuilder.milesPerYear') : "";
							$calc_dealer_disc=  Arr::has($json_calc, 'xmlvariables.dealer_disc') ?  Arr::get($json_calc,'xmlvariables.dealer_disc') : 0;
                            /*
                             * Discount into lead
                            */
                            $dealer_discount_text = 'Dealer Discount: $0';
                            if($isActive_dlr_discount){
                                $result = $this->dealerDiscountCalculationforVin($datass['vin'],'Lease');
                                if(!empty($result)){
                                    $dealer_discount_text = !empty($result['discount_text']) ? $result['discount_text'] : $dealer_discount_text;
                                    $calc_dealer_disc = !empty($result['total_discount_amount']) ? $result['total_discount_amount'] : $calc_dealer_disc;                                    
                                }
                            }	
                            /*
                             * Discount into lead end
                            */
							// if($calc_dealer_disc!='')
							// {
							// 	$calc_downpayment = (int)$calc_downpayment - (int)$calc_dealer_disc;
							// }
							
								if(Arr::has($json_calc, 'xmlvariables.original_downpayment')){
									$calc_downpayment=  Arr::has($json_calc, 'xmlvariables.original_downpayment') ?  Arr::get($json_calc,'xmlvariables.original_downpayment') : 0; 
								}
							
							$pricecomments .= ''.'Down Payment: $'.$calc_downpayment.' | '.$dealer_discount_text.' | '.'Adjusted Capitalized Costs: $'.$calc_total.' | '.'Annual Mileage: '.$calc_milesPerYear.' | '.''.$calc_terms.' | '.'Monthly Payment: $'.$calc_monthlypayment;
					   }
					   elseif($json_calc['type'] == 'Finance')
					   {			
							$calc_f_dealer_disc=  Arr::has($json_calc, 'xmlvariables.f_dealer_disc') ?  Arr::get($json_calc,'xmlvariables.f_dealer_disc') : "";
                            /*
                             * Discount into lead
                            */
                            $dealer_f_discount_text = 'Dealer Discount: $0';
                            if($isActive_dlr_discount){
                                $result = $this->dealerDiscountCalculationforVin($datass['vin'],'Finance');
                                if(!empty($result)){
                                    $dealer_f_discount_text = !empty($result['discount_text']) ? $result['discount_text'] : $dealer_f_discount_text;
                                    $calc_f_dealer_disc = !empty($result['total_discount_amount']) ? $result['total_discount_amount'] : $calc_f_dealer_disc;                                    
                                }
                            }
                            /*
                             * Discount into lead end
                            */
							// if($calc_f_dealer_disc!='')
							// {
							// 	$calc_downpayment = $calc_downpayment - $calc_f_dealer_disc;
							// }
							if(Arr::has($json_calc, 'xmlvariables.original_downpayment')){
									$calc_downpayment=  Arr::has($json_calc, 'xmlvariables.original_downpayment') ?  Arr::get($json_calc,'xmlvariables.original_downpayment') : 0; 
								}
							$pricecomments  .= ''.'Down Payment: $'.$calc_downpayment.' | '.$dealer_f_discount_text.' | '.'Adjusted Capitalized Costs: $'.$calc_total.' | '.''.$calc_terms.' | '.'Monthly Payment: $'.$calc_monthlypayment;
					  }
					  else
					  {	
                        $calc_c_dealer_disc=  Arr::has($json_calc, 'xmlvariables.dealer_disc') ?  Arr::get($json_calc,'xmlvariables.dealer_disc') : 0;
                        /*
                         * Discount into lead
                        */
                        $dealer_c_discount_text = 'Dealer Discount: $0';
                        if($isActive_dlr_discount){
                            $result = $this->dealerDiscountCalculationforVin($datass['vin'],'Cash');
                            if(!empty($result)){
                                $dealer_c_discount_text = !empty($result['discount_text']) ? $result['discount_text'] : $dealer_c_discount_text;
                                $calc_c_dealer_disc = !empty($result['total_discount_amount']) ? $result['total_discount_amount'] : $calc_c_dealer_disc;                                    
                            }
                        }   
                        /*
                         * Discount into lead end
                        */			    
						$pricecomments .= $dealer_c_discount_text.' | Est. Net Price:'.$calc_total;				   
					  }
				}else{
					$pricecomments .= "";
				}

		}catch(\Exception $e){
            \Log::info($e->getMessage());    
            \Log::info($e->getLine());    
			\Log::info(" == Price Comments issues so catch blocked ==");	
			$pricecomments = "";
		}
		/*********  Price Comments Attibute - Entire Payment Calculation ******************/
			
		$setTime = Carbon::now('America/New_York');
		
		$data['session_id'] = $session_id; 
		$data['source_id'] = config('ore.ore_sourceid');
		$data['lead_id'] = $lead_id;
		//$data['lead_source'] =  \Ore::is_array_key( $user,'source');
		//$data['lead_source'] = 'ORE';
		$data['lead_source'] = \Ore::is_array_key($user,'current_tier_value');
		
		//Personal
		$data['first_name'] = \Ore::is_array_key($user,'first');
		$data['last_name'] = \Ore::is_array_key($user,'last');
		$data['email'] =  \Ore::is_array_key($user,'contact_email');
		$data['phone'] = \Ore::is_array_key($user,'contact_phone');  
		$data['streetline1'] = \Ore::is_array_key($user,'streetline1'); 
		$data['streetline2'] = \Ore::is_array_key($user,'streetline2');
		$data['apartment'] = \Ore::is_array_key($user,'apartment');		
		$data['city'] =  \Ore::is_array_key($user,'city');
		$data['state'] =  \Ore::is_array_key($user,'regioncode');
		$data['zip'] = \Ore::is_array_key($user,'postalcode');
		$data['comments'] = $leadServices;
		
		
		$veh_data = \Ore::isCacheGetAll(\Ore::is_array_key($vehicle,'vin')); 
		$veh_stock = \Databucket::isCacheHMGet($veh_data['vin'], 'stock_number');
		$veh_doors = \Databucket::isCacheHMGet($veh_data['vin'], 'doors');
		$veh_icolor = \Databucket::isCacheHMGet($veh_data['vin'], 'interior_meta_color_desc');		
			 
		//Vehicle
		$data['dealer_code'] = $dealerid;
		$data['dealer_name'] = $dealername;
		$data['vehicle_year'] =  \Ore::is_array_key($vehicle,'year');
		$data['vehicle_make'] =  \Ore::is_array_key($vehicle,'make');
		$data['vehicle_model'] =  \Ore::is_array_key($vehicle,'model');
		$data['vehicle_vin'] = \Ore::is_array_key($vehicle,'vin');
		$data['vehicle_trim'] = \Ore::is_array_key($vehicle,'trim_desc');
		$data['vehicle_stock'] = $veh_stock[0];
		$data['vehicle_doors'] = $veh_doors[0];
		$data['vehicle_bodystyle'] = \Ore::is_array_key($vehicle,'body_style');
		$data['vehicle_transmission'] = \Ore::is_array_key($vehicle,'transmission_desc');
		$data['vehicle_interiorcolor'] = $veh_icolor[0];
		$data['vehicle_exteriorcolor'] = \Ore::is_array_key($vehicle,'exterior_color_desc');
		$data['vehicle_preference'] = '';
		$data['vehicle_imagetag'] = '';
		$data['vehicle_price'] =  \Ore::is_array_key($vehicle,'msrp');
		$data['vehicle_price_comments'] = $pricecomments;
		$data['vehicle_optionname'] = '';
		$data['vehicle_manufacturercode'] = '';
		$data['vehicle_weighting'] = ''; 
		
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator') ){ 
			$json_calc = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator');	 
			$json_calc = json_decode($json_calc[0], true);
			
			$data['vehicle_option_method'] = \Ore::is_array_key($json_calc,'type');
			$data['vehicle_option_total_payment'] = \Ore::is_array_key($json_calc,'total');
			if($data['vehicle_option_method']!='Cash'){
				$data['vehicle_option_down_payment'] = \Ore::is_array_key($json_calc,'downpayment');
				$data['vehicle_option_monthly_payment'] = \Ore::is_array_key($json_calc,'monthly');
			}else{
				$data['vehicle_option_down_payment'] = "";
				$data['vehicle_option_monthly_payment'] = "";
			}
			
			
		}else{
			$data['vehicle_option_method'] = '';
			$data['vehicle_option_down_payment'] = '';
			$data['vehicle_option_monthly_payment'] = '';
			$data['vehicle_option_total_payment'] = '';
		}
 
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein') ){ 
			$json_tradein = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein');	 
			$array_tradein = json_decode($json_tradein[0], true);
            $tradein_price = \Ore::is_array_key($array_tradein,'price');
            $tradein_remainValue = \Ore::is_array_key($array_tradein,'remainingvalue');
            $estimated_owed = 0;
			if(!empty($tradein_price)){
                $tradein_price = str_replace('$', '', $tradein_price);
                $tradein_price = str_replace(',', '', $tradein_price);
                $tradein_price = intval($tradein_price);
                $estimated_owed = !empty($tradein_remainValue) ? ($tradein_price - $tradein_remainValue) : $estimated_owed;
            }
            
			$data['tradein_year'] = \Ore::is_array_key($array_tradein,'year');
			$data['tradein_make'] = \Ore::is_array_key($array_tradein,'make');
			$data['tradein_model'] = \Ore::is_array_key($array_tradein,'model');
			$data['tradein_vin'] = '';
			$data['tradein_units'] = \Ore::is_array_key($array_tradein,'mileage');
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = \Ore::is_array_key($array_tradein,'zip');
			$data['tradein_vehicle_condition'] = \Ore::is_array_key($array_tradein,'condition');
            $data['estimated_owed'] = $estimated_owed;
			$data['tradein_value'] = \Ore::is_array_key($array_tradein,'price');
		}else{
			$data['tradein_year'] = '';
			$data['tradein_make'] = '';
			$data['tradein_model'] = '';
			$data['tradein_vin'] = '';
			$data['tradein_units'] = '';
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = '';
			$data['tradein_vehicle_condition'] = '';
			$data['estimated_owed'] = '';
			$data['tradein_value'] = '';
		} 
		

		//Timeframe
		$data['timeframe_desc'] = '';
		$data['timeframe_earliestdate'] = '';
		$data['timeframe_latestdate'] = '';

		//Service and Protection
		/*$s_and_pro = '';
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar') ){ 
			$string_mophar = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar');	 
			if(count($string_mophar) > 0){
				$s_and_pro = $string_mophar[0];
			}  
		}*/
		$data['service_protection'] = $leadServices;
		
		$data['inbound_id'] = '';
		$data['additional_details'] = $types;
		$data['lead_status'] = \Ore::is_array_key($user,'lead_status'); 

		$data['created_at'] = $setTime;
        $data['updated_at'] = $setTime;  
		return $data;		
	}
	
	  /* 
	  * Method Name: Routeone
	  * Method Desc: Routeone form data mapping with table coloum index
	  * Date: 24-4-2019
	  *  
	  * @request ore_session<string>, user<string>, tradeIn<string>
	  *  	  
	  * @return data<array>
	  */
	  
	  
	  
	  
	 public function Routeone_InBound($soap_response){
		 
		// \Log::info('Step-3'); 
		 $currentSession = \Ore::getSessionID();
		$plainXML = \Ore::mungXML($soap_response);
		$arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true); 
		
try{
		$E_Body 			= $arrayResult['E_Body'];
		$B_DataArea 		= $arrayResult['E_Body']['B_ProcessCreditDecision']['B_DataArea']['B_CreditDecision'];
		$B_Detail = $arrayResult['E_Body']['B_ProcessCreditDecision']['B_DataArea']['B_CreditDecision']['B_Detail'];
		$A_AdditionalInfo = $arrayResult['E_Body']['A_RouteOne'];
 
		
		$B_CreditVehicle 		= $B_Detail['B_CreditVehicle'];
		$B_IndividualApplicant 	= $B_Detail['B_IndividualApplicant'];
		$B_Financing 			= $B_Detail['B_Financing'];
		$B_Decision 			= $B_Detail['B_Decision'];
		
		$v_arr = $B_Detail['B_CreditVehicle'];
					$dealerid = $dealername = "";
				if(!\Databucket::isCacheExists($v_arr['B_VIN'])){ 
						$datass = \App\Vehicle::where(['vin' => $v_arr['B_VIN']])->first(); 
						$val1 = json_decode(collect($datass),true);
						$dealerid = $val1['dealer_code'];
						$dealername =\Ore::dealerFetchByID($dealerid);

				}else{
					$datass = \Ore::isCacheGetAll($v_arr['B_VIN']);
					$dealerid = $datass['dealer_code'];
					$dealername =\Ore::dealerFetchByID($dealerid);
					}
					
					if( $dealername!=''){
						// $isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealername));
						// $isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('romeo-', 'romeo', strtolower($isDealer));

						$isDealer = $dealername;
						$isDealer =  ucwords($isDealer);
					}else $isDealer = '';
		$return_array['dealerName'] = $isDealer;
		
		
		/*******************************/
		$RONE_ADDITIOAL = "";
		if(\Ore::is_array_key($A_AdditionalInfo, 'A_AdditionalInfo')){
				$B_AIFNO 				= $A_AdditionalInfo['A_AdditionalInfo']['A_R1CreditApplicationDecisionInfo'];
				
				//A_PartnerSpecificAdditionalInfo
				if(\Ore::is_array_key($B_AIFNO, 'A_PartnerSpecificAdditionalInfo')){
					$RONE_ADDITIOAL .= 'Partner Specific Additional Info\n';
					foreach($B_AIFNO['A_PartnerSpecificAdditionalInfo'] as $key=>$val){
						$RONE_ADDITIOAL .= $val.'\n';
					}
				}
				//A_Reserve
				if(\Ore::is_array_key($B_AIFNO, 'A_Reserve')){
					$RONE_ADDITIOAL .= "RESERVE\n";
					foreach($B_AIFNO['A_Reserve'] as $key=>$val){ 
						$RONE_ADDITIOAL .= $key.':'.$val.'\n';
					}
				}
				//LTVItem
				if(\Ore::is_array_key($B_AIFNO, 'A_LTV')){
					
						if(\Ore::is_array_key($B_AIFNO['A_LTV'], 'A_Actual')){
							$RONE_ADDITIOAL .= 'LTV ACTUAL:'.$B_AIFNO['A_LTV']['A_Actual'].'\n';
						}
						if(\Ore::is_array_key($B_AIFNO['A_LTV'], 'A_Maximum')){
							$RONE_ADDITIOAL .= 'LTV Maximum:'.$B_AIFNO['A_LTV']['A_Maximum'].'\n';
						}
						if(\Ore::is_array_key($B_AIFNO['A_LTV'], 'A_Comment')){
							$RONE_ADDITIOAL .= 'LTV COMMENTS:'.$B_AIFNO['A_LTV']['A_Comment'].'\n';
						} 
						if(\Ore::is_array_key($B_AIFNO['A_LTV'], 'A_LTVItem')){
							$acol = array_column($B_AIFNO['A_LTV']['A_LTVItem'],'A_Rate','A_Range');
							$bcol = array_column($B_AIFNO['A_LTV']['A_LTVItem'],'A_Fee','A_Range'); 
							$RONE_ADDITIOAL .= "LTV BY RATE:\n";
							foreach($acol as $key2=>$val2){ 
								$RONE_ADDITIOAL .= $key2.':'.$val2.'\n';
							}
							$RONE_ADDITIOAL .= "LTV BY FEE:\n";
							foreach($bcol as $key3=>$val3){ 
								$RONE_ADDITIOAL .= $key3.':'.$val3.'\n';
							}							
						} 
				} 
		}
		 
		/*********************************/
		$reference_id = $E_Body['B_ProcessCreditDecision']['B_ApplicationArea']['B_Sender']['B_ReferenceId'];
		
		$setTime = Carbon::now('America/New_York');
		$data['session_id'] 		= \Ore::getRefSessionID('routeone_refid', $reference_id); 
		$data['source_id'] 			= config('ore.routeone_sourceid');
		 $data['lead_source'] 		= \Ore::getTierID('routeone_refid', $reference_id);  
		//Personal
		$data['first_name'] 		= \Ore::is_array_key($B_IndividualApplicant['B_PersonName'],'B_GivenName');		
		$data['last_name'] 			= \Ore::is_array_key($B_IndividualApplicant['B_PersonName'],'B_FamilyName');
		$data['email'] 				=  \Ore::is_array_key($B_IndividualApplicant['B_Contact'],'B_EMailAddress');
		$data['phone'] 				= \Ore::is_array_key($B_IndividualApplicant['B_Contact'],'B_Telephone');
		
		  
		if(\Ore::is_array_key($B_IndividualApplicant['B_Address'],'B_AddressLine')){
			$data['streetline1'] 	= $B_IndividualApplicant['B_Address']['B_AddressLine'];
		}
		$data['streetline2'] 		= ''; 
		 
		$data['city'] 				=  \Ore::is_array_key($B_IndividualApplicant['B_Address'],'B_City');
		$data['state'] 				=  \Ore::is_array_key($B_IndividualApplicant['B_Address'],'B_StateOrProvince');
		$data['zip'] 				=  \Ore::is_array_key($B_IndividualApplicant['B_Address'],'B_PostalCode');
		$data['comments'] 			= \Ore::is_array_key($B_Financing,'B_MiscellaneousNotes');
		//Vehicle
		$data['dealer_code'] 		= $dealerid; 
            
        $data['dealer_name'] 		= $isDealer;
		$data['vehicle_year'] 		=  \Ore::is_array_key($B_CreditVehicle,'B_ModelYear');
		$data['vehicle_make'] 		=  \Ore::is_array_key($B_CreditVehicle,'B_Make');
		$data['vehicle_model']		=  \Ore::is_array_key($B_CreditVehicle,'B_Model');
		$data['vehicle_vin'] 		= \Ore::is_array_key($B_CreditVehicle,'B_VIN');
		$data['vehicle_trim'] 		= '';
		$data['vehicle_stock'] 		= '';
		$data['vehicle_doors'] 		= '';
		$data['vehicle_bodystyle'] 	= \Ore::is_array_key( $B_CreditVehicle,'B_VehicleNote');
		$data['vehicle_transmission'] = '';
		$data['vehicle_interiorcolor'] = '';
		$data['vehicle_exteriorcolor'] = '';
		$data['vehicle_preference'] = '';

		$data['vehicle_imagetag'] 	= '';
		$data['vehicle_price'] 		=  \Ore::is_array_key2($B_CreditVehicle['B_Pricing'],'0','B_VehiclePrice');
		$data['vehicle_price_comments'] =  \Ore::is_array_key2($B_CreditVehicle['B_Pricing'],'1','B_VehiclePrice');

		$data['vehicle_optionname'] 	= '';
		$data['vehicle_manufacturercode'] = '';
		$data['vehicle_weighting'] = ''; 
		$data['vehicle_option_method'] = '';
		$data['vehicle_option_down_payment'] =  \Ore::is_array_key($B_Financing,'B_DownPaymentAmount');
		$data['vehicle_option_monthly_payment'] = '';
		$data['vehicle_option_total_payment'] = '';
		
		$cacheVerifiedSessionID = $data['session_id'];
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein') ){ 
			$json_tradein = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein');	 
			$array_tradein = json_decode($json_tradein[0], true);
			 $tradein_price = \Ore::is_array_key($array_tradein,'price');
            $tradein_remainValue = \Ore::is_array_key($array_tradein,'remainingvalue');
            $estimated_owed = 0;
            if(!empty($tradein_price)){
                $tradein_price = str_replace('$', '', $tradein_price);
                $tradein_price = str_replace(',', '', $tradein_price);
                $tradein_price = intval($tradein_price);
                $estimated_owed = !empty($tradein_remainValue) ? ($tradein_price - $tradein_remainValue) : $estimated_owed;
            } 
			$data['tradein_year'] = \Ore::is_array_key($array_tradein,'year');
			$data['tradein_make'] = \Ore::is_array_key($array_tradein,'make');
			$data['tradein_model'] = \Ore::is_array_key($array_tradein,'model');
			$data['tradein_vin'] = '';
			$data['tradein_units'] = \Ore::is_array_key($array_tradein,'mileage');
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = \Ore::is_array_key($array_tradein,'zip');
			$data['tradein_vehicle_condition'] = \Ore::is_array_key($array_tradein,'condition');
			$data['estimated_owed'] = $estimated_owed;
			$data['tradein_value'] = \Ore::is_array_key($array_tradein,'remainingvalue');
		}else{
			$data['tradein_year'] = '';
			$data['tradein_make'] = '';
			$data['tradein_model'] = '';
			$data['tradein_vin'] = '';
			$data['tradein_units'] = '';
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = '';
			$data['tradein_vehicle_condition'] = '';
			$data['estimated_owed'] = '';
			$data['tradein_value'] = '';
		} 
		

		//Timeframe
		$data['timeframe_desc'] = '';
		$data['timeframe_earliestdate'] = '';
		$data['timeframe_latestdate'] = '';

		//Service and Protection
		$s_and_pro = '';
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar') ){ 
			$string_mophar = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar');	 
			if(count($string_mophar) > 0){
				$s_and_pro = $string_mophar[0];
			}  
		}
		$data['service_protection'] = $s_and_pro;
		
		$data['additional_details'] = $RONE_ADDITIOAL; 
		$data['created_at'] = $setTime;
        $data['updated_at'] = $setTime;
		return $data; 
	}catch(\Exception $e){
						\Log::error('Routeone Message: ' .$e->getMessage());
		}		
	}
	
	/********* Routeone InBound End***********************/
	
	
	/********* 700 Credit InBound ***********************/
	
	public function Credit700_InBound($json){ 
	
	}
	/********700 Credit InBound End*********************/
	
	
	/********* Carnow InBound ***********************/
	
	public function Carnow_InBound($soap_response){ 

		$my_array_data = json_decode($soap_response, TRUE);
		
		
		$dealerid = "";
				if(!\Databucket::isCacheExists($my_array_data['vehicle_vin'])){ 
						$datass = \App\Vehicle::where(['vin' => $my_array_data['vehicle_vin']])->first(); 
						$val1 = json_decode(collect($datass),true);
						$dealerid = $val1['dealer_code'];
						$dealername =\Ore::dealerFetchByID($dealerid);
				}else{
					$datass = \Ore::isCacheGetAll($my_array_data['vehicle_vin']);
					$dealerid = $datass['dealer_code'];
					$dealername =\Ore::dealerFetchByID($dealerid);
					}
		 if( $dealername!=''){
						// $isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealername));
						// $isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
						// $isDealer =  Str::replaceFirst('romeo-', 'romeo', strtolower($isDealer));

						$isDealer = $dealername;
						$isDealer =  ucwords($isDealer);
					}else $isDealer = '';
		$return_array['dealerName'] = $isDealer;
		 
		$setTime = Carbon::now('America/New_York');

try{
		$reference_id = \Ore::is_array_key($my_array_data,'oreSessionID');
		$data['session_id'] = \Ore::getRefSessionID('carnow_refid', $reference_id); 
		$data['source_id'] = config('ore.carnow_sourceid');
		$data['lead_source'] = \Ore::getTierID('carnow_refid', $reference_id); 
		
		//Personal
		$data['first_name'] = \Ore::is_array_key($my_array_data,'first_name');
		$data['last_name'] = \Ore::is_array_key($my_array_data,'last_name');
		$data['email'] =  \Ore::is_array_key($my_array_data,'email');
		$data['phone'] = \Ore::is_array_key($my_array_data,'mobile_phone');  
		$data['streetline1'] = \Ore::is_array_key($my_array_data,'streetline1'); 
		$data['streetline2'] = \Ore::is_array_key($my_array_data,'streetline2');
		$data['apartment'] = \Ore::is_array_key($my_array_data,'apartment');		
		$data['city'] =  \Ore::is_array_key($my_array_data,'city');
		$data['state'] =  \Ore::is_array_key($my_array_data,'state');
		$data['zip'] = \Ore::is_array_key($my_array_data,'zip');
		
		$carnow_comments = \Ore::is_array_key($my_array_data,'comments');
		
		$tmpArr = array();
			foreach ($carnow_comments as $sub) {
			  $tmpArr[] = implode(',',$sub);
			}
			$result = implode('',$tmpArr);
		$data['comments'] = htmlspecialchars($result);
		
		if(\Databucket::hexists('user:experience:sessionid:carnow:'.$reference_id, 'session') ){
			$sessionid_basedon_carnow_refid = \Databucket::isCacheHMGet('user:experience:sessionid:carnow:'.$reference_id, 'session');
			$cacheVerifiedSessionID = $sessionid_basedon_carnow_refid[0];
		}else{
			
			$cacheVerifiedSessionID = \Ore::getSessionID();
		} 
		 
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'vehicle_info') ){ 
			$json_carnow = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'vehicle_info');	 
			$array_carnow = json_decode($json_carnow[0], true);
			$veh_data = $array_carnow['vin']; 
		$veh_stock = \Databucket::isCacheHMGet($veh_data, 'stock_number');
		$veh_doors = \Databucket::isCacheHMGet($veh_data, 'doors');
		$veh_icolor = \Databucket::isCacheHMGet($veh_data, 'interior_meta_color_desc');		
		

				$data['dealer_code'] = \Ore::is_array_key($array_carnow,'dealer_code');  
				$data['dealer_name'] = '';
				$data['vehicle_year'] = \Ore::is_array_key($array_carnow,'year');
				$data['vehicle_make'] = \Ore::is_array_key($array_carnow,'make');
				$data['vehicle_model'] = \Ore::is_array_key($array_carnow,'model');
				$data['vehicle_vin'] = \Ore::is_array_key($array_carnow,'vin');
				$data['vehicle_trim'] = Ore::is_array_key($array_carnow,'trim_desc');
				$data['vehicle_stock'] = $veh_stock[0];
				$data['vehicle_doors'] = $veh_doors[0];
				$data['vehicle_bodystyle'] = \Ore::is_array_key($array_carnow,'body_style');
				$data['vehicle_transmission'] = \Ore::is_array_key($array_carnow,'transmission_desc');
				$data['vehicle_interiorcolor'] = $veh_icolor[0];
				$data['vehicle_exteriorcolor'] = \Ore::is_array_key($array_carnow,'exterior_color_code');
				$data['vehicle_preference'] = '';
				$data['vehicle_imagetag'] = '';
				$data['vehicle_price'] =  \Ore::is_array_key($array_carnow,'msrp');
				$data['vehicle_price_comments'] = '';
				$data['vehicle_optionname'] = '';
				$data['vehicle_manufacturercode'] = '';
				$data['vehicle_weighting'] = ''; 
		}else{
		//Vehicle
		$data['dealer_code'] = $dealerid;
		$data['dealer_name'] = $isDealer;
		$data['vehicle_year'] = \Ore::is_array_key($my_array_data,'vehicle_year');
		$data['vehicle_make'] = \Ore::is_array_key($my_array_data,'vehicle_make');
		$data['vehicle_model'] = \Ore::is_array_key($my_array_data,'vehicle_model');
		$data['vehicle_vin'] = \Ore::is_array_key($my_array_data,'vehicle_vin');
		$data['vehicle_trim'] = \Ore::is_array_key($my_array_data,'vehicle_trim');
		$data['vehicle_stock'] = \Ore::is_array_key($my_array_data,'vehicle_stock');
		$data['vehicle_bodystyle'] = '';
		$data['vehicle_transmission'] = '';
		$data['vehicle_interiorcolor'] = '';
		$data['vehicle_exteriorcolor'] = '';
		$data['vehicle_preference'] = '';
		$data['vehicle_imagetag'] = '';
		$data['vehicle_price'] =  \Ore::is_array_key($my_array_data,'vehicle_price');
		$data['vehicle_price_comments'] = '';
		$data['vehicle_optionname'] = '';
		$data['vehicle_manufacturercode'] = '';
		$data['vehicle_weighting'] = ''; 
		}
		 
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator') ){ 
			$json_calc = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'calculator');	 
			$json_calc = json_decode($json_calc[0], true);
			
			$data['vehicle_option_method'] = \Ore::is_array_key($json_calc,'type');
			$data['vehicle_option_total_payment'] = \Ore::is_array_key($json_calc,'total');
			if($data['vehicle_option_method']!='Cash'){
				$data['vehicle_option_down_payment'] = \Ore::is_array_key($json_calc,'downpayment');
				$data['vehicle_option_monthly_payment'] = \Ore::is_array_key($json_calc,'monthly');
			}else{
				$data['vehicle_option_down_payment'] = "";
				$data['vehicle_option_monthly_payment'] = "";
			}
			
			
		}else{
			$data['vehicle_option_method'] = '';
			$data['vehicle_option_down_payment'] = '';
			$data['vehicle_option_monthly_payment'] = '';
			$data['vehicle_option_total_payment'] = '';
		}

		
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein') ){ 
			$json_tradein = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'tradein');	 
			$array_tradein = json_decode($json_tradein[0], true);
			 $tradein_price = \Ore::is_array_key($array_tradein,'price');
            $tradein_remainValue = \Ore::is_array_key($array_tradein,'remainingvalue');
            $estimated_owed = 0;
            if(!empty($tradein_price)){
                $tradein_price = str_replace('$', '', $tradein_price);
                $tradein_price = str_replace(',', '', $tradein_price);
                $tradein_price = intval($tradein_price);
                $estimated_owed = !empty($tradein_remainValue) ? ($tradein_price - $tradein_remainValue) : $estimated_owed;
            }  
			$data['tradein_year'] = \Ore::is_array_key($array_tradein,'year');
			$data['tradein_make'] = \Ore::is_array_key($array_tradein,'make');
			$data['tradein_model'] = \Ore::is_array_key($array_tradein,'model');
			$data['tradein_vin'] = '';
			$data['tradein_units'] = \Ore::is_array_key($array_tradein,'mileage');
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = \Ore::is_array_key($array_tradein,'zip');
			$data['tradein_vehicle_condition'] = \Ore::is_array_key($array_tradein,'condition');
			$data['estimated_owed'] = $estimated_owed;
			$data['tradein_value'] = \Ore::is_array_key($array_tradein,'remainingvalue');
		}else{
			$data['tradein_year'] = '';
			$data['tradein_make'] = '';
			$data['tradein_model'] = '';
			$data['tradein_vin'] = '';
			$data['tradein_units'] = '';
			$data['finance_balance'] = '';
			$data['type_of_mode'] = '';
			$data['tradein_zip'] = '';
			$data['tradein_vehicle_condition'] = '';
			$data['estimated_owed'] = '';
			$data['tradein_value'] = '';
		} 

		//Timeframe
		$data['timeframe_desc'] = '';
		$data['timeframe_earliestdate'] = '';
		$data['timeframe_latestdate'] = '';

		//Service and Protection
		$s_and_pro = '';
		if(\Databucket::hexists('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar') ){ 
			$string_mophar = \Databucket::isCacheHMGet('user:experience:sessionid:'.$cacheVerifiedSessionID, 'mophar');	 
			if(count($string_mophar) > 0){
				$s_and_pro = $string_mophar[0];
			}  
		}
		$data['service_protection'] = $s_and_pro;
		if($data['comments']!='')
		{
			$data['service_protection'] = $s_and_pro.' '.$data['comments'];
		}
		
		$data['inbound_id'] = '';
		$data['additional_details'] = '';
		$data['lead_status'] = ''; 

		$data['created_at'] = $setTime;
        $data['updated_at'] = $setTime; 
		
			return $data;
			
	}catch(\Exception $e){
						\Log::error('Carnow Message: ' .$e->getMessage());
		}			
	}
	/********Carnow InBound End*********************/
	public  function rModelValidation($tmp_val, $model_a){
		if(str_word_count($tmp_val)<=1){
			return ['status'=>'fail', 'value' => $tmp_val];
		}
		if($tmp_val == $model_a){
				return ['status'=>'pass', 'value' => $tmp_val];
		}else{
				$r_tmp_val=$this->strLastWordRemoved($tmp_val);
				$this->rModelValidation($r_tmp_val, $model_a); 
		}
	}
	
	public function strLastWordRemoved($a){
		return trim(substr($a, 0, strrpos($a, " ")));
	}
function findWords($words, $search) {
			$words_array = explode(" ", trim($words));
			//$word_length = count($words_array);

			$search_array = explode(" ", $search);
			$search_length = count($search_array);

			$mix_array = array_intersect($words_array, $search_array);
			$mix_length = count($mix_array);

			if ($mix_length == $search_length) {
				return true;
			} else {
				return false;
			}
	}


	public function ramVinDecoder($models){
		$models = strtolower($models);
		/*
		*  Due to Ram Vindecoder API not returning proerply model values over API
		*	so i did hard codging for RAM brand here
		* Date: 27-Mar-2020 by Sathish
		*/
	// $ramModels =["Ram 1500","Ram 2500","Ram 3500","Ram 1500 Classic",
	// 				"Ram 3500 Chassis Cab","Ram 4500 Chassis Cab","Ram 5500 Chassis Cab",
	// 				"Ram 3500 SRW 10K GVWR Chassis Cab",
	// 	0			"Ram ProMaster City®","Ram ProMaster®"];
		
		if($this->findWords($models, 'chassis')	){
					if($this->findWords($models, '3500')	){
						if($this->findWords($models, 'srw')	){
								return 'Ram 3500 SRW 10K GVWR Chassis Cab';
						}else return 'Ram 3500 Chassis Cab';
						
					}else if($this->findWords($models, '4500')	){
								return 'Ram 4500 Chassis Cab';
							
					}else if($this->findWords($models, '5500')	){
						return 'Ram 5500 Chassis Cab';
					}else{
						return "Ram 5500 Chassis Cab";
					}
		} 
		if($this->findWords($models, 'promaster')	){
					if($this->findWords($models, 'city')	){ 
						return "Ram ProMaster City®";
					}else{
						return "Ram ProMaster®";
					}
		} 	
		if($this->findWords($models, 'classic')	){	
						if($this->findWords($models, '1500')	){ 
							return 'Ram 1500 Classic';
						
					}else if($this->findWords($models, '2500')	){
								return 'Ram 2500 Classic';
							
					}else if($this->findWords($models, '3500')	){
						return 'Ram 3500 Classic';
					}else{
						return "Ram 1500 Classic";
					}
		}	


					if($this->findWords($models, '1500')	){ 
							return 'Ram 1500';
					}else if($this->findWords($models, '2500')	){
								return 'Ram 2500';
							
					}else if($this->findWords($models, '3500')	){
						return 'Ram 3500';
					}else if($this->findWords($models, '4500')	){
						return 'Ram 1500';
					}else if($this->findWords($models, '5500')	){
						return 'Ram 1500';
					}else{
						return "Ram 1500";
					}

	}
	/********* Vindecoder start***********************/
	
	public function Vindecoder($vin){
		
		$endpoint = 'https://www.chrysler.com/veqpws/VehicleEquipmentsServlet.do?vin='.$vin.'&responsemode=S&responseType=JSON';
	  
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); 
		$result = curl_exec($ch);	
		
		    $plainXML = \Ore::mungXML($result);
			
			$arrayResult = json_decode(json_encode(SimpleXML_Load_String($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true); 
			
			$contains = Arr::has($arrayResult, 'vehicle_details.model');
			 
			if($contains){
				$vehicle = (explode(" ",$arrayResult['vehicle_details']['model']));
				 
				$year  = Arr::has($vehicle,0) ? Arr::get($vehicle,0) : 0; 
				$model = Arr::has($vehicle,2) ? Arr::get($vehicle,2) : '';  

				$model_a =  Arr::has($arrayResult,'vehicle_details.model_type') ? trim(Arr::get($arrayResult,'vehicle_details.model_type')) : ''; 
				$make =  Arr::has($arrayResult,'vehicle_details.make') ? trim(Arr::get($arrayResult,'vehicle_details.make')) : ''; 
				$model_a = str_replace('  ',' ',trim($model_a));
				$model_a = str_replace('  ','',$model_a); 
				
				if(strtolower($make) != 'ram'){
					$mm = \App\Vmodel::where(
				 	['description' => $model_a, 'modelYearDescription' => $year])->select('franchiseDescription','modelDesc')->first();

					if(!empty($mm->modelDesc)){
						$model = strtolower($mm->modelDesc);
					}
				}else{
					
						$model = $this->ramVinDecoder($arrayResult['vehicle_details']['model']);
				}
				 
				  
				if($make=='') $make = Arr::has($vehicle,1) ? Arr::get($vehicle,1) : '';  

				$make = strtolower($make);
				$model = str_replace('  ','-',$model);
				$model = str_replace(' ','-',$model);
				$model = strtolower($model);

				if(($year >= 2018) && 
				($make=='jeep' || $make=='dodge' || $make=='fiat' || $make=='ram' || $make=='chrysler')  && $model!='')
				{
					$response_array = array('make'=>$make, 'model'=>$model, 'year'=>$year);
					
					return ['status' =>true, 'data' => $response_array]; 
				}
				else{
					return ['status' =>false, 'data' => '']; 
				}
			} else{
					return ['status' =>false, 'data' => '']; 
				}

    }
	
	/********Vindecoder End*********************/
	
	
	/********* Dealer Address Finding  Start***********************/
	
	public function DealerApi($dealercode){
		
		$endpoint = config('ore.dealerapi.endpoint').$dealercode.'&func=SALES';
	   
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); 
		$result = curl_exec($ch);			 
		curl_close($ch);
	
		    $dealerarrayresult = json_decode($result, TRUE);
			 
			$contains = Arr::has($dealerarrayresult, 'dealer.0');
	
			if($contains){
				$dealer = ($dealerarrayresult['dealer'][0]);

				$dealerCode = $dealerName = $dealerAddress1 = $dealerAddress2 = $dealerCity = $dealerState = $dealerZip = $demail = '';
				
				$dealerCode = Arr::get($dealerarrayresult, 'dealer.0.dealerCode');
				$dealerName = Arr::get($dealerarrayresult, 'dealer.0.dealerName');
				$dealerAddress1 = Arr::get($dealerarrayresult, 'dealer.0.dealerAddress1');
				$dealerAddress2 = Arr::get($dealerarrayresult, 'dealer.0.dealerAddress2');
				$dealerCity = Arr::get($dealerarrayresult, 'dealer.0.dealerCity');
				$dealerState = Arr::get($dealerarrayresult, 'dealer.0.dealerState');
				$dealerZip = Arr::get($dealerarrayresult, 'dealer.0.dealerZipCode');
				$phoneNumber = Arr::get($dealerarrayresult, 'dealer.0.phoneNumber');
				$demail = Arr::get($dealerarrayresult, 'dealer.0.demail');
				 
				
				
				return ['dealerCode' => $dealerCode, 'dealerName' => $dealerName, 'dealerAddress1' => $dealerAddress1,  'dealerAddress2' => $dealerAddress2, 'dealerCity' => $dealerCity, 'dealerState' => $dealerState, 'dealerZip' => $dealerZip, 'phoneNumber' => $phoneNumber, 'demail' => $demail ];
			}
    }
	
	/********Dealer Address Finding End*********************/
	
	
	/********* Dealer Eliminate InBound ***********************/
	
	public function Dealereliminate_InBound($soap_response){ 

		$my_array_data = json_decode($soap_response, TRUE);

		$setTime = Carbon::now('America/New_York');

		try{
		
		$data['dlr_code'] = \Ore::is_array_key($my_array_data,'dealer_code');
		$data['dlr_dba_name'] = \Ore::is_array_key($my_array_data,'dealer_name');
		$data['status'] =  \Ore::is_array_key($my_array_data,'status');
		$data['created_at'] = $setTime;
        $data['updated_at'] = $setTime;
			return $data;
			
	}catch(\Exception $e){
						\Log::error('Dealereliminate Message: ' .$e->getMessage());
		}			
	}
	
	
	  
	  
	 /*************************   LEAD MERGE OPERATIONS *******************************/
	  /**
	  *   5 Function Operated
	  *
	  *	 1. vendor_information_insert()
			a. Insert values into database for received from Inbounds
			b. If user experience then call vendor_consolidation()
		 2. vendor_consolidation()
			a. QUERY for conslidate all inbounds and preparing for one single rows
			b. single row date pass to XMLBuild() function
		 3. xmlBuild($array_data_getting_from_vendor_consolidation)
		    a. LEAD XML Attribute mapping with array_data
			b. XML format save into one variable
			c. XML variable pass to XML_SEND_LEAD() function
		 4. XML_SEND_LEAD($getting_lead_xml_format_from_xmlBuild)
		    a. CURL operation to send LEAD API
			b. getting_lead_xml_format_from_xmlBuild merge with BODY
			c. before sending API, save into PRE_LEAD table
			d. Received LEAD RESPONSE with status, leadid
			e. Pass reponse to Lead_Insert() function
		 5.	Lead_Insert($getting_response_from_XML_SEND_LEAD)
		    a. LEAD TABLE POPULATED
			b. STAGE TABLE: Change the FLAG.
		 
		 6. CRON:
		     every 10 mins server contact our  vendor_consolidation() fucntion.-
		 
	  */
	  
	  public function vendor_information_insert($vendor_type, $array,  $form_type){
				
			if (!empty($array['first_name']) && !empty($array['last_name']) && !empty($array['email']) && !empty($array['zip'])) {
					Stage::insert($array);
				} else {
					return ['status' => false, 'message' => 'invalid field values', 'description' => '']; 
				} 
				
				if($vendor_type=='Routeone_InBound') { $ref = $array['session_id'];} else{$ref = '';}
				if($vendor_type=='Carnow_InBound') { $ref = $array['session_id'];} else{$ref = '';}
				if($vendor_type=='user_experience') { $ref = $array['session_id'];} else{$ref = '';}
				
				return \Ore::vendor_consolidation($ref,  $form_type);			
	}
	 
	public function vendor_consolidation($ref,  $form_type){	
			
			   $collection = \Databucket::vendor_experience_query($ref);
			
				foreach($collection as $k=>$v){ 
				
				return $xmlLead = \Ore::xmlLead((array)$v,  $form_type);
				}
    }
	
	public function xmlLead($vendor_experience_consolidate,  $form_type){ 
			$v_arr = $vendor_experience_consolidate;
				if($form_type == 'auto'){
					$ore_session = $v_arr['session_id']; 
					 $csession = \App\Leadsession::where(['id' => $ore_session])->select('ore_session')->first(); 
					 if($csession->ore_session == null) {
							$currentSession = '';
						}else{
							$currentSession = $csession->ore_session;
						}
				}else{				
								 if($v_arr['source_id'] == 2 || $v_arr['source_id'] == 1){
									$currentSession = \Ore::getSessionID();
									/* $mycount = \App\Leadsession::where(['ore_session' => $currentSession, 'flag' => 8])->count(); 
									 if($mycount > 0){
										session()->regenerate();
										$currentSession = \Ore::getSessionID();
									 } */
									
									/* //-- ROUTEONE --
									
									$reference_id = \Ore::is_array_key($v_arr,'session_id');
							
									$session = \Ore::getORESessionID('id', $reference_id); 
									Leadsession::updateOrCreate(
									['routeone_refid' => $session],
											['ore_session' => $currentSession]); */
								 
								}
				}				
				
				/*
					//-- CARNOW --
				if($v_arr['source_id'] == 4){
				$currentSession = \Ore::getSessionID();
				$reference_id = \Ore::is_array_key($v_arr,'session_id');
			
				$session = \Ore::getORECarnowSessionID('id', $reference_id);
				
				{
					Leadsession::updateOrCreate(
					['carnow_refid' => $session],
							['ore_session' => $currentSession]);
				}
			} */
				
				
                
		try{ 
				$setTime = Carbon::now('America/New_York'); 
				$dealerName = $vin = $leadServices  = ""; 				
				/* Mopar service and finance*/
				$v_arr['service_protection'] = str_replace('&','and',$v_arr['service_protection']); 
				$v_arr['dealer_name']  = str_replace('&','and',$v_arr['dealer_name']); 
				$v_arr['vehicle_price_comments']  = str_replace('&','and',$v_arr['vehicle_price_comments']);

				$v_arr['zip'] = str_pad($v_arr['zip'], 5, 0, STR_PAD_LEFT);

				$xmlLead   ='<adf>';
				$xmlLead  .='<prospect status="new">';
				$xmlLead  .='<id sequence="1" source="ORE Lead ID">'.$currentSession.'</id>';
				$xmlLead  .='<id sequence="2" source="Form">Buy It Now</id>';
				$xmlLead  .='<id sequence="3" source="Session ID">'.$currentSession.'</id>';
				$xmlLead  .='<id sequence="4" source="SourceCode"></id>';
				$xmlLead  .='<id sequence="5" source="EventName"></id>'; 
				$xmlLead.='<requestdate>'. $setTime.'</requestdate>';
				
				$xmlLead  .= '<vehicle interest="buy" status="new">';
                $xmlLead  .= '<year>'.$v_arr['vehicle_year'].'</year>';
                $xmlLead  .= '<make>'.$v_arr['vehicle_make'].'</make>';
                $xmlLead  .= '<model>'.htmlspecialchars($v_arr['vehicle_model']).'</model>';
                $xmlLead  .= '<vin>'.$v_arr['vehicle_vin'].'</vin>';
                $xmlLead  .= '<trim>'.htmlspecialchars($v_arr['vehicle_trim']).'</trim>';
                $xmlLead  .= '<stock>'.$v_arr['vehicle_stock'].'</stock>';
                $xmlLead  .= '<doors>'.htmlspecialchars($v_arr['vehicle_doors']).'</doors>';
                $xmlLead  .= '<bodystyle>'.htmlspecialchars($v_arr['vehicle_bodystyle']).'</bodystyle>';
                $xmlLead  .= '<transmission>'.htmlspecialchars($v_arr['vehicle_transmission']).'</transmission>';
                $xmlLead  .= '<colorcombination>';
                $xmlLead  .= '<interiorcolor>'.$v_arr['vehicle_interiorcolor'].'</interiorcolor>';
                $xmlLead  .= '<exteriorcolor>'.$v_arr['vehicle_exteriorcolor'].'</exteriorcolor>';
                $xmlLead  .= '<preference>'.$v_arr['vehicle_preference'].'</preference>';
                $xmlLead  .= '</colorcombination>';
                $xmlLead  .= '<colorcombination>';
                $xmlLead  .= '<interiorcolor></interiorcolor>';
                $xmlLead  .= '<exteriorcolor></exteriorcolor>';
                $xmlLead  .= '<preference>2</preference>';
                $xmlLead  .= '</colorcombination>';
                $xmlLead  .= '<imagetag width="200" height="300" alttext="Alt Text"></imagetag>';
                $xmlLead  .= '<price type="quote" currency="USD" source="A Source">'.$v_arr['vehicle_price'].'</price>';
                $xmlLead  .= '<pricecomments>'.$v_arr['vehicle_price_comments'].'</pricecomments>';
                $xmlLead  .= '<option>';
                $xmlLead  .= '<optionname></optionname>';
                $xmlLead  .= '<manufacturercode></manufacturercode>';
                $xmlLead  .= '<weighting></weighting>';
                $xmlLead  .= '</option>';
                $xmlLead  .= '<option>';
                $xmlLead  .= '<optionname></optionname>';
                $xmlLead  .= '<manufacturercode></manufacturercode>';
                $xmlLead  .= '<weighting></weighting>';
                $xmlLead  .= '<stock></stock>';
                $xmlLead  .= '<price type="invoice" currency="USD" relativeto="invoice"></price>';
                $xmlLead  .= '</option>';
				 
			
                $xmlLead  .= '<finance>';
                $xmlLead  .= '<method>'.$v_arr['vehicle_option_method'].'</method>';
                $xmlLead  .= '<amount type="downpayment" currency="USD">'.$v_arr['vehicle_option_down_payment'].'</amount>';
                $xmlLead  .= '<amount type="monthly" currency="USD">'.$v_arr['vehicle_option_monthly_payment'].'</amount>';
                $xmlLead  .= '<amount type="total" currency="USD">'.$v_arr['vehicle_option_total_payment'].'</amount>';
                $xmlLead  .= '</finance>';
				
                $xmlLead  .= ' <comments></comments>';
                $xmlLead  .= '</vehicle>';
				 
				/*
				 * TradeIN coiming from consolidate  
				*/
				$xmlLead .= '<vehicle status="used" interest="trade-in">';
				$xmlLead .= '<year>'.$v_arr['tradein_year'].'</year>';
				$xmlLead .= '<make>'.$v_arr['tradein_make'].'</make>';
				$xmlLead .= '<model>'.htmlspecialchars($v_arr['tradein_model']).'</model>';
				$xmlLead .= '<vin>'.$vin.'</vin>';
				$xmlLead .= '<condition>'.$v_arr['tradein_vehicle_condition'].'</condition>';
				$xmlLead .= '<odometer status="original" units="miles">'.$v_arr['tradein_units'].'</odometer>';
				$xmlLead .= '<finance>';
				
				//  if($v_arr['tradein_value']==0 || $v_arr['tradein_value']==''){
				//  	$xmlLead .= '<balance type="finance">'.$v_arr['estimated_owed'].'</balance>';
				//  }else{
				//  	$xmlLead .= '<balance type="finance">'.$v_arr['tradein_value'].'</balance>';
				//  }

				$xmlLead .= '<balance type="finance">'.$v_arr['tradein_value'].'</balance>';
				$xmlLead .= '</finance>';
				$xmlLead .= '</vehicle>';
				
				$xmlLead.='<customer>';
				$xmlLead.='<contact primarycontact="1">';
				$xmlLead.='<name part="first">'.$v_arr['first_name'].'</name>';
				$xmlLead.='<name part="last">'.$v_arr['last_name'].'</name>';
				$xmlLead.='<name part="middle"></name>';
				$xmlLead.='<email>'.$v_arr['email'].'</email>';
				$xmlLead.='<phone type="voice" time="evening"></phone>';
				$xmlLead.='<phone type="voice" time="day" preferredcontact="1"></phone>';
				$xmlLead.='<phone type="cellphone" time="nopreference">'.$v_arr['phone'].'</phone>';
				$xmlLead.='<address type="home">';
				$xmlLead.='<street line="1">'.htmlspecialchars($v_arr['streetline1']).'</street>';
				$xmlLead.='<street line="2">'.htmlspecialchars($v_arr['streetline2']).'</street>';
				$xmlLead.='<apartment></apartment>';
				$xmlLead.='<city>'.htmlspecialchars($v_arr['city']).'</city>';
				$xmlLead.='<regioncode>'.htmlspecialchars($v_arr['state']).'</regioncode>';
				$xmlLead.='<postalcode>'.$v_arr['zip'].'</postalcode>';
				$xmlLead.='<country>US</country>';
				$xmlLead.='</address>';
				$xmlLead.='</contact>';
				$xmlLead.='<timeframe>';
				$xmlLead.='<description></description>';
				$xmlLead.='<earliestdate></earliestdate>';
				$xmlLead.='<latestdate></latestdate>';
				$xmlLead.='</timeframe>'; 
				
				$xmlLead.= '<comments>'.$v_arr['service_protection'].'</comments>';
				$xmlLead.='</customer>';
				
				$xmlLead.='<vendor>';
				$xmlLead.='<id source="chrysler-dealer-id">'.$v_arr['dealer_code'].'</id>'; 
				$xmlLead.='<vendorname>'.htmlspecialchars($v_arr['dealer_name']).'</vendorname>';
				$xmlLead.='</vendor>';
				$xmlLead.='<provider>';
				
				if($v_arr['lead_source']=='') $v_arr['lead_source'] = 'ore';
					
				if($v_arr['lead_source'] == 'ore') {				
					$xmlLead.='<id source="chrysler-source-id">'.config('ore.website_sourceid').'</id>';
					$xmlLead.='<name part="full" type="business">'.config('ore.website_businessname').'</name>';				
				}else if($v_arr['lead_source'] == 't1') { 
					$xmlLead.='<id source="chrysler-source-id">'.config('ore.brand_sourceid').'</id>';
					$xmlLead.='<name part="full" type="business">'.config('ore.brand_businessname').'</name>'; 
				}else if($v_arr['lead_source'] == 't3') { 
					$xmlLead.='<id source="chrysler-source-id">'.config('ore.dealer_sourceid').'</id>';
					$xmlLead.='<name part="full" type="business">'.config('ore.dealer_businessname').'</name>';
				}else{
					$xmlLead.='<id source="chrysler-source-id">'.config('ore.website_sourceid').'</id>';
					$xmlLead.='<name part="full" type="business">'.config('ore.website_businessname').'</name>';
				}
				
				
				$xmlLead.='</provider>';
				$xmlLead.=' </prospect>';
				$xmlLead.='</adf>';
				
				\Log::info('   ==========  XML LOG START =============================');
				\Log::info($v_arr['lead_source']);
				\Log::info($xmlLead);
				\Log::info('   ==========  XML LOG END =============================');
				
				return \Ore::XML_SEND_LEAD($xmlLead, $v_arr,  $form_type);

				
        }catch(\Exception $e){
                \Log::info($e->getMessage());
        }	
	}
	

	public function XML_SEND_LEAD($xmlLead, $vendor_experience_consolidate,  $form_type){
				
				/* -auto- for cron operation */
				if($form_type == 'auto'){
					$ore_session = $vendor_experience_consolidate['session_id']; 
					 $csession = \App\Leadsession::where(['id' => $ore_session])->select('ore_session')->first(); 
					 if ($csession->ore_session == null) {
							$currentUserSessionTableID = '';
					} else {
							$currentUserSessionTableID = $csession->ore_session;
						}
					
				}else{
					$currentUserSessionTableID = \Ore::getSessionID();
				} 
				 
			 $vendor_experience_consolidate['session_id'] = $currentUserSessionTableID; 	
		$this->Lead_Insert('Prelead', $vendor_experience_consolidate, $currentUserSessionTableID);
		
		
		try{
			$leadUrl = config('ore.lead.endpoint');
			\Log::info('   ==========  LEAD ENDPOINT =============================');
			\Log::info($leadUrl);
			\Log::info('   ==========  LEAD ENDPOINT =============================');
			$client = new \GuzzleHttp\Client();
			$res = $client->request('POST', $leadUrl,[
													 'headers' => ['Content-Type' => 'text/xml; charset=UTF8'],
													 'body' => $xmlLead
													 ]);
            $status = $res->getStatusCode(); 
			$XMLoutput = $res->getBody()->getContents();  
			 \Log::info($XMLoutput);
			$xmlObject = simplexml_load_string($XMLoutput);
			
              
			if($xmlObject->Successful=="false"){
				 
				 return ['status' => false, 'message' => $xmlObject->Message, 'description' => $xmlObject->Errors->ServiceError->Description];
			}elseif($xmlObject->LeadId!="" && $xmlObject->Successful=="true"){
				
				$vendor_experience_consolidate['lead_id'] = $xmlObject->LeadId;
				$this->Lead_Insert('Post', $vendor_experience_consolidate,  $form_type);
				 
							
						if(env('APP_ENV') != 'local' && env('APP_ENV') != 'dev' || env('APP_ENV') != 'training'){
								if(config('ore.merkle.offline_merkle')){
									if($form_type == 'auto'){					 
										try{ 
										\Log::info('   ==========  MERKLE  =============================');
											$adobeCloudID = \Databucket::isCacheHMGet('user:experience:popup:status:'.$currentUserSessionTableID, 'current_adobe_session');
											$leadID = $xmlObject->LeadId;
											$sessionid = $adobeCloudID[0];
											$this->offlineMerkle($leadID, $sessionid);
										}catch(Exception $e){
												\Log::error('AutoCron Merkle Lead Setup Issues: '.$e->getMessage());             
										}
									}	
								}
						}

				
                return ['status' => true, 'message' => $xmlObject->LeadId]; 
			}else{
				return ['status' => false, 'message' => 'Error while submitting lead, try after some time.', 'description' => ''];
            }
            
        }catch(\Exception $e){			 
            return ['status' => false, 'message' => 'Error while submitting lead, try after some time', 'description' => '']; 
            dd($e->getMessage());
			
        }

	}

	public function Lead_Insert($lead_type, $vendor_experience_consolidate,  $form_type){
		
		if($lead_type == 'Prelead'){
			Prelead::insert($vendor_experience_consolidate);

		}else if($lead_type == 'Post'){
			$vendor_experience_consolidate['lead_status'] = 'success';
			Lead::insert($vendor_experience_consolidate); 
			
			//$LEADID = $vendor_experience_consolidate['lead_id'];
			$SessionId=$vendor_experience_consolidate['session_id'];
			
			$this->SHOWROOM_QRCODE($vendor_experience_consolidate,$SessionId,  $form_type);
			
			 \Databucket::oreSession_flagupdate_query($SessionId);	
		}	
		
		RETURN TRUE;
	}
	public function SHOWROOM_QRCODE($vendor_experience_consolidate, $SessionId,  $form_type){
		
		$v_arr1 = $vendor_experience_consolidate;
		 
		$dealer_code = Databucket::isCacheHMGet($v_arr1['vehicle_vin'], 'dealer_code');
		$service_ishowroom=$tradein_ishowroom=$CreditRequested="false";
		
		if($v_arr1['service_protection'] != null && (strpos($v_arr1['service_protection'], 'Lease -') !== false) || (strpos($v_arr1['service_protection'], 'Finance -') !== false))  $service_ishowroom='true';
		
		if($v_arr1['tradein_year'] != null) $tradein_ishowroom='true';
		if($form_type=='rone') $CreditRequested='true';
		 
		\Log::info("   === SHOWROOM START====="); 		 
		\Log::info("{\n    \"ChryLeadID\": \"".$SessionId."\",\n    \"DealerCode\": \"".$dealer_code[0]."\",\n    \"VIN\": \"".$v_arr1['vehicle_vin']."\",\n    \"FirstName\": \"".$v_arr1['first_name']."\",\n    \"LastName\": \"".$v_arr1['last_name']."\",\n    \"PrimaryEmail\": \"".$v_arr1['email']."\",\n    \"PrimaryPhone\": \"".$v_arr1['phone']."\",\n    \"PrimaryPhoneType\": \"\",\n    \"SecondaryEmail\": \"\",\n    \"SecondaryPhone\": \"\",\n    \"SecondaryPhoneType\": \"\",\n    \"Address1\": \"".$v_arr1['streetline1']."\",\n    \"Address2\": \"".$v_arr1['streetline2']."\",\n    \"City\": \"".$v_arr1['city']."\",\n    \"State\": \"".$v_arr1['state']."\",\n    \"ZipCode\": \"".$v_arr1['zip']."\",\n    \"TradeInVehicle\": ".$tradein_ishowroom.",\n    \"CreditRequested\": ".$CreditRequested.",\n    \"EstimatedPayment\": \"".$v_arr1['estimated_owed']."\",\n    \"MoparProtection\": ".$service_ishowroom."\n}");		 
		\Log::info("   === SHOWROOM END ====="); 
		 
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => config('ore.ishowroom.endpoint'),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "{\n    \"ChryLeadID\": \"".$SessionId."\",\n    \"DealerCode\": \"".$dealer_code[0]."\",\n    \"VIN\": \"".$v_arr1['vehicle_vin']."\",\n    \"FirstName\": \"".$v_arr1['first_name']."\",\n    \"LastName\": \"".$v_arr1['last_name']."\",\n    \"PrimaryEmail\": \"".$v_arr1['email']."\",\n    \"PrimaryPhone\": \"".$v_arr1['phone']."\",\n    \"PrimaryPhoneType\": \"\",\n    \"SecondaryEmail\": \"\",\n    \"SecondaryPhone\": \"\",\n    \"SecondaryPhoneType\": \"\",\n    \"Address1\": \"".$v_arr1['streetline1']."\",\n    \"Address2\": \"".$v_arr1['streetline2']."\",\n    \"City\": \"".$v_arr1['city']."\",\n    \"State\": \"".$v_arr1['state']."\",\n    \"ZipCode\": \"".$v_arr1['zip']."\",\n    \"TradeInVehicle\": ".$tradein_ishowroom.",\n    \"CreditRequested\": ".$CreditRequested.",\n    \"EstimatedPayment\": \"".$v_arr1['estimated_owed']."\",\n    \"MoparProtection\": ".$service_ishowroom."\n}",  
				   
				  CURLOPT_HTTPHEADER => array(
					"Accept: */*",
					"Cache-Control: no-cache",
					"Connection: keep-alive",
					"Content-Type: application/json", 
					"accept-encoding: gzip, deflate",
					"cache-control: no-cache", 
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				   \Log::info('Error in iShowroom Response');
					\Log::info($response);
				} else {
					\Log::info('WE Received iShowroom Response');
					\Log::info($response);
				}
				return true;
	}
	
	
	public function Verify_SoldStatus(){
		return strtolower(\Ore::cacheGet('status'));
	}
	 
	public function getRefSessionID($table_field, $reference_id){
		  $mycount = \App\Leadsession::where($table_field,'=',$reference_id)->count();
		  
		  if($mycount > 0){
				 $data = \App\Leadsession::where($table_field,'=',$reference_id)->first();
				 
				  
				if ($data->id == null) {
					return false; 
				} else {
					return $data->id; 
				}
		}else return false;
	}
	
	public function getORESessionID($table_field, $reference_id){
		 
		 $data = \App\Leadsession::where($table_field,'=',$reference_id)->first();
		if ($data->routeone_refid == null) {
			return false; 
		} else {
			return $data->routeone_refid; 
		}
	}
	public function getORECarnowSessionID($table_field, $reference_id){
		 
		 $data = \App\Leadsession::where($table_field,'=',$reference_id)->first();
		if ($data->carnow_refid == null) {
			return false; 
		} else {
			return $data->carnow_refid; 
		}
	}
	public function getTierID($vendor , $reference_id){
		 
		 $data = \App\Leadsession::where($vendor,'=',$reference_id)->first();
		if ($data->source == null) {
			return false; 
		} else {
			return $data->source; 
		}
	}
	
	public function is_array_key($array, $key){
		 
		if (array_key_exists($key,$array)){
			return $array[$key];
		} else  return '';
	}
	public function is_array_key2($array, $key, $key2){
		 
		if (array_key_exists($key,$array)){
			if (array_key_exists($key2,$array[$key])){
				return $array[$key][$key2];
			} else return '';
		} else  return '';
	}

	public function referenceIDGenerator($type, $string){
		if($string=='') $string = uniqid();
		switch($type){
			case "Routeone":
				return substr($string, 0, 12);
			break;
			case "credit700":
				return substr($string, 0, 12);
			break;
			case "carnow":
				return substr($string, 0, 16);
			break;			
			default:
			break;	
			
		}
	}
	
	public function mungXML($xml)
	{
    $obj = SimpleXML_Load_String($xml);
    if ($obj === FALSE) return $xml;

    // GET NAMESPACES, IF ANY
    $nss = $obj->getNamespaces(TRUE);
    if (empty($nss)) return $xml;

    // CHANGE ns: INTO ns_
    $nsm = array_keys($nss);
    foreach ($nsm as $key)
    {
        // A REGULAR EXPRESSION TO MUNG THE XML
        $rgx
        = '#'               // REGEX DELIMITER
        . '('               // GROUP PATTERN 1
        . '\<'              // LOCATE A LEFT WICKET
        . '/?'              // MAYBE FOLLOWED BY A SLASH
        . preg_quote($key)  // THE NAMESPACE
        . ')'               // END GROUP PATTERN
        . '('               // GROUP PATTERN 2
        . ':{1}'            // A COLON (EXACTLY ONE)
        . ')'               // END GROUP PATTERN
        . '#'               // REGEX DELIMITER
        ;
        // INSERT THE UNDERSCORE INTO THE TAG NAME
        $rep
        = '$1'          // BACKREFERENCE TO GROUP 1
        . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
        ;
        // PERFORM THE REPLACEMENT
        $xml =  preg_replace($rgx, $rep, $xml);
    }
    return $xml;
}
	
	public function Lead_SessionID_Validator($reference_id){
		
	 /*	$currentSession = $this->getSessionID(); 
		$uniqid = uniqid();
		
		$record = Session::where(array('ore_session' => $currentSession))->first();
		 if (is_null($record)) {
				$session =  Session::create(array('ore_session' => $currentSession)); 
			} else {
				 dd($record);
			}
		  */
	}
public function mophar_function($array, $type){
		$leadServices  = ''; 
		$cSession = md5('userinfo' . \Ore::getSessionID());
		$service_data=$this->serviceprotectionjson($cSession);
		 
		if($type == 'lease'){
		if(count($array) > 0){
				$leadServices .= "";
				foreach($array as $key=>$value){
							// switch($value){
							// 	case $value: $leadServices .= $service_data[$value]; break;
							// 	// case 2: $leadServices .= "Lease - Lease Wear and Tear, "; break;
							// 	// case 3: $leadServices .= "Lease - Auto Appearance Care, "; break;
							// 	// case 4: $leadServices .= "Lease - Lease Protect, "; break;
							// 	// case 5: $leadServices .= "Lease - Plantinum Lease, "; break;
							// 	// case 6: $leadServices .= "Lease - Scheduled Maintenance, "; break;
							// 	// case 7: $leadServices .= "Lease - Premium Care, "; break;
							// }
							$leadServices .= "Lease -".$service_data['lease'][$value]['title'];
				}
				
				$leadServices = substr(trim($leadServices), 0, -1);
			}
		}
		 
		if($type == 'finance'){
			if(count($array) > 0){
				$leadServices .= "";
				foreach($array as $key=>$value){
							// switch($value){
							// 	case 1: $leadServices .= " Finance - Maximum Care, "; break;
							// 	case 2: $leadServices .= "Finance - Added Care Plus, "; break;
							// 	case 3: $leadServices .= "Finance - Road Hazard Tire and Wheel Protection, "; break;
							// 	case 4: $leadServices .= "Finance - Auto Appearance Care,"; break;
							// 	case 5: $leadServices .= "Finance - GAP Plans, "; break;
							// 	case 6: $leadServices .= "Finance - Scheduled Maintenance, "; break;
							// }
							$leadServices .= "Finance -".$service_data['finance'][$value]['title'];
				}
				$leadServices = substr(trim($leadServices), 0, -1);
			}
		}
		//dd($leadServices);
		return $leadServices;
	}
	
	public function dealerFetchByID($dealerID){
		$alldealers2 = json_decode(\Ore::cacheGet('alldealers:'), true);
		$alldealers = array_column($alldealers2,'dlr_dba_name','dlr_code');
		 
		if(array_key_exists($dealerID, $alldealers )){
			return $alldealers[$dealerID];
		}else return ""; 
	}
	
	
	public function offlineMerkle($leadID,$adobeCloudID){ 
		try{ 
				date_default_timezone_set('EST');
				
				$offline['adobe_cloud_id'] = $adobeCloudID;
				$offline['name'] = 'ORE OFFLINE';
				$offline['lead_id'] = $leadID;
				$offline['event1'] = 1;				
				$offline['event2'] = 1;
				$offline['lead_date_timestamp'] = \Carbon\Carbon::now('America/New_York')->toIso8601String();
				MerkleOffline::insert($offline); 
				
				$offlineDate = MerkleOffline::whereRaw("DATE(`lead_date`) = CURDATE()")->get();
				
				$txtExt = '.txt';	
				$finExt = '.fin';	
				$fileNameCreate = "orelead".date('MdY');
				
				if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'training' || env('APP_ENV') == 'test'){ 			 		
							$fileName = 'staging_ore_server/'.$fileNameCreate.$txtExt; 
							$fileNameTxt = 'staging_ore_server/'.$fileNameCreate.$txtExt; 
							$fileNameFin = 'staging_ore_server/'.$fileNameCreate.'-fin'.$txtExt; 
							$fileNameFinPath = 'staging_ore_server/'.$fileNameCreate.$finExt; 
				}else{
							$fileName = $fileNameCreate.$txtExt; 
							$fileNameTxt = $fileNameCreate.$txtExt; 
							$fileNameFin = $fileNameCreate.'-fin'.$txtExt; 
							$fileNameFinPath = $fileNameCreate.$finExt; 
				} 
				$emptyTxt = '';
				
                $txt = "# Generic Data Source (Summary Data Only) template file (user: 100093701 ds_id: 2)\n";
				$txt .= "#\tAdobe Visitor ID (v64)\tLead Form Name (Global US) (v2)\tForm Transaction ID (v80)\tLead Form Completion (Global US) (e1)\tForm Completion (Global US) (e24)\n";
				$txt .= "date\tevar64\teVar2\tevar80\tevent1\tevent24\n";	
				 
				foreach($offlineDate as $key => $val){ 
					$merkle_time = $m_o_time = $val['lead_date_timestamp'];
					 $merkle_time = substr($m_o_time, 5, 2).'/';
					$merkle_time .= substr($m_o_time, 8, 2).'/';
					$merkle_time .= substr($m_o_time, 0, 4).'/';
					$merkle_time .= substr($m_o_time, 11, 2).'/';
					$merkle_time .= substr($m_o_time, 14, 2).'/';
					$merkle_time .= substr($m_o_time, 17, 2); 
					
                     //$txt .=    $merkle_time."\t".$val['adobe_cloud_id']."\t".$val['name']."\t".$val['lead_id']."\t".$val['event1']."\t".$val['event2']."\n";
					 $txt .=	$merkle_time."\t".$val['adobe_cloud_id']."\t".$val['name']."\t\t".$val['event1']."\t".$val['event2']."\n";
				}
                /*\Log::info('----------------------- Merkle offline ------------------------------------------------');
				\Log::info($txt);*/
				Storage::disk('offline-merkle')->put($fileName, $txt);
				Storage::disk('offline-merkle')->put($fileNameFin, "");
				$localFileTxt =  Storage::disk('offline-merkle')->get($fileName);  
				$localFileFin =  Storage::disk('offline-merkle')->get($fileNameFin);  
				
				
				Storage::disk('merkle-lead-ftp')->put($fileNameTxt, $localFileTxt);
				Storage::disk('merkle-lead-ftp')->put($fileNameFinPath, $localFileFin);
		}catch(Exception $e){
            \Log::error('FTP Upload Issues: '.$e->getMessage());             
        }
	}


    /**
    * Discount functions
    */

    function dealerDiscountCalculationforVin($VinNumber,$financeoption){
        if(!\Databucket::isCacheExists($VinNumber)){
            $datass = \App\Vehicle::where(['vin' => $VinNumber])->first();                
        }else{
            $datass = \Ore::isCacheGetAll($VinNumber);            
        }
        $result = 0;
        $dealerid = $datass['dealer_code'];
        if(empty($dealerid)){
            return ['discount_text' => '','total_discount_amount' => 0 ];
        }
        $vin_info = array(
            'dealer_code' => $datass['dealer_code'],
            'make' => $datass['make'],
            'year' => $datass['year'],
            'model' => $datass['model'],
            'trim_desc' => $datass['trim_desc'],
            'vin' => $VinNumber
        );
        $price = $datass['msrp'];
        $discountAmount = 0;
        $list_discount_array = array();
        $list_discount_html = '';
        $list_discount_cmts = '';
        if($financeoption == 'Cash'){
            $financeoption =  3;
        }else if($financeoption == 'Finance'){
            $financeoption =  2;
        }else{
            $financeoption =  1;
        }
       
         $list_discount = $this->SqlFiltergroups($vin_info, $financeoption);
            if(!empty($list_discount)){
                $list_discount_array = $list_discount->toArray();
                foreach($list_discount_array as $list_discount_array_key => $list_discount_array_v){
                    $list_discount_array_value = (array)$list_discount_array_v;
                    if($list_discount_array_value['flat_rate'] == null || empty($list_discount_array_value['flat_rate']) || 
                        $list_discount_array_value['flat_rate']==0 ){ 
                            //if($list_discount_array_value['saved_discount'] == 1){
                                $p_cent = 0;
                                $p_cent = round(($list_discount_array_value['percent_offer'] / 100 )*  $price); 
                                $discountAmount +=  $p_cent;
                                $list_discount_html .= $list_discount_array_value['name_of_discount'].' ('.$list_discount_array_value['percent_offer'].' % - '.$p_cent.'),';
                            //}                            
                    }else{  
                            //if($list_discount_array_value['saved_discount'] == 1){ 
                                $discountAmount += $list_discount_array_value['flat_rate'];
                                $list_discount_html .= $list_discount_array_value['name_of_discount'].' ($'.$list_discount_array_value['flat_rate'].'),';
                            //}                         
                    }
                } 
                if(!config('ore.discounts.maxAmount5000Allowed')){
                    if(5000 < $discountAmount){
                        $discountAmount = 5000;
                    }
               }
               $list_discount_html .=(0 < $discountAmount) ? ' Total Discount:$'.$discountAmount : '';
               $list_discount_cmts = 'Dealer Discount: '. !empty($list_discount_html) ? $list_discount_html : '$0';
            }else{
                $list_discount_cmts = 'Dealer Discount: $0';
            }
            $response = ['discount_text' => $list_discount_cmts,'total_discount_amount'=>$discountAmount];
            
        //$this->payment_calculator['dlrDiscLists']   = $list_discount_html;       
       return $response;
    }

    public function SqlFiltergroups($vin_info, $financeoption){
        $level4 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'],'trim' => $vin_info['trim_desc'] ]);
                                            
        $count_level4 = $level4->count();
        \Log::info('L4: '.$count_level4);
        if($count_level4 == 0){
       
                $level3 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'], 'trim' => NULL  ]);
                $count_level3 = $level3->count();
                \Log::info('L3: '.$count_level3);
                if($count_level3 == 0){
               
                $level2 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'],'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => NULL,'trim' => NULL]);
               
                    $count_level2 = $level2->count();
                    \Log::info('L2: '.$count_level2);
                    if($count_level2 == 0){
                           
                            $level1 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'],'model' => NULL,'trim' => NULL,'model_year' => NULL]);
                            $count_level1 = $level1->count();
                            \Log::info('L1: '.$count_level1);
                            if($count_level1 == 0){ $output = []; } else {$output = $level1->get(); }
                           
                    }else $output = $level2->get();
                   
                } else $output = $level3->get();
        }else $output = $level4->get();
       
       $vin = $vin_info['vin'];
       $dealer_code = $vin_info['dealer_code'];
       if(count($output) == 0){
            $result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin); 
             
            return $result;
        }
        ####Include Vins
        if(!$output->isEmpty()){
            $filter_group = $output->toArray();
            
            $includevins = $filter_group[0]['includevins'];
            $excludevins = $filter_group[0]['excludevins'];
            $vinFoundFlag = $excludeFound = false;
            if(!empty($includevins)){
                 $vinFoundFlag = $this->checkValueExistsinArray($vin,$includevins);
                 if($vinFoundFlag){
                    $result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
                    
                    if(0 < count($result)){
                        \Log::info('not empty includevins');
                        return $result;                             
                    }                       
                 }
            }
            if(!empty($excludevins)){
                if(!$vinFoundFlag){
                    $vinFoundFlag = $this->checkValueExistsinArray($vin,$excludevins);
                    if($vinFoundFlag){
                        \Log::info('excludevins ------------------------------------------');
                        $result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
                        \Log::info($result);
                        if(0 < count($result)){
                            \Log::info('not empty excludevins');
                            return $result;                             
                        }   
                    }                    
                }
            }
            if(!empty($includevins) || !empty($excludevins)){
                if(!$vinFoundFlag){
                    \Log::info('individualdiscount ------------------------------------------');
                    $individual_result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin);
                    \Log::info($individual_result);
                    if(0 < count($individual_result)){
                        return $individual_result;
                    }
                    \Log::info('filtergroupdiscounts ------------------------------------------');
                    $result = $this->sqlFilterRulediscount($dealer_code,$filter_group[0]['id'],$financeoption);             
                    \Log::info($result);
                    if(0 < count($result)){
                        \Log::info('not empty filtergroupdiscounts');
                        return $result;                             
                    }               
                }
            }
        }
        $result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin);  
        \Log::info('individualdiscount ------------------------------------------');
        \Log::info($result);
        return $result;               
    }
    public function sqlindividualdiscountforvin($dealer_code,$finance_option,$VinNumber)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'v.vin' => $VinNumber,'f.finance_option' => $finance_option])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode')->get();
    }

    public function sqlRulediscountforvin($dealer_code,$filterGroupId,$VinNumber,$finance_option)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'v.vin' => $VinNumber,'f.finance_option' => $finance_option,'d.rule_flag' => '1','fl.filtergroup_id' => $filterGroupId])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode')->groupBy('d.discount_name')->get();
    }

    public function sqlFilterRulediscount($dealer_code,$filterGroupId,$finance_option)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'fl.filtergroup_id' => $filterGroupId,'d.rule_flag' => '1','f.finance_option' => $finance_option])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode')->groupBy('d.discount_name')->get();
    }

    public function checkValueExistsinArray($value,$array)
    {
        if(empty($array)){
            return false;
        } 
        $str_array = explode(',', $array);
        return in_array($value, $str_array);
    }
}    
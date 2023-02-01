<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis; 
use App\Facades\OreDSClass;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str ;
use Carbon\Carbon;
use App\Dlrmgdiscount; 

use DB;   

class CalculatorController extends Controller
{	
	private $pre_owner 		   			 = 'No Previous Ownership Requirement';
	private $grp_aff 		   			 = 'No Specific Group Affiliation'; 
	private $incentive_amount  			 = 0; 
	private $explore_amount 			 = 0;
	private $msrp_price 				 = 0;
	private $man_incentives_id 			 = []; 
	private $man_incentives_name		 = [];
	private $man_incentives_sin_name	 = [];
	private $man_incentives_disclaimer_name = [];
	private $man_incentives_exp_name = [];
	 	
	private $rebateIDColletions			 = [];	
	private $explore_checkes 		     = [];
	private $responseRebateDetails		 = [];
	
	private $paymentWithTaxesVal_inarray = ['double','integer','float'];	
	private $paymentWithTaxesKey_inarray = ['amountFinanced','monthlyPayment','monthlySalesTax','inceptionFees'];
	
	private $explore_inarray 			 = ['Loyalty','Lease Loyalty','Military','Automobility','Lease Conquest','Conquest','First Responders'];	
	 
	
	private $date_validation 			 = true;
	private $dummy_activate 			 = false;
	private $haveTaxes;
	
	private $defaultFinanceType	;
	
	private $Dlrmgdiscount;
	
	private $transactionDiscountType;
	
	private $vin_info;
	
	private $total_applicable_terms;
	private $lease_applicable_terms;
	private $lease_default_terms;
	private $finance_applicable_terms;
	private $finance_default_terms;
	

	private $incentivesBonusCash_available = false;
	private $incentivesBonusCash_amount = 0;
	
	private $financeSourceName;
	
	private $request_vin, $request_transactionType, $request_zipcode, $request_down;
	private $request_tradein, $request_term, $request_rebateIDs2, $request_mileage;
	private $request_methods, $request_dealer_disc, $request_f_dealer_disc; 
	private $isCCAPAvailale; 

	private $finance_tier, $lease_tier, $cash_tier;
	private $nonccap_disclaimer, $ccap_disclaimer, $salescode_disclaimer, $incentiveBonus_disclaimer;
	private $request_lease_additional_disc, $request_finance_additional_disc, $request_cash_additional_disc;
	private $salesCodesArray;

	private $partner_id;
	/*
	* return array
	*/
	private $payment_calculator = array();
	
	public function __construct(Dlrmgdiscount $Dlrmgdiscount)
    {  
		//\Log::info(' ********* ENVIRONMENT *****************');
		
		 date_default_timezone_set('Etc/GMT');
		 
		 $this->Dlrmgdiscount			 = $Dlrmgdiscount;
		 $this->endpoint 				 = config('ore.calc.endpoint');
		 $this->accessKeyId 			 = config('ore.calc.accessKeyId');
		 $this->routeoneSecret 			 = config('ore.calc.routeoneSecret'); 		
		 $this->XRouteOneActAsDealership_name = 'x-routeone-act-as-dealership-partner-id';		 
		 $this->XRouteContentType 		 = config('ore.calc.XRouteContentType');
		 $this->financeSource 		 	 = config('ore.calc.financeSource');
		 $this->tier 		 			 = config('ore.calc.tier');
		 $this->defaultFinanceType	     =  config('ore.calc.financeName'); 
		 $this->XRouteDate 				 = date('D, d M Y H:i:s \g\m\t');
		 $this->today 					 = date("Y-m-d"); //,  strtotime("+2 month")		 
		 $this->isActive_dlr_discount	 = config('ore.calc.dealer_discount'); 
		 
		 		 
		 $this->payment_calculator['restrict'] = false;
		
		 $this->haveTaxes = (config('ore.calc.taxes')) ? 'paymentWithTaxes' : 'paymentWithoutTaxes';
		 
		 $this->payment_calculator['incentiveIds']     	=     ($this->man_incentives_id);
		$this->payment_calculator['incentiveNames'] 	=     $this->man_incentives_name;
		$this->payment_calculator['man_incentives_sin_name'] 	=     $this->man_incentives_sin_name;
        $this->payment_calculator['incentiveAmount']	=     (int)$this->incentive_amount; 		
        $this->payment_calculator['explores']        	=     $this->explore_checkes; 
		$this->payment_calculator['incentive_disclaimer']        	=    $this->man_incentives_disclaimer_name;
		$this->payment_calculator['incentive_expdate']        	=    $this->man_incentives_exp_name;
		

		$this->total_applicable_terms 	= config('ore.calc.total_applicable_terms');
		$this->lease_applicable_terms 	= config('ore.calc.lease_applicable_terms');
		$this->lease_default_terms 		= config('ore.calc.lease_default_terms');
		$this->finance_applicable_terms = config('ore.calc.finance_applicable_terms');
		$this->finance_default_terms 	= config('ore.calc.finance_default_terms');

		 $this->payment_calculator['incentivesBonusCash_available']	=     false;		
        $this->payment_calculator['incentivesBonusCash_amount']        	=     0;
		$this->salesCodesArray = ['CCAP', 'diesel', 'engine', 'transmission', 'package', 'v6', 'v8', 'hemi', 'srt'];
		$this->payment_calculator['salesCodesArray']    =   $this->salesCodesArray;
		
		$this->financeSourceName ='ChryslerCapital';

		$this->partner_id = '';

		$this->ccap_disclaimer = 'When Financed through Chrysler Capital. Not all buyers will quality. Restrictions apply. See Dealers for details. Must take retail delivery by ';
		$this->salescode_disclaimer = 'Incentive is applicable only if the vehicle has certain Options / Equipment. Incentive is applicable only if the vehicle has certain specific options. Restrictions apply. See Dealers for details. Must take retail delivery by ';
		$this->nonccap_disclaimer = 'Restrictions apply. See Dealers for details. Must take retail delivery by ';
		$this->incentiveBonus_disclaimer = array(
			'29HL1' => 'Offer is available on the purchase of a new 2019 Wrangler or new 2020 model year Gladiator (excluding Rubicon and Mojave) or Wrangler (excluding Rubicon and Rubicon Recon). Customer responsible for $200 administration fee. Not compatible with subvented APR offers. Must take retail delivery by 6/1/2020. See dealer for details.',
			'29HK1' => 'Offer is available on the purchase of a new 2019 Wrangler or new 2020 model year Gladiator (excluding Rubicon and Mojave) or Wrangler (excluding Rubicon and Rubicon Recon). Customer responsible for $200 administration fee. Not compatible with subvented APR offers. Must take retail delivery by 6/1/2020. See dealer for details.',
			'29HLF' => 'Offer is available on the purchase or lease of a new 2020 Pacifica or Pacifica Hybrid. Customer responsible for $200 administration fee. Must take retail delivery by 6/1/2020. See dealer for details.'
			//'' => 'Offer is available on the purchase of a new 2019 Ram 2500 or 3500 pickup. Customer responsible for $200 administration fee. Not compatible with subvented APR offers. Must take retail delivery by 6/1/2020. See dealer for details.'
			);
    } 
	
	public function PaymentCalculator(Request $request){
		$vin 				= $request->get('vin'); 
		$transactionType	= $request->get('transactionType');
		$zipcode			= $request->get('zipcode');
		$down				= $request->get('down'); 
		$tradein			= $request->get('tradein');
		$term				= $request->get('term'); 
		$rebateIDs2			= $request->get('rebateIDs');
		$mileage			= $request->get('mileage');
		$methods			= $request->get('methods');
		$dealer_disc		= $request->get('dealer_disc');
		$f_dealer_disc		= $request->get('f_dealer_disc');
		$lease_additional_disc		= $request->get('lease_additional_disc');
		$finance_additional_disc		= $request->get('finance_additional_disc');
		$cash_additional_disc		= $request->get('cash_additional_disc');

		
		$filters = array();		
		$filters['make'] = \Databucket::isCacheHMGet($vin, 'make');
		$filters['model'] = \Databucket::isCacheHMGet($vin, 'model');
		$filters['trim_desc'] = \Databucket::isCacheHMGet($vin, 'trim_desc'); 
		$filters['year'] = \Databucket::isCacheHMGet($vin, 'year'); 
		$filters['dealer_code'] = \Databucket::isCacheHMGet($vin, 'dealer_code');
		$filters['vin'] = $vin;
		
		/* Dealer Zipcode */
		$myDealerCode = $this->get_dealer_zipcode($filters['dealer_code'][0], $zipcode); 
		
		$zipcode = str_pad($myDealerCode, 5, 0, STR_PAD_LEFT); 
		
		$this->request_vin 				= $vin;
		$this->request_transactionType 	= $transactionType; 
		$this->request_zipcode 			= $zipcode; 
		$this->request_down 			= $down; 
		$this->request_tradein 			= $tradein; 
		$this->request_term 			= $term; 
		$this->request_rebateIDs2 		= $rebateIDs2; 
		$this->request_mileage 			= $mileage; 
		$this->request_methods 			= $methods; 
		$this->request_dealer_disc 		= $dealer_disc; 
		$this->request_f_dealer_disc 	= $f_dealer_disc;
		$this->transactionDiscountType 	= $transactionType;
		$this->request_lease_additional_disc 	= $lease_additional_disc;
		$this->request_finance_additional_disc 	= $finance_additional_disc;
		$this->request_cash_additional_disc	= $cash_additional_disc;
		$this->vin_info = $filters; 
		
		if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test'){
				$this->XRouteOneActAsDealership = '99970';
				$finance_type		= $this->defaultFinanceType; 
				$this->payment_calculator['finance_type'] =$finance_type; 
				$this->verifyPaymentMethodAllocation($vin, $transactionType);  	
		}else{ 
				$finance_type		= $this->defaultFinanceType;
				$this->payment_calculator['finance_type'] = $this->defaultFinanceType;	
				$this->XRouteOneActAsDealership = str_pad($request->get('dlr_code'), 5, 0, STR_PAD_LEFT);						
		} 

		$this->payment_calculator['original_downpayment'] = $down;

		/*
		* Mdoca dummy dealer code maintain for staging server. It's lookup with fca_ore_temprorary_routeone_dealers
		* Prodcution server actual dealer and zipcode passing
		*/ 
		if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test'){
					// \Log::info(' local / dev / test / Staging Env'); 
					// $mdoca_list = \Databucket::mdoca_availability($filters['dealer_code'][0]);
					// $this->accessKeyId 			 = config('ore.calc.accessKeyId');
					
				
					// 		if(config('ore.calc.routeone_tmp_delaercode')){   
					// 					$data = \DB::Select('select rone_dealer_id from fca_ore_dealer_info AS d LEFT JOIN  fca_ore_temprorary_routeone_dealers AS tmp ON
					// 					tmp.state_abbreviation = d.dlr_shw_state WHERE d.dlr_code = ?', [$filters['dealer_code'][0]]);

					// 					if(count($data) > 0){
					// 						if(Arr::has($data,0)){
					// 							$data1 = $data[0];
					// 							if(property_exists($data1,'rone_dealer_id'))	
					// 								$this->XRouteOneActAsDealership = $data1->rone_dealer_id;
					// 						}
											
					// 					}  
					// 		} 
 

							$isAlternateDealerCode = $this->get_mdoca_alternate_dealer_code($filters['dealer_code'][0]);
							$this->XRouteOneActAsDealership = str_pad($isAlternateDealerCode, 5, 0, STR_PAD_LEFT);
							$this->accessKeyId 			 = config('ore.calc.accessKeyId'); 
			 
		
		}else{  
							$isAlternateDealerCode = $this->get_mdoca_alternate_dealer_code($filters['dealer_code'][0]);
							$this->XRouteOneActAsDealership = str_pad($isAlternateDealerCode, 5, 0, STR_PAD_LEFT);
							$this->accessKeyId 			 = config('ore.calc.accessKeyId'); 
		}




			// \Log::info($this->XRouteOneActAsDealership); 
			// \Log::info($zipcode);
			// \Log::info($this->accessKeyId);  

			foreach($this->salesCodesArray as $key => $val){
					$isKey = 'is'.$val.'Availale';
					$this->payment_calculator[$isKey]        	=     false; 
			}
			

		/* Don't change the place/order of exection*/
		if(config('ore.calc.incentivesBonusCash')){
			$this->incentivesBonusCash($vin,$filters['make'][0],$filters['dealer_code'][0]);
		}  
		
		if($finance_type == 'ChryslerCapital'){ 
			$this->financeSource = 'F00CHC'; 
			$this->finance_tier='A1'; 
			//if(strtolower($filters['make'][0])=='fiat') $this->finance_tier = '1'; else $this->finance_tier = 'A1';
			$this->lease_tier=1;
			$this->cash_tier=1;
		}else if($finance_type == 'AllyFinancialInc'){ 
			$this->financeSource = 'F000BA'; 
			//$this->tier = 'S';
			$this->finance_tier='S'; 
			$this->lease_tier='S';
			$this->cash_tier='S';
		}else{ $this->financeSource = 'F000BA'; $this->tier = 'S';
		} 
		
		$this->CalculatorIncentives($vin, $transactionType);
		
		if(config('ore.calc.rules')){
			$this->financeSourceName = $finance_type;
			
		}
		/********************************  Configuration Done *********************************************/
		/********************************  ************** *************************************************/
		/********************************  ************** *************************************************/
		
		if(empty($rebateIDs2)) $rebateIDs=[];
		else $rebateIDs = $rebateIDs2;

		/** 
		*  If two stockability shows in any incentive, handle it. 
		**/
		
		try{
			if(empty($rebateIDs2)) $rebateIDs=[];
			else {
				$rebateIDs=[];
				foreach($rebateIDs2 as $key => $value){
					$v2 = explode(',',$value); 
					if(count($v2) > 1){
						foreach($v2 as $vkey => $vvalue){ 
							array_push($rebateIDs, (int)$vvalue);
						} 
					}else{ 
						array_push($rebateIDs, (int)$v2[0]);
					} 
				} 
			}
		}catch(Exception $e){
            \Log::error('Array Merge Issue in PaymentCalculator=>PaymentCalculator');
             
        }
		
		try{  
            if(gettype($this->man_incentives_id) == 'string') $this->man_incentives_id = array(0=>str_replace('"','',$this->man_incentives_id));
            
            if(count($this->man_incentives_id) > 0) {
					if(count($rebateIDs) > 0) $AllIncentiveIds = array_merge($rebateIDs, $this->man_incentives_id);
					else $AllIncentiveIds =$this->man_incentives_id;
			}
			else $AllIncentiveIds = $rebateIDs; 
			
			$this->payment_calculator['AllIncentiveIds']= $AllIncentiveIds;
			 
			if(Arr::has($this->payment_calculator, 'explores')){
			$explore_response 			= $this->payment_calculator['explores'];
			 		
			foreach($explore_response as $key => $val){
				$temp = array();
				$temp = explode(',', $val['ids']);
				if(count($temp) > 1){
					foreach($temp as $key_temp => $val_temp){
						if(in_array($val_temp, $rebateIDs)){
							$this->explore_amount += $val['amount'];
						} 
					}
				}else{
					if(in_array($val['ids'], $rebateIDs)){
						$this->explore_amount += $val['amount'];
					} 
				}
				 
			} 
			
			
			$this->payment_calculator['explore_amount'] = $this->explore_amount;
			}else {
				$this->payment_calculator['explore_amount'] = 0;
				$this->payment_calculator['explores'] = array();
			}
			
        }catch(Exception $e){
            \Log::error('Array Merge Issue in PaymentCalculator=>PaymentCalculator');
             
        }


		  
		  if($this->transactionDiscountType=='finance' || $this->transactionDiscountType=='lease'){
		   		$down = $this->downCalculation($down);
		  } 

		$fallbackCalcTier = '';
		if($this->transactionDiscountType=='finance'){
				$fallbackCalcTier = 'fallback:calctier:'.$vin.':'.$this->transactionDiscountType;
				$f_tier = \Databucket::isCacheHMGet($fallbackCalcTier, 'value'); 
				if($f_tier[0] != null && $f_tier[0] !=''){
					$this->finance_tier = '1'; 
				}
		} 

         if($transactionType=='lease') {			
             $jsonArray = $this->ArrayBuilderLease($vin, $transactionType, $zipcode, $down, $tradein, $term, $AllIncentiveIds, $mileage);
         }     
         else if($transactionType=='finance') {
             $jsonArray = $this->ArrayBuilderFinance($vin, $transactionType, $zipcode, $down, $tradein, $term, $AllIncentiveIds);
         }     
         else if($transactionType=='cash') { 
            $jsonArray = $this->ArrayBuilderCash($vin, $zipcode, $tradein,$AllIncentiveIds);
         }
         else { $jsonArray = array(); }
		
        $this->payment_calculator['developer']['arraybuilder'] = $jsonArray;
          
	//	 \Log::info(' ---- jsonArray Array\'s ----');
	 //  \Log::info($jsonArray);  
				 
      $NonCashCustomerQuoteKey = $this->RouteoneCalculator($jsonArray,config('ore.calc.'.$transactionType));  
	 
	 

	 /**********
	 *   Some Specific Terms blocked in Finance 
	 *   
	 */
	 $carbon = Carbon::today();
	 $format = $carbon->format('Y-m-d');
	 if($format <= '2020-06-01' && $format >= '2020-05-06'){
	
		if((strtolower($filters['model'][0])=='pacifica hybrid' || strtolower($filters['model'][0])=='pacifica')){
				if($this->request_term == '' && $this->transactionDiscountType=='finance'){ 				
					if(count($NonCashCustomerQuoteKey['result']) > 1){
						$NonCashCustomerQuoteKey['results'] = [];
					
						foreach($NonCashCustomerQuoteKey['result'] as $key => $value){
							$v_term = $value['term'];
							$v_apr  = $value['apr']; 						
							if($v_term != 72){
								$NonCashCustomerQuoteKey['results'][$key] = $value;
							}
						}
						
						$NonCashCustomerQuoteKey['result'] = $NonCashCustomerQuoteKey['results'];
					}
				}
		 }
	 }
	 
	
	 
	  	/* 
		  * Find Minimum APR rate
		  * 
		  */
		if($this->request_term != '' && $this->transactionDiscountType=='finance'){
			if(count($NonCashCustomerQuoteKey['result']) > 1){
				$allterms =array_column($NonCashCustomerQuoteKey['result'], 'apr');
				$minapr_payload = array_keys($allterms, min($allterms));
				if(count($minapr_payload) > 0) {
					$NonCashCustomerQuoteKey['result'][0] = $NonCashCustomerQuoteKey['result'][$minapr_payload[0]];
				}else $NonCashCustomerQuoteKey['result'][0] = [];
				
			}
		}
	 
		
	  /*
	  * 	Finance Payment option only applicable
	  *		APR return zero for 84 months, Inventory cash should not applicable.
	  * 	08Apr2020 on Sathish Kumar
	  */
	  			// \Log::info(' ---- Finance Payment option only applicable  ----');
				// \Log::info($this->request_term);	
				// if($this->request_term==''){
				// 	\Log::info("Term is empty");
				// } 
				// if($this->request_term==84){
				// 	\Log::info("Term is ".$this->request_term);
				// }  
			  	// \Log::info($NonCashCustomerQuoteKey);
				//   if(count($NonCashCustomerQuoteKey['result']) == 0 && $this->transactionDiscountType=='finance'){ 

				//   } 
				

	   /*
	   * Finance tier A1 returns empty payload  
	   *	28March2020 on Sathish kumar
	   */
	   if(count($NonCashCustomerQuoteKey['result']) == 0 && $this->transactionDiscountType=='finance'){ 
		   
		   if($transactionType=='lease') { $jsonArray = $this->ArrayBuilderLease($vin, $transactionType, $zipcode, $down, $tradein, $term, $AllIncentiveIds, $mileage);
           } else if($transactionType=='finance') { 
			   $this->finance_tier = '1';
			   \Databucket::isCacheHMSet($fallbackCalcTier, 'value', $this->finance_tier); 	
			   $jsonArray = $this->ArrayBuilderFinance($vin, $transactionType, $zipcode, $down, $tradein, $term, $AllIncentiveIds);
           } else if($transactionType=='cash') {    $jsonArray = $this->ArrayBuilderCash($vin, $zipcode, $tradein,$AllIncentiveIds);
           }  else { $jsonArray = array(); }		
        	$this->payment_calculator['developer']['arraybuilder'] = $jsonArray;
			$NonCashCustomerQuoteKey = $this->RouteoneCalculator($jsonArray,config('ore.calc.'.$transactionType)); 
	   }

        if($NonCashCustomerQuoteKey['status'] == 'error'){
			 \Log::info('NonCashCustomerQuoteKey isssues in CalculatorController');
           exit;
        }
		 
		//$NonCashCustomerQuoteKey = $this->lease_sample_result();
		
	//  \Log::info(' ---- NonCashCustomerQuoteKey RESULTS ----');
	//	\Log::info($NonCashCustomerQuoteKey);
		 
		 if(count($NonCashCustomerQuoteKey['result']) == 0){
			 $this->payment_calculator['restrict'] = true;
			 $this->payment_calculator['transactionType'] = $transactionType; 
			  return $this->payment_calculator;
		 }
		 
		 
         $this->payment_calculator['terms'] = array(); 
		 $this->payment_calculator['rebateDetails'] = array();
         $paymentWithTaxesResults = array();
		  $rebateDetailsResults = array();
		 
		 if($transactionType == 'cash') $NonCashCustomerQuoteKeyResults[0] = $NonCashCustomerQuoteKey['result'];
		 else $NonCashCustomerQuoteKeyResults = $NonCashCustomerQuoteKey['result'];
         
		 for($i=24;$i<=85;$i++){
			 $programTitleVerification[$i] = 'allowed';
		 } 
		 
		 

         foreach($NonCashCustomerQuoteKeyResults as $NonCashCustomerQuoteNameKey => $NonCashCustomerQuoteVal){  
		    
		   if (array_key_exists("specialProgram",$NonCashCustomerQuoteVal)) $specialProgram = $NonCashCustomerQuoteVal['specialProgram']; 
		   
		    if (array_key_exists("programTitle",$NonCashCustomerQuoteVal)) $programTitle = $NonCashCustomerQuoteVal['programTitle']; 
			
			
			
		    
		   
		   //if($programTitleVerification=='allowed') {
			   
			 $this->rebateDetails = $NonCashCustomerQuoteVal['rebateDetails'];  
		   
			if (array_key_exists("term",$NonCashCustomerQuoteVal)) $forTerms = $NonCashCustomerQuoteVal['term']; else $forTerms = 24;
			if (array_key_exists("apr",$NonCashCustomerQuoteVal)) $paymentWithTaxesResults[$forTerms]['apr'] = $NonCashCustomerQuoteVal['apr'];
			
			if (array_key_exists("rebateDetails",$NonCashCustomerQuoteVal)) {
				 $rebateDetailsResults[$forTerms]['rebateDetails'] = "";
				 $rebateDetailsResults[$forTerms]['rebateDetailsid'] = "";
				  $rebateDetailsResults[$forTerms]['rebateDetailsfinalamount'] = 0;
				 
				foreach($NonCashCustomerQuoteVal['rebateDetails'] as $rebateDetailsKey => $rebateDetailsVal){ 
			 
				  if($rebateDetailsVal['expirationDate'] >= $this->today){
					 // \Log::info('PART - 01');
					  $rebateDetailsVal['name'] = $this->paymentCalcRules_01_labelReplace($rebateDetailsVal['name']);
					$rebateDetailsResults[$forTerms]['rebateDetails'] .= $rebateDetailsVal['name'].','; 
					$rebateDetailsResults[$forTerms]['rebateDetailsid'] .= $rebateDetailsVal['incentiveId'].','; 
					$rebateDetailsResults[$forTerms]['rebateDetailsfinalamount'] += $rebateDetailsVal['amount'];
				  }
				}
				$ends = trim($rebateDetailsResults[$forTerms]['rebateDetails'], ',');
				$ends_mandatoryincentives = trim($rebateDetailsResults[$forTerms]['rebateDetailsid'], ',');
				
				$this->payment_calculator['rebateDetailsfinalamount'] = $rebateDetailsResults[$forTerms]['rebateDetailsfinalamount'];
				 $this->payment_calculator['rebateDetails'] = $ends;
				 $this->payment_calculator['rebateDetailsid'] = $ends_mandatoryincentives; 
				 
				 
			}
			
			 
			
            array_push($this->payment_calculator['terms'], $forTerms); 
			  				
			 if($programTitleVerification[$forTerms]=='allowed') {		 
             foreach($NonCashCustomerQuoteVal[$this->haveTaxes] as $paymentWithTaxesKey => $paymentWithTaxesVal){         					 
			
				
				 if(in_array($paymentWithTaxesKey, $this->paymentWithTaxesKey_inarray))
                     {
						 
				 
                        if(in_array(gettype($paymentWithTaxesVal), $this->paymentWithTaxesVal_inarray) )
                            $paymentWithTaxesResults[$forTerms][$paymentWithTaxesKey] = number_format($paymentWithTaxesVal, 2);
                         else
                            $paymentWithTaxesResults[$forTerms][$paymentWithTaxesKey] = $paymentWithTaxesVal; 
                     }
					 // for lease Taxes
                     if($paymentWithTaxesKey=='capitalizedTaxes'){
                            foreach($paymentWithTaxesVal as $capitalizedTaxesKey => $capitalizedTaxesVal){                    
                                if($capitalizedTaxesKey=='salesTax'){ 
                                    $paymentWithTaxesResults[$forTerms][$paymentWithTaxesKey]['salesTaxAmount'] = number_format($capitalizedTaxesVal['salesTaxAmount'], 2); 
                                    $paymentWithTaxesResults[$forTerms][$paymentWithTaxesKey][$capitalizedTaxesKey]['taxParameters']['rate'] = $capitalizedTaxesVal['taxParameters']['rate'];
                                }
                            }              
                     } 
					 // for finance taxes
					if($paymentWithTaxesKey=='financeItemizedTaxes'){	
							foreach($paymentWithTaxesVal as $capitalizedTaxesKey => $capitalizedTaxesVal){                    
                                if($capitalizedTaxesKey=='salesTax'){ 
                                    $paymentWithTaxesResults[$forTerms]['capitalizedTaxes']['salesTaxAmount'] = number_format($capitalizedTaxesVal['salesTaxAmount'], 2); 
                                    $paymentWithTaxesResults[$forTerms]['capitalizedTaxes'][$capitalizedTaxesKey]['taxParameters']['rate'] = $capitalizedTaxesVal['taxParameters']['rate'];
                                }
                            }
					}	
					//for cash payment					
					if($paymentWithTaxesKey=='taxesBreakdown'){	
							foreach($paymentWithTaxesVal as $capitalizedTaxesKey => $capitalizedTaxesVal){                    
                                if($capitalizedTaxesKey=='salesTax'){ 
                                    $paymentWithTaxesResults[$forTerms]['capitalizedTaxes']['salesTaxAmount'] = number_format($capitalizedTaxesVal['salesTaxAmount'], 2); 
                                    $paymentWithTaxesResults[$forTerms]['capitalizedTaxes'][$capitalizedTaxesKey]['taxParameters']['rate'] = $capitalizedTaxesVal['taxParameters']['rate'];
                                }
                            }
					}
					if($paymentWithTaxesKey=='outTheDoorPrice'){
						$this->payment_calculator['outTheDoorPrice'] = number_format($paymentWithTaxesVal);
					} 
			 }
			  
			 
				

				
             }
			 if(Arr::has($NonCashCustomerQuoteVal, 'programName')){
			 	if($this->incentivesFindWords($NonCashCustomerQuoteVal['programName'], 'Incentivized') || $this->incentivesFindWords($NonCashCustomerQuoteVal['programName'], 'Subvented')){
					$programTitleVerification[$forTerms] = 'not-allowed'; 
				}  
			 } 
         }
		  
		 
		// \Log::info('TERMS::::::');
		// \Log::info($this->payment_calculator['terms']);
			 if($transactionType == 'cash'){
				 $this->payment_calculator['terms'] = 24;
				 $this->payment_calculator['paymentWithTaxesVal'] = 0;
				 
			 }else if($transactionType == 'lease'){
				/* $uni_terms = array_unique($this->payment_calculator['terms']); 
				Terms without config
				$uni_terms2 = $uni_terms;
				 $uni_terms = $uni_terms2; //array_unique($uni_terms2,array(24,36,48,60,72));
					rsort($uni_terms);
					 
		  
					$this->payment_calculator['terms'] = $uni_terms;
					
					if (in_array(36, $uni_terms))  
						$leaseterms = 36; 
					else if (in_array(24, $uni_terms)) 
						$leaseterms = 24; 
					else 
						$leaseterms = $uni_terms[0];
					*/					
					
					/* Terms with config */
					$uni_terms2 =  $uni_terms = array_unique($this->payment_calculator['terms']);  
					
					 $applied_terms = array_intersect($this->total_applicable_terms, $this->lease_applicable_terms); 
				 $uni_terms = array_intersect($uni_terms2,$applied_terms);
					rsort($uni_terms);
					 
					$this->payment_calculator['terms'] = $uni_terms;
				
					if (in_array($this->lease_default_terms, $uni_terms))  
						$leaseterms = $this->lease_default_terms;  
					else 
						$leaseterms = $uni_terms[0]; 
					$this->payment_calculator['default_lease'] = $leaseterms;
					
					$this->payment_calculator['paymentWithTaxesVal'] = $paymentWithTaxesResults[$leaseterms];
			 }else{
				 
				 /* Without Config */
					/* $uni_terms = array_unique($this->payment_calculator['terms']);
				 
					rsort($uni_terms); 
					$this->payment_calculator['terms'] = $uni_terms;
					$this->payment_calculator['paymentWithTaxesVal'] = $paymentWithTaxesResults[$uni_terms[0]]; */
					
					/* With Config */
					$uni_terms2 = $uni_terms = array_unique($this->payment_calculator['terms']);
					  
					$applied_terms = array_intersect( 
													$this->total_applicable_terms, 
													$this->finance_applicable_terms); 
										
				 $uni_terms = array_intersect($uni_terms2,$applied_terms);
					 
					rsort($uni_terms);
					 
					$this->payment_calculator['terms'] = $uni_terms;
				
					if (in_array($this->finance_default_terms, $uni_terms))  
						$financeterms = $this->finance_default_terms;  
					else 
						$financeterms = $uni_terms[0];
					
					
					$this->payment_calculator['default_finance'] = $financeterms; 
					$this->payment_calculator['terms'] = $uni_terms;
					$this->payment_calculator['paymentWithTaxesVal'] = $paymentWithTaxesResults[$uni_terms[0]];
			 } 
			 
			
			 
		$this->payment_calculator['transactionType'] = $transactionType;
        $this->payment_calculator['methods'] = $methods; 
		$this->payment_calculator['dealer_disc'] = $dealer_disc; 
		$this->payment_calculator['f_dealer_disc'] = $f_dealer_disc;

		$this->payment_calculator['rebateDetailsfinalamount'] 			+=  $this->payment_calculator['incentivesBonusCash_amount'];

		
		
		if($this->payment_calculator['paymentWithTaxesVal']['amountFinanced'] < 0){
			$this->payment_calculator['paymentWithTaxesVal']['amountFinanced'] = 0;
		}
		if($this->payment_calculator['paymentWithTaxesVal']['monthlyPayment'] < 0){
			$this->payment_calculator['paymentWithTaxesVal']['monthlyPayment'] = 0;
		}
		 
		
		/********** FOR LEADS ******************/
		
		$toCacheCalculator['total'] = 0;
		$toCacheCalculator['monthly'] = 0;
		
		if($transactionType == 'cash'){ 
			$this->payment_calculator['outTheDoorPrice'] = number_format(str_replace(',','',$this->payment_calculator['outTheDoorPrice']));
			$toCacheCalculator['total'] = $this->payment_calculator['outTheDoorPrice'];
		}else {
			
			 $this->payment_calculator['paymentWithTaxesVal']['monthlyPayment'] =  number_format(str_replace(',','',$this->payment_calculator['paymentWithTaxesVal']['monthlyPayment']));
		$toCacheCalculator['monthly'] = $this->payment_calculator['paymentWithTaxesVal']['monthlyPayment'];
			$this->payment_calculator['paymentWithTaxesVal']['amountFinanced'] = number_format(str_replace(',','',$this->payment_calculator['paymentWithTaxesVal']['amountFinanced']));
			$toCacheCalculator['total'] = $this->payment_calculator['paymentWithTaxesVal']['amountFinanced'];
		}
		
		if(empty($term)){
			if($transactionType == 'lease'){
				if(array_key_exists('default_lease',$this->payment_calculator)){
					$term = $this->payment_calculator['default_lease'];
				}
			}else if($transactionType == 'finance'){
				if(array_key_exists('default_finance',$this->payment_calculator)){
					$term = $this->payment_calculator['default_finance'];
				}
			}
		}

		$toCacheCalculator['type'] = ucfirst($transactionType);
		$toCacheCalculator['comments'] = 'Terms:'.$term;
		$toCacheCalculator['xmlvariables'] = $this->payment_calculator;
		
		 
		\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'calculator',json_encode($toCacheCalculator));
		/*************** LEADS END **************/
	 
			
         return $this->payment_calculator;
	}
	
	/**
     Rules
	 1. ‘Retail Consumer Cash is applicable only to Cash and not to Finance.’ 
	 2.	Sales code Specific changes. For now if you see: dealer, inventory, engine , transmission , pakage as keywords, 
		we should drop that incentive.
	 3. For any incentive which has ‘CCAP’ in the verbiage, create a 3rd question Offer(initial) popup
				a.	Lease :- ‘Use Chrysler Capital to lease the vehicle.
				b.	Finance:- ‘Use Chrysler Capital to finance your vehicle.
	 4. Move the Chrysler Capital Incentives to ‘ Additional Incentives’ Section. 
	 *
     * @return void
     */
	function paymentCalcRules($value,$methos){
		/* $this->request_vin 				= $vin;
		$this->request_transactionType 	= $transactionType; 
		$this->request_zipcode 			= $zipcode; 
		$this->request_down 			= $down; 
		$this->request_tradein 			= $tradein; 
		$this->request_term 			= $term; 
		$this->request_rebateIDs2 		= $rebateIDs2; 
		$this->request_mileage 			= $mileage; 
		$this->request_methods 			= $methods; 
		$this->request_dealer_disc 		= $dealer_disc; 
		$this->request_f_dealer_disc 	= $f_dealer_disc;
		$this->transactionDiscountType 	= $transactionType; */
		
		
		$paymentCalcRules = 'allow';
		
					
			// Rule-2 Validate
			 if (!$this->paymentCalcRules_02_SalesCodeSpecificChanges($value)) {
				 $paymentCalcRules = 'dis-allow';
			 } 
			 
								 
			// Rule-3 Validate								 
			if ($this->paymentCalcRules_03_IncentiveWhichHasCCAP($value, $this->request_transactionType)) {
				$paymentCalcRules = 'dis-allow';
			} 
			
									
									
		return $paymentCalcRules;
	}
	
	
	function paymentCalcRules_01_labelReplace($value){
		//\Log::info('paymentCalcRules_01_labelReplace');
		//\Log::info($value);
		return trim(str_replace("FCA US","", $value));
	//	return $value;
	}
		
	/*
	* return boolean
	* Verify blocked keywords use caes
	* Description			
			// TRUE THEN ALLOW
			// FALSE THEN DIS-ALLOW

			Lease / Finance / Cash
			inventory, engine, transmission, package, idl
			
	*/
	function paymentCalcRules_02_SalesCodeSpecificChanges($value){
				// FALSE THEN KEYWORD NOT FOUND
				// TRUE  THEN KEYWORD FIND
		if ($this->incentivesFindWords(strtolower($value), 'dealer') || $this->incentivesFindWords(strtolower($value), 'diesel') || $this->incentivesFindWords(strtolower($value), 'inventory') || $this->incentivesFindWords(strtolower($value), 'engine') || $this->incentivesFindWords(strtolower($value), 'transmission') || $this->incentivesFindWords(strtolower($value), 'package') || $this->incentivesFindWords(strtolower($value), 'idl')) { 
			return false;
		}

		// if ($this->incentivesFindWords(strtolower($value), 'dealer') || $this->incentivesFindWords(strtolower($value), 'inventory')  || $this->incentivesFindWords(strtolower($value), 'idl')) { 
		// 	return false;
		// }
		return true;
		
	}


	/*
	* return boolean
	* Verify "ccap" and "retail consumer cash" use caes
	* Description			
			// FALSE THEN ALLOW
			// TRUE THEN DIS-ALLOW

			Lease / Finance
			Retail Consumer Cash -> Blocked
			CCAP -> Blocked and moved to Explore option

			Cash
			Retail Consumer Cash -> Blocked
			CCAP -> Blocked and moved to Explore option
			
	*/
	function paymentCalcRules_03_IncentiveWhichHasCCAP($value, $method){
		// ALLOWED LEASE / FINANCE ONLY
		if($method != 'cash'){
			// FALSE THEN KEYWORD NOT FOUND
			// TRUE  THEN KEYWORD FIND
			if ($this->incentivesFindWords(strtolower($value), 'retail consumer cash')) { 
				return true;
			}
			if ($this->incentivesFindWords(strtolower($value), 'ccap')) {  
				return true;
			}
			return false;
		} 
		if($method == 'cash'){
			// FALSE THEN KEYWORD NOT FOUND
			// TRUE  THEN KEYWORD FIND
			if ($this->incentivesFindWords(strtolower($value), 'retail consumer cash')) { 
				return false;
			}
			if ($this->incentivesFindWords(strtolower($value), 'ccap')) { 
				return true;
			}
			return false;
		} 
		
	}

	// function paymentCalcRules_01_RetailConsumerCashOnlyFinance($value, $method){
	// 			// ALLOWED CASH ONLY
	// 	if($method != 'cash'){
	// 			// FALSE THEN KEYWORD NOT FOUND
	// 			// TRUE  THEN KEYWORD FIND
				
	// 		if ($this->incentivesFindWords(strtolower($value), 'retail consumer cash')) { 
	// 			return false;
	// 		}
	// 	} 
	// 	return true;
	// }
	
	
	
	function CalculatorIncentives($vin, $transactionType){
		#Declared the Cache Name
        $cacheName = "calc:".$vin.':'.$transactionType;
         
        #Verified Cache Whether Cache already set or not
        if(!\Databucket::isCacheExists($cacheName)){  
            #Rebates Builder 
                $postArray                     = $this->ArrayBuilderRebates($vin, $transactionType);
                $XRouteoneAPIResource          = config('ore.calc.rebates'); 
				 
            #Rebates Routeone API                    
                if($this->dummy_activate){
                    $dummy  = $this->dummy_rebates();
                    $output['status'] = 'success';
                    $output['result'] = json_decode($dummy, true);
                }else{
                    $output                     = $this->RouteoneCalculator($postArray, $XRouteoneAPIResource);
                }
                
			//	\Log::info("=== ALL INCENTIVES =====");
			//	\Log::info($output);
            if($output['status'] == 'success'){
                $response                     = $output['result']; 
            }else{
                throw new \Exception('Routeone Response Validation Error. check the log');
                exit;
            }
			if(count($response) == 0){
			//	$this->payment_calculator['restrict'] = true;
				return true;
			} 
                
            # Incentive Calculation
            $this->IncentiveAmount($response); 
            $this->ExploreDetails($response);  
			
            if(count($this->man_incentives_id) > 0) 
				$this->man_incentives_id          =     $this->man_incentives_id;
            if(count($this->man_incentives_name) > 0) 
				$this->man_incentives_name      =     $this->man_incentives_name; 
            #Assigned Into Redis Cache System    
            \Databucket::isCacheHMSet($cacheName, 'response',json_encode($response));  
            \Databucket::isCacheHMSet($cacheName, 'vin',$vin);  
            \Databucket::isCacheHMSet($cacheName, 'transactionType',$transactionType); 
            \Databucket::isCacheHMSet($cacheName, 'incentiveIds',json_encode($this->man_incentives_id)); 
			\Databucket::isCacheHMSet($cacheName, 'incentiveNames',json_encode($this->man_incentives_name));
			\Databucket::isCacheHMSet($cacheName, 'man_incentives_sin_name',json_encode($this->man_incentives_sin_name));
			\Databucket::isCacheHMSet($cacheName, 'man_incentives_disclaimer_name',json_encode($this->man_incentives_disclaimer_name));
			\Databucket::isCacheHMSet($cacheName, 'man_incentives_exp_name',json_encode($this->man_incentives_exp_name));			
            \Databucket::isCacheHMSet($cacheName, 'incentiveAmount',$this->incentive_amount); 
         }else{        
             $response = \Databucket::isCacheHMGet($cacheName, 'response'); 
             $this->ExploreDetails(json_decode($response[0], true)); 
			 
			list($e_1) = \Databucket::isCacheHMGet($cacheName, 'incentiveIds');
			list($e_2) = \Databucket::isCacheHMGet($cacheName, 'incentiveNames');
			list($e_3) = \Databucket::isCacheHMGet($cacheName, 'man_incentives_sin_name');
			list($e_4) = \Databucket::isCacheHMGet($cacheName, 'man_incentives_disclaimer_name');
			list($e_5) = \Databucket::isCacheHMGet($cacheName, 'man_incentives_exp_name');
			 
			$encode_man_incentives_id         	= json_decode($e_1);
			$encode_man_incentives_name 		= json_decode($e_2);
			$encode_man_incentives_sin_name 	= json_decode($e_3);
			$encode_man_incentives_disclaimer_name 	= json_decode($e_4);
			$encode_man_incentives_exp_name 	= json_decode($e_5);
            $encode_incentive_amount          	= \Databucket::isCacheHMGet($cacheName, 'incentiveAmount');
			 
            $this->man_incentives_id         = $encode_man_incentives_id;
			$this->man_incentives_name       = $encode_man_incentives_name;
			$this->man_incentives_sin_name   = $encode_man_incentives_sin_name;
			$this->man_incentives_disclaimer_name   = $encode_man_incentives_disclaimer_name;
			$this->man_incentives_exp_name   = $encode_man_incentives_exp_name;
			$this->incentive_amount          = $encode_incentive_amount[0];  
        }     
        #Incentives Array
		 
			
        $this->payment_calculator['incentiveIds']     	=     ($this->man_incentives_id);
		$this->payment_calculator['incentiveNames'] 	=     $this->man_incentives_name;
		$this->payment_calculator['man_incentives_sin_name'] 	=     $this->man_incentives_sin_name;
        $this->payment_calculator['incentiveAmount']	=     (int)$this->incentive_amount; 
		$this->payment_calculator['incentive_disclaimer']        	=    $this->man_incentives_disclaimer_name;
		$this->payment_calculator['incentive_expdate']        	=    $this->man_incentives_exp_name;
 
        $this->payment_calculator['explores']        	=     $this->explore_checkes; 
	 
	}
	
	/**
     * Taxes Response.
     *
     * @return void
     */
	function TaxesDetails($response_arr){
			try{
				 
			}catch(Exception $e) {
						\Log::debug('TaxesDetails Debug: '.$e->getMessage());
						return true;
			}		
	}
	
	/**
     * Explore Details from Rebates Response.
     *
     * @return void
     */
	function ExploreDetails($response_arr){ 
			try{
				$explore_array_keys = [];
				foreach($this->salesCodesArray as $saleskey => $salescodes)	{
					$salescodes = strtolower($salescodes);
					${$salescodes} = [];
				}
				
				 
				 foreach($response_arr as $key => $val){

					   if($val['groupAffiliation'] == $this->grp_aff && $val['previousOwnership'] == $this->pre_owner){
						   if($this->date_validation){
								if($val['expirationDate'] >= $this->today) $date_state = true; else $date_state=false;
							}else $date_state = true;
							
							if($date_state)
							{ 
								//\Log::info('PART - 02');
								$val['name'] = $this->paymentCalcRules_01_labelReplace($val['name']);

								foreach($this->salesCodesArray as $saleskey => $salescodes)	{
										$salescodes = strtolower($salescodes);

										if ($this->incentivesFindWords(strtolower($val['name']), $salescodes)) {
												array_push(${$salescodes},$val); 	
											}
								}
								
							}
						   
						   
						   
						   
					   }else{


						   if($this->date_validation){
								if($val['expirationDate'] >= $this->today) $date_state = true; else $date_state=false;
							}else $date_state = true;
						


							if($date_state)
							{
								if(in_array($val['groupAffiliation'], $this->explore_inarray) || in_array($val['previousOwnership'], $this->explore_inarray)){
									
									if($val['groupAffiliation']==$this->grp_aff || $val['previousOwnership'] == $this->pre_owner){
										$variable = str_replace(" ","-",$val['groupAffiliation']);
										 $variable = str_replace("/","-",$val['groupAffiliation']);
										 $variable = strtolower($variable);
										 if($val['previousOwnership'] == 'Lease Loyalty') $variable = 'lease-loyalty';
										 if($val['previousOwnership'] == 'Lease Conquest') $variable = 'lease-conquest';
										 if($val['previousOwnership'] == 'Loyalty') $variable = 'lease-loyalty';
										 if($val['previousOwnership'] == 'Conquest') $variable = 'lease-conquest';
										  
										
										 array_push($explore_array_keys,$variable); 
										 
										 if(!isset(${$variable.'_id'})) ${$variable.'_id'} = array();
										 if(!isset(${$variable.'_name'})) ${$variable.'_name'} = array();
										 if(!isset(${$variable.'_amount'})) ${$variable.'_amount'} = 0; 
										  if(!isset(${$variable.'_inv_amt'})) ${$variable.'_inv_amt'} = array();
										  if(!isset(${$variable.'_exp_date'})) ${$variable.'_exp_date'} = array();
										   if(!isset(${$variable.'_disclaimer'})) ${$variable.'_disclaimer'} = array();
										 //\Log::info('PART - 03');
										 $val['name'] = $this->paymentCalcRules_01_labelReplace($val['name']);
										 array_push(${$variable.'_id'},$val['incentiveId']);
										 array_push(${$variable.'_name'},$val['name']); 
										 array_push(${$variable.'_disclaimer'},$this->nonccap_disclaimer.$val['expirationDate']); 
										 array_push(${$variable.'_inv_amt'},$val['amount']); 
										array_push(${$variable.'_exp_date'},$val['expirationDate']); 
										${$variable.'_amount'} += $val['amount'];	
									}									 
								} // In array validte close	
							 } // Date Validate Close
							 
					   }  
					}
					
				foreach($this->salesCodesArray as $saleskey => $salescodes)	{
					$salescodes = strtolower($salescodes);
					if(count(${$salescodes}) > 0){

							if($salescodes == 'ccap'){
									$variable = "chrysler-capital-incentives";
									$this->payment_calculator['isCCAPAvailale']  =  true;
									$thisdisc = $this->ccap_disclaimer;
							}else{
									$variable = $salescodes;
									$this->payment_calculator['is'.$salescodes.'Availale']  =  true;
									$thisdisc = $this->salescode_disclaimer;
							}
							
							array_push($explore_array_keys,$variable); 	 

							foreach(${$salescodes} as $key => $val){

								if(!isset(${$variable.'_id'})) ${$variable.'_id'} = array();
								if(!isset(${$variable.'_name'})) ${$variable.'_name'} = array();
								if(!isset(${$variable.'_amount'})) ${$variable.'_amount'} = 0; 
								 if(!isset(${$variable.'_inv_amt'})) ${$variable.'_inv_amt'} = array();
								 if(!isset(${$variable.'_exp_date'})) ${$variable.'_exp_date'} = array();
								 if(!isset(${$variable.'_disclaimer'})) ${$variable.'_disclaimer'} = array();
							
								 $val['name'] = $this->paymentCalcRules_01_labelReplace($val['name']);		
								array_push(${$variable.'_id'},$val['incentiveId']);
								array_push(${$variable.'_name'},$val['name']); 
								array_push(${$variable.'_exp_date'},$val['expirationDate']); 
								array_push(${$variable.'_disclaimer'},$thisdisc.$val['expirationDate']); 
								array_push(${$variable.'_inv_amt'},$val['amount']); 								
								${$variable.'_amount'} += $val['amount'];
							}	
					}
				}	
					
					
					$explore_array_keys = array_unique($explore_array_keys); 
					$validate_name = [];
					foreach($explore_array_keys  as $key => $val){ 

								if(in_array(${$val.'_name'}, $validate_name)){
									
								}else{
										array_push($validate_name, ${$val.'_name'});
										$val = $this->paymentCalcRules_01_labelReplace($val);	
										$this->explore_checkes[$val]['feature'] = ucwords($val);
										$this->explore_checkes[$val]['name'] = ${$val.'_name'};
										$this->explore_checkes[$val]['exp_date'] = ${$val.'_exp_date'};
										$this->explore_checkes[$val]['disclaimer'] = ${$val.'_disclaimer'};
										$this->explore_checkes[$val]['inv_amount'] = ${$val.'_inv_amt'};
										$this->explore_checkes[$val]['amount'] = ${$val.'_amount'};
										$this->explore_checkes[$val]['ids'] = implode( ", ", ${$val.'_id'} );
								}
								
							
					}

					
			}catch(Exception $e) {
						\Log::debug('ExploreDetails Debug: '.$e->getMessage());
						return true;
			}	 
		 
		return true;		
	}
	
	 
	/**
     * Customized all man incentives
     * Table-name: 
	 *
     * @return void
     */

	function incentivesBonusCash($vin,$make,$dealer_code){
		try{
			$this->payment_calculator['incentivesBonusCashList'] = array();
			$cacheName = 'calc:incentives:bonuscash:'.$make.':'.$dealer_code;
			
				if(\Databucket::hexists($cacheName, 'data')){
						$calc_allocation = \Databucket::isCacheHMGet($cacheName, 'data'); 
							if(count($calc_allocation) > 0){
								 $list_incentives_bonuscash = json_decode($calc_allocation[0], true);
							}  
						
					}else{
							$list_incentives_json = \Databucket::sqlIncentivesBonusCash($make,$dealer_code); 
							$list_incentives_bonuscash = $list_incentives_json->toArray(); 
							
							\Databucket::isCacheHMSet($cacheName, 'data',$list_incentives_json); 
					} 
					$incentivesBonusCashList = array();
					$incentivesBonusCashList = array();
					 foreach($list_incentives_bonuscash as $key => $val){
					 	$value = (array) $val;
					 	$isApplicableTranscationType = false;
					 	$var_key = 'is_lease';
					 	if($this->transactionDiscountType == 'finance'){
					 		$var_key = 'is_finance';
					 	}else if($this->transactionDiscountType == 'cash'){
					 		$var_key = 'is_cash';
					 	}
					 	if(array_key_exists($var_key, $value)){
					 		$isApplicableTranscationType = (!empty($value[$var_key]) && $value[$var_key]=='TRUE') ?  true : false;					 		
					 	}
						if($value['vin'] == $vin  && $isApplicableTranscationType){
							$bonuscashArray = array();
							if(!array_key_exists($value['incentive_label'], $incentivesBonusCashList)){
								$bonuscashArray['name'] = $value['incentive_label'];
								$bonuscashArray['disclaimer'] = '';
								if(array_key_exists($value['program_id'], $this->incentiveBonus_disclaimer)){
									$bonuscashArray['disclaimer'] = $this->incentiveBonus_disclaimer[$value['program_id']];
								}
								$bonuscashArray['program_id'] = $value['program_id'];
								$bonuscashArray['discount'] = $value['discount_amount'];
								$incentivesBonusCashList[$value['incentive_label']] = $bonuscashArray;
								$incentivesBonusCashList[$value['incentive_label']]['discount'] = $value['discount_amount'];								
							}else{
								$incentivesBonusCashList[$value['incentive_label']]['discount'] = ($incentivesBonusCashList[$value['incentive_label']]['discount'] + $value['discount_amount']);
							}
							$this->payment_calculator['incentivesBonusCash_available'] 	= true; 
							$this->payment_calculator['incentivesBonusCash_amount'] 	+= $value['discount_amount'];
						}
					 }
					 $this->payment_calculator['incentivesBonusCashList'] = $incentivesBonusCashList;

			}catch(\Exception $e){
				  \Log::error('Calcualtor: incentivesBonusCash');
				  \Log::error('Message: ' .$e->getMessage());
				  \Log::error('Line: ' .$e->getLine());
			}
	}	
	/**
     * Verify Payment Methods
     *
     * @return void
     */
	function verifyPaymentMethodAllocation($VinNumber, $transactionType){
	 
		/* Swithced Area */
		try{
			//$this->payment_calculator['finance_type'] = $this->defaultFinanceType;
			
			if(\Databucket::isCacheExists($VinNumber) ){ 
				 $vin_info_make = \Databucket::isCacheHMGet($VinNumber, 'make');
				 $vin_info_model = \Databucket::isCacheHMGet($VinNumber, 'model');
				 $vin_info_trim_desc = \Databucket::isCacheHMGet($VinNumber, 'trim_desc'); 
				 $list_calc_array = [];
				  
					if(\Databucket::hexists('calc:allocation', 'data')){
						$calc_allocation = \Databucket::isCacheHMGet('calc:allocation', 'data'); 
							if(count($calc_allocation) > 0){
								 $list_calc_array = json_decode($calc_allocation[0], true);
							}  
						
					}else{
							$list_calc_json = \Databucket::sqlPaymentMethodAllocation();
							\Databucket::isCacheHMSet('calc:allocation', 'data',$list_calc_json); 
							 $list_calc_array = $list_calc_json->toArray(); 
					} 
				  
				 
				  
				  if($list_calc_array > 0){
					  
					  foreach($list_calc_array as $key => $val){ 
							  if($val['dkey'] == 'make' && ($val['financetype'] == $transactionType || $val['financetype'] == 'all')){  
								  if(strtolower($vin_info_make[0]) == strtolower($val['dvalue'])){
									  $this->payment_calculator['finance_type'] = $val['financemode'];
								  }
							  }
					   }
					   
					   foreach($list_calc_array as $key => $val){						  
							  if($val['dkey'] == 'model' && ($val['financetype'] == $transactionType || $val['financetype'] == 'all')){ 
								  if(strtolower($vin_info_model[0]) == strtolower($val['dvalue'])){
									  $this->payment_calculator['finance_type'] = $val['financemode'];
								  }
							  }
					   }
					   
					   
					  foreach($list_calc_array as $key => $val){ 			  
							  if($val['dkey'] == 'trim_desc' && ($val['financetype'] == $transactionType || $val['financetype'] == 'all')){ 
							  
								  if(strtolower($vin_info_trim_desc[0]) == strtolower($val['dvalue'])){
									  $this->payment_calculator['finance_type'] = $val['financemode'];
								  }
							  }
					   }
					   
					   foreach($list_calc_array as $key => $val){						  
							  if($val['dkey'] == 'vin' && ($val['financetype'] == $transactionType || $val['financetype'] == 'all')){  
								  if($VinNumber == $val['dvalue']){
									  $this->payment_calculator['finance_type'] = $val['financemode'];
								  }
							  }
					   } 
				  }  
			}
		}catch(\Exception $e){
				  \Log::error('Calcualtor: Payment Method Switch Ally or CCAP');
				  \Log::error('Message: ' .$e->getMessage());
		}
	}
	function dealerAutomatedCalculation($VinNumber, $transcation){
		try{
			//$this->payment_calculator['finance_type'] = $this->defaultFinanceType;
			
			if(\Databucket::isCacheExists($VinNumber) ){ 
				 $vin_info['make'] = \Databucket::isCacheHMGet($VinNumber, 'make');
				 $vin_info['model'] = \Databucket::isCacheHMGet($VinNumber, 'model');
				 $vin_info['trim_desc'] = \Databucket::isCacheHMGet($VinNumber, 'trim_desc'); 
				 $vin_info['year'] = \Databucket::isCacheHMGet($VinNumber, 'year'); 
				 $vin_info['dealer_code'] = \Databucket::isCacheHMGet($VinNumber, 'dealer_code');
				if($transcation == 'cash'){
					$financeoption =  3;
				}else if($transcation == 'finance'){
					$financeoption =  2;
				}else{
					$financeoption =  1;
				}
			 
				$result = \Databucket::SqlFiltergroups($vin_info,$financeoption); 
			}
		}catch(\Exception $e){
				  \Log::error('dealerAutomatedCalculation Issues');
				  \Log::error('Message: ' .$e->getMessage());
		}		 
	}
	/**
     * Incentive Details gathered from Rebates Response.
     *
     * @return void
     */
	function dealerDiscountCalculation($VinNumber, $price){
		$discountAmount = 0;
		$list_discount_array = array();
		$list_discount_html = '';
		$list_discount_cmts = '';
		$transcation = $this->transactionDiscountType;

		if(empty($VinNumber)) return true;
		
		# Validate Cache Available or Not.
		if(!\Databucket::isCacheExists($VinNumber)){ 		
			$data = \App\Vehicle::where(['vin' => $VinNumber])->first();			
		}else{
			$data = \Ore::isCacheGetAll($VinNumber); 
		}			  
		$objVehicle = (object)$data;
		
		if(strtolower($objVehicle->vehicle_type) == 'new'){
			$msrp = (int)$objVehicle->msrp;
		}else{
			$msrp = (int)$objVehicle->internetPrice;
		}

		$price = $msrp;
		
		//$list_automated_array =  $this->dealerAutomatedCalculation($VinNumber,$transcation);
		 
		// $list_discount_array = \Databucket::SqlDlrmgdiscount($VinNumber,$transcation);
	//	\Log::info($this->vin_info);
		$list_discount_array = $this->SqlFiltergroups($this->vin_info, $transcation);
	//	 \Log::info("--------------------------------------list_discount_array--------------------------------"); 
	//	\Log::info($list_discount_array);
			if(!empty($list_discount_array)){
				$list_discount_array = $list_discount_array->toArray();
				
				foreach($list_discount_array as $list_discount_array_key => $list_discount_array_v){ 
				//\Log::info($list_discount_array);
				$list_discount_array_value = (array)$list_discount_array_v;
					if($list_discount_array_value['flat_rate'] == null || empty($list_discount_array_value['flat_rate']) || 
						$list_discount_array_value['flat_rate']==0 ){  
								$p_cent = 0;
								$p_cent = round(($list_discount_array_value['percent_offer'] / 100 )*  $price); 
								$discountAmount += 	$p_cent;
								$list_discount_html .= '<h4 class="dealer-discount"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width="20" height="20"><span class="">'.$list_discount_array_value['name_of_discount'].'</span><span><b>'.$list_discount_array_value['percent_offer'].'%</b></span></h4>';
								$list_discount_cmts .= $list_discount_array_value['name_of_discount'] .' - discountPercent: '.$list_discount_array_value['percent_offer'].'% , '; 
							
					}else{   
								$discountAmount += $list_discount_array_value['flat_rate'];
								$list_discount_html .= '<h4 class="dealer-discount"><img alt="incentive-icon" src="/images/incentive-icon-5.png" width="20" height="20"><span class="">'.$list_discount_array_value['name_of_discount'].'</span><span><b>$'.$list_discount_array_value['flat_rate'].'</b></span></h4>';
								$list_discount_cmts .= $list_discount_array_value['name_of_discount'] .' - discountFlatRate: $'.$list_discount_array_value['flat_rate'].','; 
					}
				} 
			}
		$this->payment_calculator['dlrDiscLists']  	= $list_discount_html;
		if(!config('ore.discounts.maxAmount5000Allowed')){
            if(5000 < $discountAmount){
                $discountAmount = 5000;
            }
       }
		$this->payment_calculator['dlrDiscAmount'] 	= $discountAmount; 
		$this->payment_calculator['dlrDiscCmts'] 	= $list_discount_cmts; 
	}
	
	/**
     * Incentive Details gathered from Rebates Response.
     *
     * @return void
     */
	function IncentiveAmount($response_arr){
			try{
					if(!empty($response_arr)){
					foreach($response_arr as $key => $val){
						
						if($this->date_validation){
							if($val['expirationDate'] >= $this->today) $date_state = true; else $date_state=false;
						}else $date_state = true;
						
						if($date_state && $val['groupAffiliation'] == $this->grp_aff && $val['previousOwnership'] == $this->pre_owner){
							
							 if(array_key_exists('name', $val)){ 
								 
							$paymentCalcRules = 'allow';  	
								
								
								$paymentCalcRules = $this->paymentCalcRules($val['name'], $this->request_transactionType);
							
								 
								 // Rules
								 if ($paymentCalcRules=='allow') {

											if(array_key_exists('name', $val))
											{ 
												$val['name'] = $this->paymentCalcRules_01_labelReplace($val['name']);
											}
									if(!in_array($val['name'],$this->man_incentives_name)){

											if(array_key_exists('incentiveId', $val))
											{
												array_push($this->man_incentives_id,$val['incentiveId']);
											}

											if(array_key_exists('name', $val))
											{ 
												$val['name'] = $this->paymentCalcRules_01_labelReplace($val['name']);
												array_push($this->man_incentives_name,$val['name']);
											}
											if(array_key_exists('amount', $val))
											{
												array_push($this->man_incentives_sin_name,$val['amount']); 
												$this->incentive_amount += $val['amount'];	
											}	
											if(array_key_exists('expirationDate', $val))
											{
												array_push($this->man_incentives_exp_name,$val['expirationDate']);  
												array_push($this->man_incentives_disclaimer_name,$this->nonccap_disclaimer.' '.$val['expirationDate']); 
											}

									} // Name Duplicate in incentives


								 } // Rules End Loop
								 
								 
							 } // array_key_exists => name
							
							
						}
					}
				}
			}catch(Exception $e) {
						\Log::debug('Incentive Amount Debug: '.$e->getMessage());
						return true;
			}	
			return true;			
	}
	
	/**
     * Routeone API Called For Payment Calculator.
     *
     * @return \Illuminate\Http\Response
     */
	function RouteoneCalculator($post, $XRouteoneAPIResource){
			# Credentials Declaration
			$fullURLString 				= $this->endpoint.$XRouteoneAPIResource;
			$accessKeyId 				= $this->accessKeyId;			
			$routeoneSecret 			= $this->routeoneSecret;
			$XRouteOneActAsDealership 	= $this->XRouteOneActAsDealership;
			$XRouteDate 				= $this->XRouteDate;
			$XRouteContentType 			= $this->XRouteContentType; 
			$XRouteOneActAsDealership_iname = $this->XRouteOneActAsDealership_name;
			  
			  
			# HAMC debugging	
			try{	
				# Prepared Content  MD5
				$json = json_encode($post); 
			//	\Log::info($json);
				$ContentMD5_body = base64_encode(md5($json, true));
				
				#StringToSign Variable assignment 
				$HTTP_VERB               	= "POST"."\n";
				$ContentMD5              	= strtolower($ContentMD5_body)."\n";
				$ContentType             	= $XRouteContentType."\n";
				$Date                    	= strtolower($XRouteDate)."\n";  
				$CanonicalizedResource  	= $XRouteoneAPIResource."\n";
							
				$CanonicalizedHeaders 		= $XRouteOneActAsDealership_iname.':'.$XRouteOneActAsDealership."\n";
				
				#StringToSign Created
				$stringToSign = $HTTP_VERB.$ContentMD5.$ContentType.$Date.$CanonicalizedHeaders.$CanonicalizedResource;		
				$byteArrayStringToSign = utf8_encode($stringToSign);
				 
				#Signature Created	
				$signature_hash 			= hash_hmac('sha256',$byteArrayStringToSign,$routeoneSecret, true);  
				$signature 					= base64_encode($signature_hash);      
				
				# Authorization Prepared  
				$Authorization = "RouteOne $accessKeyId:$signature"; 
			}catch(Exception $e) {
						return ['status'=>'error','result'=>'HAMC Debug: '.$e->getMessage()]; 
			}
			
			# Curl debugging	
			try{
				# cURL			 
				$ch = curl_init($fullURLString);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json); 
				curl_setopt($ch, CURLOPT_HTTPHEADER, array( 				
					'accept: '.$XRouteContentType,
					'content-type: '.$XRouteContentType,
					"date: ".$XRouteDate,
					$XRouteOneActAsDealership_iname.": ".$XRouteOneActAsDealership,  
					"content-md5: ".$ContentMD5_body,
					"authorization: ".$Authorization,
				  ));    
			
				$responseBody = curl_exec($ch); 
				
				# Check for errors and display the error message
				if($errno = curl_errno($ch)) {
						$error_message = curl_strerror($errno);
						return ['status'=>'error','result'=>'Request Error #'.$errno.':' .$error_message];  
				}
				
				$response_arr = json_decode($responseBody, true);					
				curl_close($ch); 
				$ostatus = ($response_arr['routeOneErrorCode']) ?? "success";
				//\Log::info(' ---- RESULTS INNER ----');
				//\Log::info($response_arr);
		   
		   
				if($ostatus != "success"){
					\Log::debug("RouteOne Error: ".$response_arr['developerMessage']);
					return ['status'=>'error','result'=>'Request Error #'.$response_arr['developerMessage']]; 
					exit;
				}else{
					return ['status'=>'success','result'=>$response_arr];
				}
			}catch(Exception $e) {
				curl_close($ch);
				return ['status'=>'error','result'=>'Curl Debug: '.$e->getMessage()]; 
			}
		}

	public function downCalculation($down){

					$this->payment_calculator['original_downpayment'] = $down; 
					$this->payment_calculator['additional_discount'] = 0;

					if(config('ore.calc.dealer_discount')){
							$this->payment_calculator['dlrDiscAmount'] = 0; 
							$this->dealerDiscountCalculation($this->request_vin, $down);
							$down += $this->payment_calculator['dlrDiscAmount'];
					}else{
							$this->payment_calculator['dlrDiscLists']  = '';
							$this->payment_calculator['dlrDiscAmount'] = 0; 
					}



					if(config('ore.calc.incentivesBonusCash')){  
						$down += $this->payment_calculator['incentivesBonusCash_amount'];			
					}

					if (config('ore.calc.additioanal_offer')) {
						if ($this->request_transactionType == 'lease') {
							$down += $this->request_lease_additional_disc;
							$this->payment_calculator['additional_discount'] = $this->request_lease_additional_disc;
						}
						if ($this->request_transactionType == 'finance') {
							$down += $this->request_finance_additional_disc;
							$this->payment_calculator['additional_discount'] = $this->request_finance_additional_disc;
						}
						if ($this->request_transactionType == 'cash') {
							\Log::info($this->request_cash_additional_disc);
							$down += $this->request_cash_additional_disc;
							$this->payment_calculator['additional_discount'] = $this->request_cash_additional_disc;
						}
					}

					return $down;
	}	
	
	/**
     * API Json : Vehicle Details Build.
     *
     * @return \Illuminate\Http\Response
     */
    public function JsonVehicleBuild($vin)
    {
		if(empty($vin)) return true;
		
		# Validate Cache Available or Not.
		if(!\Databucket::isCacheExists($vin)){ 		
			$data = \App\Vehicle::where(['vin' => $vin])->first();			
		}else{
			$data = \Ore::isCacheGetAll($vin); 
		}			  
		$objVehicle = (object)$data;
		
		if(strtolower($objVehicle->vehicle_type) == 'new'){
			$msrp = (int)$objVehicle->msrp;
		}else{
			$msrp = (int)$objVehicle->internetPrice;
		}
		
		$this->payment_calculator['msrp_results'] = $msrp; 
		$sellingPrice = $msrp;
 
	if($this->transactionDiscountType=='cash'){
		 
		$this->payment_calculator['additional_discount'] = 0;
		
		if(config('ore.calc.dealer_discount')){
			    $this->payment_calculator['dlrDiscAmount'] = 0; 
				$this->dealerDiscountCalculation($vin, $sellingPrice);
				$sellingPrice -= $this->payment_calculator['dlrDiscAmount'];
		}else{
				$this->payment_calculator['dlrDiscLists']  = '';
				$this->payment_calculator['dlrDiscAmount'] = 0; 
		} 

		if(config('ore.calc.incentivesBonusCash')){ 
			$sellingPrice -= $this->payment_calculator['incentivesBonusCash_amount'];
		}

		if (config('ore.calc.additioanal_offer')) {
				$sellingPrice -= $this->request_cash_additional_disc;
				$this->payment_calculator['additional_discount'] = $this->request_cash_additional_disc;
		}
	} 


		$array_build = array();
		# JsonVehicleBuild debugging	
		try{
			# Vehicle Array Build.        
			$array_build = [
				'vin' => $objVehicle->vin,
				'year' => (int)$objVehicle->year,
				'make' => $objVehicle->make,
				'model' => $objVehicle->model, 
				"salesClass" => $objVehicle->vehicle_type,
				"sellingPrice" => $sellingPrice,
				"msrp" => $msrp 
			];
		}catch(Exception $e) { 
			\Log::debug('JsonVehicleBuild Debug: '.$e->getMessage()); 
		}
		
		return $array_build;		
    }
	
	/**
     * API Json : Customer Details Build.
     *
     * @return \Illuminate\Http\Response
     */
	public function JsonCustomerBuild($zipcode)
    {
		if(empty($zipcode)) return true;
		
		$array_build = array();
		# JsonCustomerBuild debugging	
		try{
			$array_build['address'] = [ 
				'zipCode' => $zipcode
			];
		}catch(Exception $e) { 
			\Log::debug('JsonCustomerBuild Debug: '.$e->getMessage()); 
		}		
		return $array_build;
	}
	
	/**
     * API Json : tradeInVehicle Build.
     *
     * @return \Illuminate\Http\Response
     */
	public function JsontradeInVehicleBuild()
    { 
		
		$array_build = array();
		# tradeInVehicle debugging	
		try{
			# tradeInVehicle Array Build.        
			$array_build = [ 'tradeAllowance' => 0, 'tradePayoff' => 0,];
		}catch(Exception $e) { 
			\Log::debug('tradeInVehicle Debug: '.$e->getMessage()); 
		}
		
		return $array_build;
	}
	function test_odd($var)
	{
	return trim(str_replace('"','',$var), '"');
	}
	/**
     * API Json : All RebateIDs Build such as incentive id, explore features ids.
     *
     * @return \Illuminate\Http\Response
     */
	public function JsonRebateBuild($AllIncentiveIds)
    {	
		$AllIncentiveIds = array_filter($AllIncentiveIds);
		
	
		if(empty($AllIncentiveIds)) return [];
		$AllIncentiveIdsReset = array();
		foreach($AllIncentiveIds as $key => $val){
			$AllIncentiveIdsReset[] = $this->test_odd($val);
		}
		
		$array_build = array();
		# JsonRebateBuild debugging	
		try{
			# RebateIDs Array Build.        
			$array_build = $AllIncentiveIdsReset;
		}catch(Exception $e) { 
			\Log::debug('JsonRebateBuild Debug: '.$e->getMessage()); 
		}
		
		return $array_build;
	}
	
	/**
     * API Json : Terms Build.
     *
     * @return \Illuminate\Http\Response
     */
	public function JsonTermsBuild($term)
    {
		//if(empty($term)) return true;
		
		$array_build = array();
		# JsonTermsBuild debugging	
		try{
			# Term Array Build.        
			if(empty($term)) $array_build = [ 0 => '24', 1=>'27', 2=>'39', 3=>'36',  4=>'48', 5=>'60', 6=>'72', 7=>'84'];
			else $array_build = [ 0 => $term];
		}catch(Exception $e) { 
			\Log::debug('JsonTermsBuild Debug: '.$e->getMessage()); 
		}
		
		return $array_build;
	}
	
	
	
	/**
     * Array Builder : Rebates.
     *
     * @return \Illuminate\Http\Response
     */
	 public function ArrayBuilderRebates($vin, $transactionType)
    {
		$rebates_array = array();
		array_push($rebates_array,['vehicle'=>$this->JsonVehicleBuild($vin),'transactionType' => $transactionType]);		 
		return array_shift($rebates_array); 
	}
	
	/**
     * Array Builder : Lease.
     *
     * @return \Illuminate\Http\Response
     */
	public function ArrayBuilderLease($vin, $transactionType, $zipcode, $cashDown, $tradeInValue, $term, $AllIncentiveIds, $mileage){
		$array = array();
		array_push($array, [
							'tradeInValue'		=>	($tradeInValue == (int) $tradeInValue) ? (int) $tradeInValue : (float) $tradeInValue, 
							'vehicle'			=>	$this->JsonVehicleBuild($vin),
							'customer'			=>	$this->JsonCustomerBuild($zipcode),
							//'tradeInVehicle' 	=>  $this->JsontradeInVehicleBuild(),
							'markupIndicator' 	=> 	true,
							'cashDown' 			=>  ($cashDown == (int) $cashDown) ? (int) $cashDown : (float) $cashDown, 
							'financeSource' 	=>  $this->financeSource,
							'tier' 				=>  $this->lease_tier,
							'rebateIds'			=>  $this->JsonRebateBuild($AllIncentiveIds),
							//'rebateIds'			=>  [],
							'terms'				=>  $this->JsonTermsBuild($term),
							'milesPerYear' 		=>  $mileage,
							'cashDownAppliedToInceptionFees'	=> false,
							]);		 
		return array_shift($array); 
	}
	
	/**
     * Array Builder : Finance.
     *
     * @return \Illuminate\Http\Response
     */
	public function ArrayBuilderFinance($vin, $transactionType, $zipcode, $cashDown, $tradeInValue, $term, $AllIncentiveIds){
		$array = array();
		array_push($array, [
							'tradeInValue'		=>	($tradeInValue == (int) $tradeInValue) ? (int) $tradeInValue : (float) $tradeInValue, 
							'vehicle'			=>	$this->JsonVehicleBuild($vin),
							'customer'			=>	$this->JsonCustomerBuild($zipcode),
							//'tradeInVehicle' 	=>  $this->JsontradeInVehicleBuild(),
							'markupIndicator' 	=> 	false,
							'cashDown' 			=>  ($cashDown == (int) $cashDown) ? (int) $cashDown : (float) $cashDown, 
							'financeSource' 	=>  $this->financeSource,
							'tier' 				=>  $this->finance_tier,
							'rebateIds'			=>  $this->JsonRebateBuild($AllIncentiveIds),
							//'rebateIds'			=>  [],
							'terms'				=>  $this->JsonTermsBuild($term) 
							]);		 
		return array_shift($array); 
	}
	
	/**
     * Array Builder : Cash.
     *
     * @return \Illuminate\Http\Response
     */
	public function ArrayBuilderCash($vin,$zipcode, $tradeInValue,$AllIncentiveIds){
		$array = array();
		array_push($array, [
							'tradeInValue'		=>	($tradeInValue == (int) $tradeInValue) ? (int) $tradeInValue : (float) $tradeInValue, 
							'vehicle'			=>	$this->JsonVehicleBuild($vin),
							'customer'			=>	$this->JsonCustomerBuild($zipcode),
							'rebateIds'			=>  $this->JsonRebateBuild($AllIncentiveIds)
							//'rebateIds'			=>  []
							]);		 
		return array_shift($array); 
	} 
	
	public function SqlFiltergroups($vin_info, $financeoption){
	/*public function SqlFiltergroups($DealerCode,$financeoption,$vin,$MakeCode,$ModelYear = NULL,$Model= NULL,$Trim= NULL){

		$vin_info = array(
			'dealer_code' => $DealerCode,
			'make' => $MakeCode,
			'year' => $ModelYear,
			'model' => $Model,
			'trim_desc' => $Trim,
			'vin' => $vin
		);*/
		if($financeoption == 'cash'){
			$financeoption =  3;
		}else if($financeoption == 'finance'){
			$financeoption =  2;
		}else{
			$financeoption =  1;
		}
      // \Log::info('Lfinanceoption: '.$financeoption);
        $level4 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'],'trim' => $vin_info['trim_desc'] ]);
                                            
        $count_level4 = $level4->count();
        //\Log::info('L4: '.$count_level4);
        if($count_level4 == 0){
       
                $level3 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'], 'trim' => NULL  ]);
                $count_level3 = $level3->count();
          //      \Log::info('L3: '.$count_level3);
                if($count_level3 == 0){
               
                $level2 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'],'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => NULL,'trim' => NULL]);
               
                    $count_level2 = $level2->count();
            //        \Log::info('L2: '.$count_level2);
                    if($count_level2 == 0){
                           
                            $level1 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'],'model' => NULL,'trim' => NULL,'model_year' => NULL]);
                            $count_level1 = $level1->count();
                           
                            if($count_level1 == 0){ $output = []; } else {$output = $level1->get(); }
                           
                    }else $output = $level2->get();
                   
                } else $output = $level3->get();
        }else $output = $level4->get();
       
       $vin = $vin_info['vin'];
       $dealer_code = $vin_info['dealer_code'];
       if(count($output) == 0){
       		$result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin); 
       	//	\Log::info('No rule Exists individualdiscount ------------------------------------------');
		//	\Log::info($result);
            return $result;
        }
        ####Include Vins
        if(!$output->isEmpty()){
            $filter_group = $output->toArray();
         //   \Log::info('filterArray ------------------------------------------');
          //  \Log::info($filter_group);
            $includevins = $filter_group[0]['includevins'];
            $excludevins = $filter_group[0]['excludevins'];
            $vinFoundFlag = $excludeFound = false;
            if(!empty($includevins)){
                 $vinFoundFlag = $this->checkValueExistsinArray($vin,$includevins);
                 if($vinFoundFlag){
                 	$result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
           //      	\Log::info('includevins ------------------------------------------');
           // 		\Log::info($result);
	            	if(0 < count($result)){
	         //   		\Log::info('not empty includevins');
	            		return $result;            	   				
	    			}                       
                 }
            }
            if(!empty($excludevins)){
                if(!$vinFoundFlag){
                    $vinFoundFlag = $this->checkValueExistsinArray($vin,$excludevins);
                    if($vinFoundFlag){
               //     	\Log::info('excludevins ------------------------------------------');
                    	$result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
            	//		\Log::info($result);
		            	if(0 < count($result)){
		            		//\Log::info('not empty excludevins');
		            		return $result;            	   				
		    			}   
                    }                    
                }
            }
            if(!empty($includevins) || !empty($excludevins)){
	            if(!$vinFoundFlag){
	            //	\Log::info('individualdiscount ------------------------------------------');
	            	$individual_result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin);
	            //	\Log::info($individual_result);
	            	if(0 < count($individual_result)){
	            		return $individual_result;
	            	}
	            //	\Log::info('filtergroupdiscounts ------------------------------------------');
	            	$result = $this->sqlFilterRulediscount($dealer_code,$filter_group[0]['id'],$financeoption);            	
	    		//	\Log::info($result);
	            	if(0 < count($result)){
	            		\Log::info('not empty filtergroupdiscounts');
	            		return $result;            	   				
	    			}            	
	            }
	        }
        }
        $result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin);  
       // \Log::info('individualdiscount ------------------------------------------');
		//\Log::info($result);
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
	public function dummy_rebates(){
		return $output = '[]';
	}
	
	public function dummy_lease(){
		
	}
	
	function incentivesFindWords($words, $search) {
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

public function get_dealer_zipcode($dealer_code, $userZipCode){
		$dealers_list = array();
    	if(\Databucket::hexists('calc:zipcode:dealer', 'data')){
			$_dealers = \Databucket::isCacheHMGet('calc:zipcode:dealer', 'data'); 
			if(count($_dealers) > 0){
				 $dealers_list = json_decode($_dealers[0], true);
			}
		}else{
			$_dealers = \Databucket::sqlZipcodeAllDealers();
			\Databucket::isCacheHMSet('calc:zipcode:dealer', 'data',$_dealers); 
			$dealers_list = $_dealers->toArray(); 
		} 
		
		if(!empty($dealers_list)){
			$_dealer_combine_array = array_column($dealers_list, 'dlr_shw_zip', 'dlr_code');
			if(array_key_exists($dealer_code, $_dealer_combine_array)){
				return $_dealer_combine_array[$dealer_code];
			}
		}
		
		return $userZipCode;
    }


	public function get_mdoca_alternate_dealer_code($dealer_code){
		$mdoca_alternate_dealers_list = array();
    	if(\Databucket::hexists('calc:mdoca:alternatedealers', 'data')){
			$mdoca_alternate_dealers = \Databucket::isCacheHMGet('calc:mdoca:alternatedealers', 'data'); 
			if(count($mdoca_alternate_dealers) > 0){
				 $mdoca_alternate_dealers_list = json_decode($mdoca_alternate_dealers[0], true);
			}
		}else{
			$mdoca_alternate_dealers = \Databucket::sqlMdocaAlternateDealers();
			\Databucket::isCacheHMSet('calc:mdoca:alternatedealers', 'data',$mdoca_alternate_dealers); 
			$mdoca_alternate_dealers_list = $mdoca_alternate_dealers->toArray(); 
		}
		if(!empty($mdoca_alternate_dealers_list)){
			$mdoca_dealer_combine_array = array_combine(array_column($mdoca_alternate_dealers_list, 'actual_dealer_code'), array_column($mdoca_alternate_dealers_list, 'alternate_dealer_code'));
			if(array_key_exists($dealer_code, $mdoca_dealer_combine_array)){
				return $mdoca_dealer_combine_array[$dealer_code];
			}
		}
		return $dealer_code;
    }



	function lease_sample_rebates(){
		return array (
						  0 => 
						  array (
							'incentiveId' => '16061626',
							'name' => 'FCA US ZF 9-Speed Settlement Voucher - 46CKP3',
							'amount' => 4000,
							'expirationDate' => '2020-09-13',
							'effectiveDate' => '2019-09-13',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'ZF 9-Speed Settlement Voucher',
						  ),
						  1 => 
						  array (
							'incentiveId' => '17402594',
							'name' => 'FCA US Military Program',
							'amount' => 500,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-02-04',
							'groupAffiliation' => 'Military',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Military Program',
						  ),
						  2 => 
						  array (
							'incentiveId' => '17142121',
							'name' => 'FCA US National Association of Realtors Employee (Type 1/L)',
							'amount' => 500,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'Association/Club Member',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'National Association of Realtors Employee (Type 1/L)',
						  ),
						  3 => 
						  array (
							'incentiveId' => '17154938',
							'name' => 'FCA US Total Loss Direct Offer Program',
							'amount' => 1000,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'Conquest',
							'source' => 'OEM',
							'programName' => 'Total Loss Direct Offer Program',
						  ),
						  4 => 
						  array (
							'incentiveId' => '17469141',
							'name' => 'FCA US New Buyer Share of Garage TDM Offer',
							'amount' => 500,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-03-01',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'New Buyer Share of Garage TDM Offer',
						  ),
						  5 => 
						  array (
							'incentiveId' => '17200915',
							'name' => 'FCA US Ram Promaster City Private Conquest Offer TDM - 39CLE',
							'amount' => 1000,
							'expirationDate' => '2020-03-31',
							'effectiveDate' => '2020-02-01',
							'groupAffiliation' => 'Targeted',
							'previousOwnership' => 'Conquest',
							'source' => 'OEM',
							'programName' => 'Ram Promaster City Private Conquest Offer TDM',
						  ),
						  6 => 
						  array (
							'incentiveId' => '16061731',
							'name' => 'FCA US ZF 9-Speed Settlement Voucher - 46CKP1',
							'amount' => 1000,
							'expirationDate' => '2020-09-13',
							'effectiveDate' => '2019-09-13',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'ZF 9-Speed Settlement Voucher',
						  ),
						  7 => 
						  array (
							'incentiveId' => '17469468',
							'name' => 'FCA US Tipp 1A Owner Program TDM Offer - 40CL7',
							'amount' => 750,
							'expirationDate' => '2020-04-30',
							'effectiveDate' => '2020-03-01',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Tipp 1A Owner Program TDM Offer',
						  ),
						  8 => 
						  array (
							'incentiveId' => '17053708',
							'name' => 'FCA US Driveability / Automobility Program',
							'amount' => 1000,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'Automobility',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Driveability / Automobility Program',
						  ),
						  9 => 
						  array (
							'incentiveId' => '16061664',
							'name' => 'FCA US ZF 9-Speed Settlement Voucher - 46CKP2',
							'amount' => 2000,
							'expirationDate' => '2020-09-13',
							'effectiveDate' => '2019-09-13',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'ZF 9-Speed Settlement Voucher',
						  ),
						  10 => 
						  array (
							'incentiveId' => '17469185',
							'name' => 'FCA US Tipp 1A Prospect Program TDM Offer - 40CL8',
							'amount' => 1000,
							'expirationDate' => '2020-04-30',
							'effectiveDate' => '2020-03-01',
							'groupAffiliation' => 'Targeted',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Tipp 1A Prospect Program TDM Offer',
						  ),
						  11 => 
						  array (
							'incentiveId' => '17469237',
							'name' => 'FCA US Tipp 1A Prospect Program TDM Offer - 40CL9',
							'amount' => 1500,
							'expirationDate' => '2020-04-30',
							'effectiveDate' => '2020-03-01',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Tipp 1A Prospect Program TDM Offer',
						  ),
						  12 => 
						  array (
							'incentiveId' => '17402922',
							'name' => 'FCA US First Responder Bonus Consumer Cash',
							'amount' => 500,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'First Responders',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'First Responder Bonus Consumer Cash',
						  ),
						  13 => 
						  array (
							'incentiveId' => '17469073',
							'name' => 'FCA US Tipp 1A Owner Program TDM Offer - 40CL6',
							'amount' => 500,
							'expirationDate' => '2020-04-30',
							'effectiveDate' => '2020-03-01',
							'groupAffiliation' => 'Targeted',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Tipp 1A Owner Program TDM Offer',
						  ),
						  14 => 
						  array (
							'incentiveId' => '17142228',
							'name' => 'FCA US National Association of Realtors Members',
							'amount' => 500,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'Association/Club Member',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'National Association of Realtors Members',
						  ),
						  15 => 
						  array (
							'incentiveId' => '17541271',
							'name' => 'FCA US Conquest Lease to Retail/Lease',
							'amount' => 500,
							'expirationDate' => '2020-03-31',
							'effectiveDate' => '2019-10-01',
							'groupAffiliation' => 'No Specific Group Affiliation',
							'previousOwnership' => 'Lease Conquest',
							'source' => 'OEM',
							'programName' => 'Conquest Lease to Retail/Lease',
						  ),
						  16 => 
						  array (
							'incentiveId' => '17142263',
							'name' => 'FCA US Partnerships/Events Email Offer',
							'amount' => 1000,
							'expirationDate' => '2021-01-04',
							'effectiveDate' => '2020-01-03',
							'groupAffiliation' => 'Direct Mail',
							'previousOwnership' => 'No Previous Ownership Requirement',
							'source' => 'OEM',
							'programName' => 'Chrysler Partnerships/Events Email Offer',
						  ),
						  17 => 
						  array (
							'incentiveId' => '17602807',
							'name' => 'FCA US 2020 Returning Lessee - 38CLB3',
							'amount' => 250,
							'expirationDate' => '2020-03-31',
							'effectiveDate' => '2019-12-03',
							'groupAffiliation' => 'No Specific Group Affiliation',
							'previousOwnership' => 'Lease Loyalty',
							'source' => 'OEM',
							'programName' => '2020 Returning Lessee',
						  ),
						)  ;
	}
	
	
	function lease_sample_result(){
		return  array (
							  'status' => 'success',
							  'result' => 
							  array (
								0 => 
								array (
								  '@class' => '.NonCashCustomerQuote',
								  'paymentWithoutTaxes' => 
								  array (
									'@class' => '.LeasePayment',
									'amountFinanced' => 37341.0,
									'monthlyPayment' => 802.8,
									'monthlyPaymentWithoutDealerAddOns' => 802.8,
									'dueAtSigning' => 4951.8,
									'totalAmountDueAtSigning' => 4951.8,
									'inceptionFees' => 802.8,
									'baseMonthlyPayment' => 0.0,
									'itemizedDealerAddOns' => 
									array (
									),
									'adjustedResidualAmount' => 21989.7,
									'totalDownPayment' => 4149.0,
									'customerCash' => 4149,
								  ),
								  'term' => 24,
								  'specialProgram' => false,
								  'rebatesTotal' => 0,
								  'rebateDetails' => 
								  array (
								  ),
								  'programTitle' => 'FCA US CCAP Prime Standard Lease - V1 [1] FCA US National',
								  'programName' => 'Chrysler Capital Prime Standard Lease',
								  'financeSourceId' => 'F00CHC',
								  'residualPercentage' => 53.0,
								  'expirationDate' => '2020-03-31',
								  'effectiveDate' => '2020-03-03',
								  'moneyFactor' => 0.00275,
								),
								1 => 
								array (
								  '@class' => '.NonCashCustomerQuote',
								  'paymentWithoutTaxes' => 
								  array (
									'@class' => '.LeasePayment',
									'amountFinanced' => 37341.0,
									'monthlyPayment' => 759.01,
									'monthlyPaymentWithoutDealerAddOns' => 759.01,
									'dueAtSigning' => 4908.01,
									'totalAmountDueAtSigning' => 4908.01,
									'inceptionFees' => 759.01,
									'baseMonthlyPayment' => 0.0,
									'itemizedDealerAddOns' => 
									array (
									),
									'adjustedResidualAmount' => 21159.9,
									'totalDownPayment' => 4149.0,
									'customerCash' => 4149,
								  ),
								  'term' => 27,
								  'specialProgram' => false,
								  'rebatesTotal' => 0,
								  'rebateDetails' => 
								  array (
								  ),
								  'programTitle' => 'FCA US CCAP Prime Standard Lease - V1 [1] FCA US National',
								  'programName' => 'Chrysler Capital Prime Standard Lease',
								  'financeSourceId' => 'F00CHC',
								  'residualPercentage' => 51.0,
								  'expirationDate' => '2020-03-31',
								  'effectiveDate' => '2020-03-03',
								  'moneyFactor' => 0.00273,
								),
								2 => 
								array (
								  '@class' => '.NonCashCustomerQuote',
								  'paymentWithoutTaxes' => 
								  array (
									'@class' => '.LeasePayment',
									'amountFinanced' => 37341.0,
									'monthlyPayment' => 636.14,
									'monthlyPaymentWithoutDealerAddOns' => 636.14,
									'dueAtSigning' => 4785.14,
									'totalAmountDueAtSigning' => 4785.14,
									'inceptionFees' => 636.14,
									'baseMonthlyPayment' => 0.0,
									'itemizedDealerAddOns' => 
									array (
									),
									'adjustedResidualAmount' => 18255.6,
									'totalDownPayment' => 4149.0,
									'customerCash' => 4149,
								  ),
								  'term' => 39,
								  'specialProgram' => false,
								  'rebatesTotal' => 0,
								  'rebateDetails' => 
								  array (
								  ),
								  'programTitle' => 'FCA US CCAP Prime Standard Lease - V1 [1] FCA US National',
								  'programName' => 'Chrysler Capital Prime Standard Lease',
								  'financeSourceId' => 'F00CHC',
								  'residualPercentage' => 44.0,
								  'expirationDate' => '2020-03-31',
								  'effectiveDate' => '2020-03-03',
								  'moneyFactor' => 0.00264,
								),
								3 => 
								array (
								  '@class' => '.NonCashCustomerQuote',
								  'paymentWithoutTaxes' => 
								  array (
									'@class' => '.LeasePayment',
									'amountFinanced' => 37341.0,
									'monthlyPayment' => 651.55,
									'monthlyPaymentWithoutDealerAddOns' => 651.55,
									'dueAtSigning' => 4800.55,
									'totalAmountDueAtSigning' => 4800.55,
									'inceptionFees' => 651.55,
									'baseMonthlyPayment' => 0.0,
									'itemizedDealerAddOns' => 
									array (
									),
									'adjustedResidualAmount' => 19085.4,
									'totalDownPayment' => 4149.0,
									'customerCash' => 4149,
								  ),
								  'term' => 36,
								  'specialProgram' => false,
								  'rebatesTotal' => 0,
								  'rebateDetails' => 
								  array (
								  ),
								  'programTitle' => 'FCA US CCAP Prime Standard Lease - V1 [1] FCA US National',
								  'programName' => 'Chrysler Capital Prime Standard Lease',
								  'financeSourceId' => 'F00CHC',
								  'residualPercentage' => 46.0,
								  'expirationDate' => '2020-03-31',
								  'effectiveDate' => '2020-03-03',
								  'moneyFactor' => 0.00256,
								),
								4 => 
								array (
								  '@class' => '.NonCashCustomerQuote',
								  'paymentWithoutTaxes' => 
								  array (
									'@class' => '.LeasePayment',
									'amountFinanced' => 37341.0,
									'monthlyPayment' => 588.07,
									'monthlyPaymentWithoutDealerAddOns' => 588.07,
									'dueAtSigning' => 4737.07,
									'totalAmountDueAtSigning' => 4737.07,
									'inceptionFees' => 588.07,
									'baseMonthlyPayment' => 0.0,
									'itemizedDealerAddOns' => 
									array (
									),
									'adjustedResidualAmount' => 16596.0,
									'totalDownPayment' => 4149.0,
									'customerCash' => 4149,
								  ),
								  'term' => 48,
								  'specialProgram' => false,
								  'rebatesTotal' => 0,
								  'rebateDetails' => 
								  array (
								  ),
								  'programTitle' => 'FCA US CCAP Prime Standard Lease - V1 [1] FCA US National',
								  'programName' => 'Chrysler Capital Prime Standard Lease',
								  'financeSourceId' => 'F00CHC',
								  'residualPercentage' => 40.0,
								  'expirationDate' => '2020-03-31',
								  'effectiveDate' => '2020-03-03',
								  'moneyFactor' => 0.00289,
								),
							  ),
							)  ;
	}
}

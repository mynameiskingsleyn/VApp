<?php

namespace Fcaore\Databucket\Http;

use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Cookie;
use App\Facades\OreDSClass;
use Fcaore\Databucket\Facade\Databucket;
use App\Vinactivation;

class SNIController extends Controller
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
     * Collection of entire vehicle details
     * @FilterQuery: $getAllVehicle
     * @type : Array
     */
    private $getAllVehicle;

	private $current_make;


    /**
     * Constructor
     *
     * @param $Models
     */
    public function __construct(){
        $this->googleKey= env("GOOGLE_APIKEY", "");
       // $this->today = substr(Carbon::today('America/Vancouver'), 0 ,10);
       $this->today = substr(Carbon::today(), 0 ,10);
	  // if(\Ore::getSessionID() == '')  session()->regenerate();
        $this->masterMemorycheck = '';
    }

    public function summaryQuery(){
        $sniParamsCacheKey = config('databucket.isCacheWithDate') ? "summary:".$this->today_date : "summary:";


        if(!Databucket::isCacheExists($sniParamsCacheKey)){
                $this->getAllInventory =json_encode(Databucket::sniSummaryQuery());
                Databucket::isCacheSet($sniParamsCacheKey, $this->getAllInventory);
          }else{
                 $this->getAllInventory = Databucket::isCacheGet($sniParamsCacheKey);
         }

    }

    public function sniSegregateSummary($params_vechType,  $params_year, $params_subcatid){

                     if(strtolower($params_vechType) == 'new'){
						$sniParamsCacheKey = Databucket::makeCache(strtolower($params_vechType).':'.$params_year.':'.$params_subcatid);
					 }else{
					  $params_subcatid = array_filter($params_subcatid);
					 $params_subcatid_array2string = implode("-", array_values($params_subcatid));

						 //if(count($params_subcatid) > 0){
							$sniParamsCacheKey = Databucket::makeCache(strtolower($params_vechType).':max-'.$params_year.':'.$params_subcatid_array2string);
						 //}else return true;
					 }

                    /*  if(!Databucket::isCacheExists($sniParamsCacheKey)){
                        $this->getSegregateInventory = json_encode(Databucket::sniSegregateSummaryQuery($params_vechType, $params_year, $params_subcatid));
                        Databucket::isCacheSet($sniParamsCacheKey, $this->getSegregateInventory);
                    }else{
                        $this->getSegregateInventory = Databucket::isCacheGet($sniParamsCacheKey);
                    }  */
					 if(!Databucket::isCacheExists($sniParamsCacheKey)){
                        $this->getSegregateInventory = json_encode(Databucket::sniSegregateSummaryQuery($params_vechType, $params_year, $params_subcatid));
                        if($this->getSegregateInventory !='false'){
                            Databucket::isCacheSet($sniParamsCacheKey, $this->getSegregateInventory);
                        }else{
                            $this->getSegregateInventory = [];
                        }
                    }else{
                        $this->getSegregateInventory = Databucket::isCacheGet($sniParamsCacheKey);
                    }


            return true;

    }
 /**
     * Return Inventory Filter Results
     *
     * @param $request
     * @return json
     * @throws Exception
     */
    public function getInventoryResults($data, $kargs){
		try{
            $datas['datarow'] = json_decode($data, true);
            if(count($kargs['cat_veh_id'])>0 || count($kargs['subcat_id'])>0){
				$total_vin=Databucket::get_match_rows($datas); 

				$matching_vin=Databucket::get_match_rows($datas,$kargs); 
				$output['exact_match']=Databucket::exact_match($matching_vin);

				//intersect value of matching vins
                $output['partial_match']=Databucket::partial_match($total_vin,$output['exact_match']);
            }else{

				$matching_vin=Databucket::get_match_rows($datas);
                $output['exact_match']=Databucket::partial_match($matching_vin);
                $output['partial_match']=array();
            }

			if(config('ore.vinmanagement.activate')){
				$array_diff = array();

				$cache_name =  Databucket::makeCache('dlrmgt:vinmanagement:alldealers');
				if(Databucket::isCacheExists($cache_name)){
					$array_diff = json_decode(Databucket::isCacheGet($cache_name), true);
				}else{
					$array_diff =  Databucket::SqlVinActivate('empty');
					// ARRAY FOR VIN-DEACTIVATED LIST
				}

				$output['exact_match'] = array_diff($output['exact_match'], $array_diff);
				$output['partial_match'] = array_diff($output['partial_match'], $array_diff); 
			}

			return $output;
		}catch(\Exception $e){
			dd($e);
		}
	}



	/***********
		Below code
	*******/
 public function sni_list(Request $request, $make, $tiers,  $year,$model){
 		$make = strtolower($make);
		$zipcode 					= $request->get('zipcode') ?? '';
		$params_radius 				= $request->get('radius') ?? '25';
		$params_color 				= $request->get('color') ?? '';
		$params_drivetype 			= $request->get('drivetype') ?? '';
		$params_trim 				= $request->get('trim') ?? '';
		$params_engine 				= $request->get('engine') ?? '';
		$params_transmission 		= $request->get('transmission') ?? '';
		$vehicle_type 				= $request->get('vehicle_type') ?? 'new';

		if(!empty($zipcode)){
			if (!ctype_digit($zipcode)) { 
				$zipcode = 00000; 
			}
			$zipcode = str_pad($zipcode, 5, 0, STR_PAD_LEFT);
		} 

		/************** OPTION CODES IN PARAMS **********/
		if($params_engine!=''){
			$params_engine =substr($params_engine, 0,3);
			$eng_options_desc = Databucket::isCacheHMGet($params_engine, 'options_desc');
			if(!empty($eng_options_desc[0])){
				$params_engine = $eng_options_desc[0];
			}
		}

		if($params_transmission!=''){
			$params_transmission = substr($params_transmission, 0,3);
			$trans_options_desc = Databucket::isCacheHMGet($params_transmission, 'options_desc');
			if(!empty($trans_options_desc[0])){
				$params_transmission = $trans_options_desc[0];
			}
		}

		if($params_color!=''){
			$params_color = substr($params_color, 0,3);
			$clr_options_desc = Databucket::isCacheHMGet($params_color, 'options_desc');
			if(!empty($clr_options_desc[0])){
				$params_color = $clr_options_desc[0];
			}
		}

		/******* Parameter Validator ********************/
		if($request->has('type')){
			$vehicle_type = $request->get('type') ?? 'new';
		}
		//if(strtoupper($model) == '4C') $model = "4c-spider";
		$vehicle_type = 'new';
		if(strtoupper($vehicle_type) == 'NEW' || strtoupper($vehicle_type) == 'CPO' || strtoupper($vehicle_type) == 'USED'){}

		if (!ctype_digit($params_radius)) $params_radius = 25;
		if ($params_radius <= 0)  $params_radius = 25;
		if ($params_radius > 150)  $params_radius = 150;

		$model 							= str_replace("_","-", $model);
		$model 							= str_replace(" ","-", $model);
		$model 							= str_replace(" ","-", $model);
		$model 							= str_replace("_","-", $model);
		$model 							= str_replace("â","", $model);
		$model 							= str_replace("Â","", $model);

		/************/
		/* foreach($this->unResolvedModels as $key=>$value ){
			if($model==$key){
				//$model=$value;
			}
		} */
		$params['params_radius'] 		= $params_radius ;
		$params['params_trim'] 			= $params_trim ;
		$params['params_drivetype'] 	= $params_drivetype ;
		$params['params_color'] 		= $params_color ;
		$params['params_engine'] 		= $params_engine ;
		$params['params_transmission'] 	= $params_transmission;
		$params['params_model'] 		= $model;

		if($tiers=='t3' && !$request->has('dealercode')){
			return view('databucket::sni.results.redirect_popup');
		}

		if( ($tiers=='t1' || $tiers=='t3') && $request->has('vin') && $request->has('dealercode')){
			$isTier_vin 		= $request->get('vin');
			$isTier_dealercode = $request->get('dealercode');
			$isTier_dealercode = str_pad($isTier_dealercode, 5, 0, STR_PAD_LEFT);
			$isTierRedirect = false;
			if(!\Databucket::isCacheExists($isTier_vin)){
				$data = \App\Vehicle::where(['vin' => $isTier_vin])->first();
				if($data == null){
					$isTierRedirect = false;
				}else{ $isTierRedirect = true; }
			}else{ $isTierRedirect = true; }

			if($isTierRedirect){
				return redirect()->route('vehicle_params_'.$tiers, ['vin' => $isTier_vin, 'dealercode'=>$isTier_dealercode, 'vehicle_type'=>$vehicle_type]);
			}
		}

		if($request->has('dealercode') || $request->has('Dealercode') || $request->has('dealerCode') || $request->has('DealerCode')) {
			$dealerCode = $request->get('dealercode');
			if($dealerCode == '') $request->get('Dealercode');
			if($dealerCode == '') $request->get('dealerCode');
			if($dealerCode == '') $request->get('DealerCode');
			$dealerCode = str_pad($dealerCode, 5, 0, STR_PAD_LEFT);
			$dealerAddress = '';
			$dealerarrayresult = \Ore::DealerApi($dealerCode); 
			if($dealerarrayresult['dealerName']){ $dealerName = $dealerarrayresult['dealerName']; } else $dealerName = '';
				if($dealerName!=''){
					$dealerName 		= $dealerarrayresult['dealerName'];
					$dealerAddress 	.= $dealerarrayresult['dealerAddress1'].', ' ?? '';
					$dealerAddress 	.= $dealerarrayresult['dealerCity'].', ' ?? '';
					$dealerAddress 	.= $dealerarrayresult['dealerState'].' ' ?? '';
					$dealerZip 		= substr($dealerarrayresult['dealerZip'],0,5) ?? '';
					$dlink = '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$dealerAddress.$dealerZip.'" class="header-gmap" data-gmapaddress="'.$dealerAddress.$dealerZip.'">'.$dealerAddress.$dealerZip.'</a>';

					//$isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealerName));
					//$isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
					//$isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
					//$isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
					//$isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
					//$isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
					//$isDealer =  Str::replaceFirst('alfa romeo', '', strtolower($dealerName));

					$isDealer = $dealerName;
					$isDealer =  ucwords($isDealer);
					$return_dealer_header = "<h3>".$isDealer."</h3><p class='themeClr2 vehicle_para'><i class='fa fa-map-marker themeClr2' aria-hidden='true'></i>  ".$dlink."</p>";
					$params['params_dealercode'] = $dealerCode;

				}else{
					$dlink = '';
					$isDealer = '';
					$return_dealer_header = '';
					$params['params_dealercode'] = '';
				}

		}else {
			$params['params_dealercode'] = $return_dealer_header = '';
		}

		if($tiers=='t1' || $tiers=='ore') { $return_dealer_header = ''; }

		$params['params_tiers']	= $tiers;

		 if($make == 'alfaromeo') $make='alfa_romeo';
		  if($make == 'alfa_romeo') $hmcache_make = 'alfa_romeo'; else $hmcache_make = $make;

		$cache_name =  Databucket::makeCache('catsubcat:'.strtolower($vehicle_type).':'.strtolower(str_replace('-','_',str_replace(' ','_',$hmcache_make))).':'.Databucket::customModel($model).':'.$year);

		if(!Databucket::isCacheExists('summary:')){
			$this->summaryQuery();
		}
		if(!Databucket::hexists($cache_name, 'cat_id')){
			$pass_model = str_replace('-',' ',$model);
			$pass_model = str_replace('_',' ',$pass_model);
			Databucket::CategoriesSubCategoryIDInititlizedCacheSystems($vehicle_type,$make, $year, $pass_model);
		}
			list($cat_id) 	= Databucket::isCacheHMGet($cache_name, 'cat_id');
			list($subcat_id)= Databucket::isCacheHMGet($cache_name, 'subcat_id');

			$params['params_year'] = $year;
			$params['params_syear'] = 2015;
			$params['params_catid'] = $cat_id;
			$params['params_subcatid'] = $subcat_id;

			if(strtolower($vehicle_type)=='new')
					$params['params_vechType'] = "NEW";
			else if(strtolower($vehicle_type)=='cpo')
				$params['params_vechType'] = "CPO";
			else $params['params_vechType'] = 'USED';


			if ($zipcode) { $params['zipcode'] = $zipcode; } else { $params['zipcode'] = $zipcode = "" ;}

			$cache_name = '';
			$cache_name =  Databucket::makeCache('msrptow:'.strtolower($vehicle_type).':'.Databucket::customModel($hmcache_make).':'.Databucket::customModel($model).':'.$year);

			 if(!Databucket::isCacheExists($cache_name)){
					/*
					* msrptow cache absent then it loads.
					*/
					//Databucket::sni1($vehicle_type, $make);


			}
				$maxminarray = Databucket::maxPriceCustom(strtolower($vehicle_type), Databucket::customModel($hmcache_make), Databucket::customIBModel($model), $year);
				foreach($maxminarray as $val){
					$msrp_price = $val->min;
					$maxs_msrp = $val->max;
				}
				
			 if($msrp_price<0 || $msrp_price=='') $msrp_price = '5000';
			 if($maxs_msrp<0 || $maxs_msrp=='') $maxs_msrp = '90000';
			$towing_capacity_count = Databucket::isCacheHMGet($cache_name, 'towing_capacity_count') ;
			// CPO CODE SNIPPET
			/* if($vehicle_type != 'new') { $msrp_price[0] = $sprice; $maxs_msrp[0] = $eprice;} */

			$params['params_make'] = $make;

			$left_form = $this->sniFilters($params);

			$left_form['getAllFilterRows']['drive_tier1'] = urldecode($params_drivetype);
			$left_form['getAllFilterRows']['trim_tier1'] = urldecode($params_trim);
			$left_form['getAllFilterRows']['color_tier1'] = urldecode($params_color);
			$left_form['getAllFilterRows']['EngDesc_tier1'] = urldecode($params_engine);
			$left_form['getAllFilterRows']['dealers_tier1'] = '';
			$left_form['getAllFilterRows']['Transmission_tier1'] = urldecode($params_transmission);;

			$left = Databucket::SNI_Left_Attributes($left_form['getAllFilterRows']);
			 return view('databucket::sni.sni_results_frame',[
					 'vehicle_type' => $vehicle_type,
					 'tier' => $tiers,
					 'params_make' => $make,
					 'params_modelname' => $model,
					 'params_year' => $year,
					 'params_radius' => $params_radius,
					 'params_dealercode' => $params['params_dealercode'],
					 'zipcode' => $zipcode,
					 'syear' => 0,
					 'params_catid' => $cat_id,
					 'params_subcatid' => $subcat_id,
					 'params_vechType' => $vehicle_type,
					 'msrp_price' => ($msrp_price),
					 'maxs_msrp' => ($maxs_msrp),
					 'towing_capacity_count' => $towing_capacity_count[0],
					 'left' => $left,
					 'return_dealer_header'=>$return_dealer_header
			 ]);
 }

 public function sniFilters($params){
        $kargs=$result=array();
		$params_year = $params['params_year'];
		$params_syear = $params['params_syear'];
		$params_catid = $params['params_catid'];
		$params_subcatid=$params['params_subcatid'];
        $params_vechType =$params['params_vechType'];
		$params_model =$params['params_model'];
		$params_make =$params['params_make'];
		 $params_radius = '25';

	    if(strtolower($params_vechType) == 'new'){

			$cache_names =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.$params_make.':'.Databucket::customModel($params_model).':'.$params_year);

			if(Databucket::hexists($cache_names, 'cat_id')){
						list($params1_catid) = Databucket::isCacheHMGet($cache_names, 'cat_id');
						list($params1_subcatid) = Databucket::isCacheHMGet($cache_names, 'subcat_id');
					}
		}else{
			// CPO CODE SNIPPET

			/*$params_subcatid = [];
			$params_catid = [];

				for($mmo = $params_year;$mmo >= 2015; $mmo--){
				$params1_catid =  $params1_subcatid = '';
				$cache_names =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.$params_make.':'.strtolower(str_replace(' ','_',$params_model)).':'.$mmo);

				if(Databucket::hexists($cache_names, 'cat_id')){
					list($params1_catid) = Databucket::isCacheHMGet($cache_names, 'cat_id');
					list($params1_subcatid) = Databucket::isCacheHMGet($cache_names, 'subcat_id');
				}

					$params_subcatid[] = $params1_subcatid;
					$params_catid[] = $params1_catid;
				}
				*/
		}


        /*** Start */

		 $this->sniSegregateSummary($params_vechType, $params_year, $params_subcatid);


        if(gettype($this->getSegregateInventory) == 'array'){
            $data['attributes'] = $this->getSegregateInventory;
        } else{
            $data['attributes'] = json_decode($this->getSegregateInventory, true);
        }

	if(strtolower($params_vechType) == 'new'){
		$result['getAllFilterRows'] = Databucket::getFilterRows($data, $params_vechType, $params_year, $params_catid, $params_subcatid);
	}else{
		$result['getAllFilterRows'] = Databucket::getFilterRows_cpoused($data, $params_vechType, $params_year, $params_catid, $params_subcatid);
	}

		 //if(count($result['getAllFilterRows']['towing'])>0){
			//$result['maxtowing']=max(array_values($result['getAllFilterRows']['towing']));
		//}
			return $result;
    }




	public function sniRightSide(Request $request){
            $params = $request->all();
			$priceRange=$towing='';
			$canExpandSearchWithin = false;
            $usePartialDealers = true;
            //$partialDealers = $usePartialDealers ? ' and sing partial data ' :'';
            $timer_comment = ' with cache ';
            $search_again = false;
            //dd($search_again);
			if($request->has('params_year')) 		$params_year 		= $params['params_year']; else $params_year = '2019';
			if($request->has('params_syear')) 		$params_syear 		= $params['params_syear']; else $params_syear = '2016';
			if($request->has('params_catid'))		$params_catid 		= $params['params_catid']; else $params_catid = '36';
			if($request->has('params_subcatid')) 	$params_subcatid 	= $params['params_subcatid']; else $params_subcatid = '234';
			if($request->has('params_vechType')) 	$params_vechType 	= $params['params_vechType']; else $params_vechType = 'new';
			if($request->has('params_make')) 		$make_value 		= $params['params_make']; else $make_value = 'alfa_romeo';
			if($request->has('params_modelname')) 	$modelName 			= $params['params_modelname']; else $modelName = 'giliua';
			if($request->has('dealerZipCode')) 		$zipcode 			= $params['dealerZipCode']; else $zipcode = '00000';
		if($request->has('searchWithIn_hidden')) $search_in 		= $params['searchWithIn_hidden']; else $search_in = 25;
		if($request->has('tier')) 				$tier 				=  $params['tier']; else $tier = 'ore';
        if($request->has('priceRange_hidden')) 				$priceRange 				=  $params['priceRange_hidden']; else $priceRange = '20000,140000';

			if($request->has('type'))
					$vehicle_type = $params['type'];
			else
					$vehicle_type = 'new';

			
			if(!is_numeric($search_in)) $search_in = 25;
			if($search_in<=0) $search_in=25;
			if($search_in>150) $search_in=150;


		$pass_model = Databucket::customModel($modelName);
        $now = Databucket::getTime();
		if(strtolower($params_vechType) == 'new'){
		$cache_name =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.$make_value.':'.$pass_model.':'.$params_year);


	 	if(!Databucket::hexists($cache_name, 'cat_id')){
			Databucket::CategoriesSubCategoryIDInititlizedCacheSystems($params_vechType,$make_value, $params_year, $pass_model);
		}


			list($params_catid) = Databucket::isCacheHMGet($cache_name, 'cat_id');
			list($params_subcatid) = Databucket::isCacheHMGet($cache_name, 'subcat_id');
		}else{
			// CPO CODE SNIPPET
			/*$params_subcatid = [];
			$params_catid = [];

			for($mmo = $params_year;$mmo >= 2017; $mmo--){
				$params1_catid =  $params1_subcatid = '';

				$cache_names =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.$make_value.':'.strtolower(str_replace(' ','_',$modelName)).':'.$mmo);

				if(Databucket::hexists($cache_names, 'cat_id')){
					list($params1_catid) = Databucket::isCacheHMGet($cache_names, 'cat_id');
					list($params1_subcatid) = Databucket::isCacheHMGet($cache_names, 'subcat_id');
				}

				$params_subcatid[] = $params1_subcatid;
				$params_catid[] = $params1_catid;

			}*/
		}
			$kargs=array();
			$catVehArray=array();
			$subcatArray=array();
            $dealer_array=array();
            $total_count = 10;
            $inventory_type = 'both';
            $param_limit_exact= $param_limit_partial= 0;

			//use for sorting
			$milesOrder='asc';
			$msrpOrder='asc';

            if($request->has('lazy_type')){
                $inventory_type = $request->get('lazy_type');
                if( $inventory_type == 'e'){
                    $param_limit_exact= !$request->has('lazy_limit') ? 0 : ($request->get('lazy_limit')+$total_count) ;
                }else{
                    $param_limit_partial= !$request->has('lazy_limit') ? 0 : ($request->get('lazy_limit')+$total_count) ;
                }
            }

		/*
		*******************************************
		Filter Code on Top of right section start
		*/

		$masterMemorycheck = str_replace('-', '_',Databucket::makeCache('vehicle_table_'.(str_replace(' ', '_', $make_value)).$params_vechType.'_'.$params_year.$pass_model));
		if($usePartialDealers){
		    $masterMemorycheck = $masterMemorycheck.$zipcode;
            $this->masterMemorycheck = $masterMemorycheck;
		    $dealersGroup = 'dealers_'.$masterMemorycheck;
		    $myDealerArray= $this->getDealersArray($dealersGroup,$zipcode,$make_value,$modelName,$params_year,$params_vechType);
        }
        //$now = Databucket::getTime();
		if(!Databucket::isCacheExists($masterMemorycheck)){
            $timer_comment = ' without cache ';
		try{
				if(!Databucket::isCacheExists($masterMemorycheck)){
				    if($usePartialDealers){
				        $chunk_result = Databucket::MasterVehicleRelation($params_vechType, $make_value, $params_year, $modelName, $myDealerArray);
                    }else{
                        $chunk_result =	Databucket::MasterVehicleRelation($params_vechType, $make_value, $params_year, $modelName);
                    }
					 if(config('databucket.have_chunk')){
						$total_chunk = 0;
						foreach($chunk_result as $key => $value){

							Databucket::isCacheSet($masterMemorycheck.'_chunk'.$key,json_encode($value));
							$total_chunk++;
					  	}
					 	 Databucket::isCacheSet($masterMemorycheck,$total_chunk);
					}else{
						Databucket::isCacheSet($masterMemorycheck,json_encode($chunk_result));
					}

				 }

			} catch (Exception $e) {
				report($e);
				return false;
			}
		}

		 if($tier == 't3'){
		 	$dealercode = $request->get('dealercode');
		 	$dealercode = str_pad($dealercode, 5, 0, STR_PAD_LEFT);
			$dealer_array = array($dealercode);
			$search_in = 150;
		 }else{
				if($request->has('dealersCode')){
					$dealersCode = $request->get('dealersCode');

					$dealer_array=array_merge($dealer_array, $dealersCode);
                    $usePartialDealers = true;
				}else{
					 $dealersWithMiles = Databucket::expand_radius($zipcode,$search_in, $make_value, $modelName, $params_year, $params_vechType);

					if(gettype($dealersWithMiles['dealers']) == 'array') {
						$dealer_array = array_keys($dealersWithMiles['dealers']);
						$search_in = $dealersWithMiles['radius'];
					}
                    $canExpandSearchWithin = true;
				}
		 }

			//Trim Code id is Cat vehicle id
			if($request->has('trimCode')){
				$catVehArray=array_merge($catVehArray, $request->get('trimCode'));
			}
			//Towing range
			if($request->has('Towing_hidden')){
				$towing=$request->get('Towing_hidden');
			}

			//Other filter option contain subcat id
			$subcatparam=array('driveCode','colorCode','EngDescCode','TransmissionCode');
			foreach($subcatparam as $sub){
				if(isset($params[$sub])){
					$subcatArray=array_merge($subcatArray, $params[$sub]);
				}
			}

			//Towing range
			$secondaySort=$sortBy_param='';
			if($request->has('sortBy')){
				$secondaySort='yes';
				$sortBy_param=$request->get('sortBy');
			}


			$kargs['cat_veh_id']=$catVehArray;
			$kargs['subcat_id']=$subcatArray;

		/*
		Filter Code Ends
		*******************************************
		*/
            $this->sniSegregateSummary($params_vechType, $params_year, $params_subcatid);


			//getting ALL exact and partial match using FILTER Provided
            $inventory = $this->getInventoryResults($this->getSegregateInventory, $kargs);

           // $vehicleParamsCacheKey = str_replace('-', '_', Databucket::makeCache('vehicle_table_'.$make_value.$params_vechType.'_'.$params_year.$modelName));


	 try{
			$dddd=array();

	//	for($iy = $params_syear; $iy<= $params_year; $iy++ ){
				$vehicleParamsCacheKey = str_replace('-', '_',Databucket::makeCache('vehicle_table_'.$make_value.$params_vechType.'_'.$params_year.$pass_model));
			 	 

                if($usePartialDealers){
                    $vehicleParamsCacheKey = $this->masterMemorycheck;
                }
				if(gettype(Databucket::isCacheGet($vehicleParamsCacheKey)) == 'array'){

					 if(count(Databucket::isCacheGet($vehicleParamsCacheKey))>0){

						$dddd = array_merge($dddd, Databucket::isCacheGet($vehicleParamsCacheKey));
					 }
				}else{
					ini_set("memory_limit", "-1");
                    set_time_limit(0);

					$jsonString = Databucket::isCacheGet($vehicleParamsCacheKey);

					 //if (strlen($jsonString) * 10 > intval(ini_get('memory_limit')) * 1024 * 1024) {
                      ///   die ('Decoding this would exhaust the server memory. Sorry!');
                     //}

					$so = json_decode($jsonString, true);


					 if(count($so)>0){

						$dddd = array_merge($dddd, $so);
					 }

				}
			//} // FOR LOOP
		 } catch (Exception $e) {

				\Log::error('SNIController::sniRightSide::vehicleParamsCacheKey::Empty');
		}
			$data['datarow'] = $dddd; 

            $get_exact_match=Databucket::get_vehicle_details($data,$inventory["exact_match"],$dealer_array,$priceRange,$towing,$secondaySort,$sortBy_param,$params_vechType,$tier,'e');
             $s_e_m = array_diff($inventory["exact_match"],array_column($get_exact_match, 'vin'));
			 $temp_partial_inventory = array_merge($inventory["partial_match"],$s_e_m);
			 // Partial Start
   
			$get_partial_match=Databucket::get_vehicle_details($data, $temp_partial_inventory,$dealer_array,$priceRange,$towing,$secondaySort,$sortBy_param,$params_vechType,$tier,'p');
			//check if empty and expandable========================
            $totalMatch = array_merge($get_exact_match,$get_partial_match);

            //die('here');
            if(empty($totalMatch) && $canExpandSearchWithin && $search_in < 150){
                $search_again = true;
                // increase search radius and get more dealers by incremement of 25 until vehicles found or radius reaches 150
                do{
                    $search_in = $search_in + 25;
                 
                    $dealersWithMiles = Databucket::expand_radius($zipcode,$search_in, $make_value, $modelName, $params_year, $params_vechType);

                    if(gettype($dealersWithMiles['dealers']) == 'array') {
                        $dealer_array = array_keys($dealersWithMiles['dealers']);
                        //$search_in = $dealersWithMiles['radius'];
                    }
                  
				  
                    $get_exact_match=Databucket::get_vehicle_details($data,$inventory["exact_match"],$dealer_array,$priceRange,$towing,$secondaySort,$sortBy_param,$params_vechType,$tier,'e');
                    $s_e_m = array_diff($inventory["exact_match"],array_column($get_exact_match, 'vin'));
                    $temp_partial_inventory = array_merge($inventory["partial_match"],$s_e_m);
                    $get_partial_match=Databucket::get_vehicle_details($data, $temp_partial_inventory,$dealer_array,$priceRange,$towing,$secondaySort,$sortBy_param,$params_vechType,$tier,'p');
                    $totalMatch = array_merge($get_exact_match,$get_partial_match);
                }
                while(empty($totalMatch) && $search_in < 150 );
                if($search_in > 150){
                    $search_again = false;
                }
            }
			



            $exact_match = array_slice($get_exact_match, $param_limit_exact, $total_count, true);
            $partial_match = array_slice($get_partial_match, $param_limit_partial, $total_count, true);
            // e - Exact Match, p - Partial Match, else - Initial Load.

			/*************** Vin Deactivation *************/
			/* if dealer deactivate VIN over admin panel, we blocked. */
			$deactiveExcount=$deactivePacount=0;
			if(config('ore.calc.dealer_discount') && config('ore.vinmanagement.activate')){
				\Log::info("========sniRightSide-vinmanagement=======================");
		 		\Log::info($dealer_array);
				$deactivevin_array = [];
				if(!empty($dealer_array)){
					$deactivevin=Vinactivation::select('dealer_code','vins')->whereIn('dealer_code',$dealer_array)->get();
					\Log::info($deactivevin); 
					if(!$deactivevin->isEmpty()){
						$deactivevin_list = $deactivevin->toArray();
						//\Log::info($deactivevin_list);
						foreach ($deactivevin_list as $key => $value) {
							if(!empty($value['vins'])){
								$deactivevin_array = array_merge($deactivevin_array,explode(',',$value['vins']));									
							}
						}
					} 
				}
				\Log::info("========sniRightSide-vinmanagement-deactivevinlist=======================");
				\Log::info($deactivevin_array); 
				if(count($deactivevin_array) > 0){
						foreach($exact_match as $key=>$value)
						 {
							 if (in_array($value['vin'], $deactivevin_array))
							  {
							    unset($exact_match[$key]); $deactiveExcount+=1; 
							  }							  
						 }
						 foreach($partial_match as $key1=>$value1)
						 {
							 if (in_array($value1['vin'], $deactivevin_array))
							  {
							    unset($partial_match[$key1]); $deactivePacount+=1; 
							  }
						 } 
				}
			}
			 /** Vin Deactivation done */

			$ecnt=count($get_exact_match) - $deactiveExcount;
			$pcnt=count($get_partial_match) - $deactivePacount;
			$totalCnt=$ecnt+$pcnt;


			switch($make_value) {
				case 'chrysler': $make_code='C'; $make_url='chrysler'; break;
				case 'dodge': $make_code='D'; $make_url='dodge'; break;
				case 'fiat': $make_code='X'; $make_url='fiatusa'; break;
				case 'jeep': $make_code='J'; $make_url='jeep'; break;
				case 'ram': $make_code='R'; $make_url='ramtrucks'; break;
				case 'alfa_romeo': $make_code='Y'; $make_url='alfaromeousa'; break;
			}
            if($inventory_type=='e'){
                return view('databucket::sni.results.vehicle_list',['tier'=>$tier,'data'=>$exact_match,'zipcode'=>$params['dealerZipCode'],'search_in'=>$search_in,'make_code' => $make_code, 'make_url' => $make_url, 'make_value' => $make_value, 'condition'=>$params_vechType ] )->render();
            }else if($inventory_type=='p'){
                return view('databucket::sni.results.vehicle_list',['tier'=>$tier,'data'=>$partial_match,'zipcode'=>$params['dealerZipCode'],'search_in'=>$search_in,'make_code' => $make_code, 'make_url' => $make_url, 'make_value' => $make_value,  'condition'=>$params_vechType ])->render();
            }else{
                return view('databucket::sni.results.results_tabs',['tier'=>$tier,'e_count'=>$ecnt,'exact'=>$exact_match,'p_count'=>$pcnt,'partial'=>$partial_match,'totalCnt'=>$totalCnt,'zipcode'=>$params['dealerZipCode'],'search_in'=>$search_in,'make_code' => $make_code, 'make_url' => $make_url, 'make_value' => $make_value,  'condition'=>$params_vechType,'search_again'=>$search_again])->render();
            }

    }

	public function sniLeftFilter(Request $request){
        $params = $request->all();
        $kargs=$result=array();

		$params_zipcode 	= $params['zipcode'];

		if($request->has('params_year')) 		$params_year 		= $params['params_year']; else $params_year = '2019';

		 if($request->has('params_subcatid')) 	$params_subcatid 	= $params['params_subcatid']; else $params_subcatid = '234';

		$params_catid 		= $params['params_catid'];
		$params_vechType 	= $params['params_vechType'];
		$params_make 		= $params['params_make'];
		$params_model 		= $params['params_model'];
		$params_tier 		= $params['tier'];
		$params_radius 		= '25';

		 if($request->has('type')) 	$vehicle_type 	= $params['type']; else $vehicle_type = 'new';

		 if(strtoupper($vehicle_type) == 'NEW' || strtoupper($vehicle_type) == 'CPO' || strtoupper($vehicle_type) == 'USED'){ }else  $vehicle_type = 'new';


		$params_model 						= str_replace("_","-", $params_model);
		$params_model 						= str_replace(" ","-", $params_model);
		$params_model 						= str_replace(" ","-", $params_model);
		$params_model 						= str_replace("_","-", $params_model);


		/*
		// CPO Snippet
		if($params_vechType == 'new'){}else{

			$params_subcatid = [];

				for($mmo = $params_year;$mmo >= 2015; $mmo--){
					$params1_catid =  $params1_subcatid = '';
					$cache_names =  Databucket::makeCache('catsubcat:'.strtolower($params_vechType).':'.$params_make.':'.strtolower(str_replace(' ','-',$params_model)).':'.$mmo);

					if(Databucket::hexists($cache_names, 'cat_id')){
						list($params1_catid) = Databucket::isCacheHMGet($cache_names, 'cat_id');
						list($params1_subcatid) = Databucket::isCacheHMGet($cache_names, 'subcat_id');
						$params_subcatid[] = $params1_subcatid;
					}
				}

		} */

		   $this->sniSegregateSummary($params_vechType, $params_year, $params_subcatid);



        if(gettype($this->getSegregateInventory) == 'array'){
            $data['attributes'] = $this->getSegregateInventory;
        } else{
            $data['attributes'] = json_decode($this->getSegregateInventory, true);
        }


		//Get all filters
		if(strtolower($params_vechType) == 'new'){
			$result['getAllFilterRows'] = Databucket::getFilterRows($data, $params_vechType, $params_year, $params_catid, $params_subcatid);
		}else{
			$result['getAllFilterRows'] = Databucket::getFilterRows_cpoused($data, $params_vechType, $params_year, $params_catid, $params_subcatid);
		}



		//get towing
		if(count($result['getAllFilterRows']['towing'])>0){
			$result['maxtowing']=max(array_values($result['getAllFilterRows']['towing']));
		}

		 $dealer_specific_cache_array = array();

		$result['getAllFilterRows']['params_zipcode']=$params_zipcode;
		$result['getAllFilterRows']['radius']= $params_radius;

		$result['getAllFilterRows']['dealers']  = ['69221' => 'Lithia Alfa Romeo of Bryan College Station'];

		return $result;

    }
    /**
     * Retrieve Dealer information with Zipcode and Radius
     *
     * @var Illuminate\Http\Request $request
     *
     * @return array
     *
     */
    public function on_dealers_by_zipcode_radius(Request $request){
        $params 			= $request->all();
        $params_zipcode 	= $params['zipcode'];
        $params_radius 		= $params['radius'];
		$params_year 		= $params['params_year'];
		$params_vechType 	= $params['params_vechType'];
		$params_make 		= $params['params_make'];
		$params_model 		= $params['params_model'];
		$pageload 			= $params['pageload'];

		 $msrp_price			= '5000';
		 $maxs_msrp 			= '120000';

		$result=Databucket::expand_radius($params_zipcode,$params_radius,$params_make, $params_model, $params_year, $params_vechType);

		$maxminarray = Databucket::maxPriceCustom(strtolower($params_vechType), Databucket::customModel($params_make), Databucket::customIBModel($params_model), $params_year);
				foreach($maxminarray as $val){
					$msrp_price = $val->min;
					$maxs_msrp = $val->max;
				}
	
	
			 if($msrp_price<0 || $msrp_price=='') $msrp_price = '5000';
			  if($maxs_msrp<0 || $maxs_msrp=='') $maxs_msrp = '120000';

		  $result['msrp_price'] = $msrp_price;
		  $result['maxs_msrp'] 	= $maxs_msrp;
		   $result['pageload'] 	= $pageload;
		   $result_dealers = $result['dealers'];
		   $dealers_list = array();
		   foreach ($result_dealers as $key => $value) {
		   	$dealer_array = array();
		   	$dealer_array[$key] = $value;
		   	array_push($dealers_list, $dealer_array);
		   }
		   $result['dealers_list'] = $dealers_list;
		  return $result;
		 exit;
    }




	public static function cpoImageFetch($vin){
		 $thumbs = 'https://d11p9i1nddg3dz.cloudfront.net/images/loader.gif';
		  if(!\Databucket::isCacheExists($vin)){

			$data = \App\Vehicle::where(['vin' => $vin])->first();
			if($data === null){
				return redirect()->route('landing.on_load');
			}
			 $val1 = (object)$data;

			 if(!\Databucket::hexists($val1->vin, 'vin')){
				if ($val1->photo_URL!=''){
						$cpo_img = explode("|",$val1->photo_URL);
						if(count($cpo_img) > 0){
							$thumbs = $cpo_img[0];
						}

					}
			 }

			 return $thumbs;
	}
	}

	public static function getMiles($dlr_code,$zipcode,$radius,$make,$model,$year,$vehicle_type){

		$cacheName = 'radiusByQuery:'.$zipcode.':'.$radius.':'.strtolower(str_replace(" ","_",$make)).':'.strtolower(str_replace(" ","-",str_replace("_","-",$model))).':'.$year.':'.strtolower($vehicle_type);

		$cacheNameMiles = 'radiusByMiles:'.$zipcode.':'.$radius.':'.strtolower(str_replace(" ","_",$make)).':'.strtolower(str_replace(" ","-",str_replace("_","-",$model))).':'.$year.':'.strtolower($vehicle_type);


         $dealerName=json_decode(databucket::isCacheGet($cacheName), true);
		 $dealerMiles=json_decode(databucket::isCacheGet($cacheNameMiles), true);

		 if(!empty($dealerMiles)){
			if(array_key_exists($dlr_code,$dealerMiles)){
				if(Arr::has($dealerName,$dlr_code)) {
					$isDealer = Arr::get($dealerName,$dlr_code);
				//	$isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
				//	$isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
					$isDealer =  ucwords($isDealer);
				} else  $isDealer='';
				
				 echo '<li>
				<div class="detBlock rBlack"><span class="status header text-uppercase gcss-typography-label-4 gcss-colors-text-body-secondary">In Transit</span>
					<p class="bold size-18"><span class="miles miles-away gcss-typography-utility-heading-2">'.sprintf('%0.2f', $dealerMiles[$dlr_code]).'</span> <span class="size-15 rLight black gcss-typography-label-3">miles away.</span></p>
				</div>
				</li>
				<li><p class="productDet rLight dealer-name gcss-button-tertiary gcss-button-small">
			'.$isDealer.'
				</p>
				</li>';
				}
		 }else{
		 				 echo '<li>
				<div class="detBlock rBlack"><span class="status header text-uppercase gcss-typography-label-4 gcss-colors-text-body-secondary">In Transit</span>
					<p class="bold size-18"><span class="miles miles-away gcss-typography-utility-heading-2">Miles Can\'t Calculate</span> <span class="size-15 rLight black gcss-typography-label-3"></span></p>
				</div>
				</li>
				<li><p class="productDet rLight dealer-name gcss-button-tertiary gcss-button-small">				</p>
				</li>';
		 }

	}


	public function zipValidation(Request $request){
		$zipcode = $request->get('zipcode');
		Databucket::AllDealersCacheSystems();
		if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
			$getZipcode = \App\Zipcode::where(['zipcode' => $zipcode])->first();
			if ($getZipcode !== null) {
				$latitude 	= number_format(str_replace('+-','-',$getZipcode['latitude']),2);
				$longitude 	= number_format($getZipcode['longitude'],2);
				Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
				return array('status'=>'available','zipcode'=>$zipcode);
			}

			/*
			* Get Lat Lng by zipcode on calling google service provider.
			*/
			$result = Databucket::findLatLonByZip($zipcode);
			if($result && $result['status'] == 'success'){
				$cordinates = $result['message'];
				$create_array = array();
				$create_array['zipcode'] = $zipcode;
				$create_array['latitude'] = '+'.$cordinates['lat'];
				$create_array['longitude'] = $cordinates['lng'];
				\App\Zipcode::insert($create_array);
				$latitude 	= number_format(str_replace('+-','-',$create_array['latitude']),2);
				$longitude 	= number_format($create_array['longitude'],2);
				if(!Databucket::isCacheExists('cord:zip:latlan:'.$zipcode)){
					Databucket::isCacheSet('cord:zip:latlan:'.$zipcode, $latitude.'@'.$longitude);
				}
				return array('status'=>'available','zipcode'=>$zipcode);
			}
			return array('status'=>'error','zipcode'=>$zipcode);
		}else{
			return array('status'=>'available','zipcode'=>$zipcode);
		}
	}

	public function cpo_catid_replacer(Request $request){
		$params_make = strtolower(str_replace(" ","_",$request->get('params_make'))) ;
		$params_modelname = strtolower(str_replace(" ","_",$request->get('params_modelname')));
		$params_year = $request->get('params_year');
		$params_vechType = $request->get('params_vechType');
		$return = array();
		$cid =  \Databucket::isCacheHMGet("catsubcat:".$params_vechType.":".$params_make.":".Databucket::customModel($params_modelname).":".$params_year,'cat_id');
		$scid = \Databucket::isCacheHMGet("catsubcat:".$params_vechType.":".$params_make.":".Databucket::customModel($params_modelname).":".$params_year,'subcat_id');
		$return[0] = $cid[0];
		$return[1] = $scid[0];
		return $return;

	}

	//just functions only used by Controller.
    public function getDealersArray($dealersGroup,$zipcode,$make_value,$modelName,$params_year,$params_vechType,$search_in=150)
    {
        $dealer_array = [];
        if(!Databucket::isCacheExists($dealersGroup)){
            $dealersWithMiles = Databucket::expand_radius($zipcode,$search_in, $make_value, $modelName, $params_year, $params_vechType);

            if(gettype($dealersWithMiles['dealers']) == 'array') {
                $dealer_array = array_keys($dealersWithMiles['dealers']);
                $search_in = $dealersWithMiles['radius'];
            }
            //\Log::info('we do not have in cache!! ');
            Databucket::cacheSetValue($dealersGroup,json_encode($dealer_array));
        }else{
            $dealer_array = Databucket::cacheGetValue($dealersGroup);
            //\Log::debug('we have in cache!!  ');
        }

        return $dealer_array;
    }

}
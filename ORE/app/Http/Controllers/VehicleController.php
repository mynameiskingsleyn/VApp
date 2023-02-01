<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;
use App\Facades\OreDSClass;
use App\Mail\FeedbackMail;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Leadsession;
use App\Stage;
use App\Lead;
use App\MoparPlans;
use DB;
use Validator;

class VehicleController extends Controller
{

	private $Stage;
	private $Leadsession;
	private $random_string;
	private $random_character;
	private $permitted_chars;


	public function __construct(Stage $Stage, Leadsession $Leadsession)
	{
		$this->stage = $Stage;
		$this->leadsession = $Leadsession;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('vehicle.layout');
	}

	public function thank_you()
	{
		return view('vehicle.thank_you');
	}

	public function sesionout(Request $request)
	{
		$user['first'] 			= $this->init_form_cache_valid('first');
		$user['last'] 			= $this->init_form_cache_valid('last');
		$user['postalcode'] 	= $this->init_form_cache_valid('postalcode');
		$user['contact_phone'] 	= $this->init_form_cache_valid('contact_phone');
		$user['contact_email'] 	= $this->init_form_cache_valid('contact_email');
		$user['current_tier_value'] = $this->init_form_cache_valid('current_tier_value');
		$user['chk_box_home_delivery'] = $this->init_form_cache_valid('chk_box_home_delivery');
		if (!empty($user['first']) && !empty($user['last']) && !empty($user['contact_email']) && !empty($user['postalcode'])) {
			$saveOreSession = $this->saveOreIdealSession($user);
			$ref = $saveOreSession['session_id'];
			$form_type = 'ideal';
			$saveOreSession['additional_details'] = $form_type;
			$this->stage->create($saveOreSession);
			$output = \Ore::vendor_consolidation($ref, $form_type);
			$lead_status = $output['status'];
			if($lead_status){
				$LeadId = $output['message'][0];
				$sold_type_from_lead = "available";
				$url = \Ore::getSessionID();
				$v_arr = $saveOreSession;
				$dealerid = "";
				if (!\Databucket::isCacheExists($v_arr['vehicle_vin'])) {
					$datass = \App\Vehicle::where(['vin' => $v_arr['vehicle_vin']])->first();
					$val1 = (object) $datass;
					if (\Databucket::hexists($val1->dealer_code, 'dealer_code')) {
						$dealerid = $val1->dealer_code;
					}
				} else {
					$datass = \Ore::isCacheGetAll($v_arr['vehicle_vin']);
					$dealerid = $datass['dealer_code'];
				}
				$dealerarrayresult = \Ore::DealerApi($dealerid);
				$isDealer = '';
				if ($dealerarrayresult['dealerName'] != '') {
					$isDealer = $dealerarrayresult['dealerName'];
					$isDealer =  ucwords($isDealer);
				} 

				// Validation Logic
				if ($lead_status || $lead_status) {
					//Sold Type
					$toEmail = $user['contact_email'];
					$comment['LeadId'] = $LeadId;
					$comment['name'] = $user['first']; //.' '.$form['last'];
					$comment['sold_type'] = $sold_type_from_lead;
					$comment['qrcodemail'] =  config('ore.ishowroom.Qrcode.endpoint') . 'vin=' . $saveOreSession['vehicle_vin'] . '&sid=S99111G&dealerCode=' . $dealerid . '&fname=User 45&lname=at FCA US LLC&launchSource=OSCustomerORE&hideVehcCapContainer=True';
					//	\Log::info($comment['qrcodemail']);
					$comment['qrcode'] = $url;
					$comment['dealerName'] = $isDealer;
					$comment['dealerAddress1'] = $dealerarrayresult['dealerAddress1'];
					$comment['dealerCity'] = $dealerarrayresult['dealerCity'];
					$comment['dealerState'] = $dealerarrayresult['dealerState'];
					$comment['dealerZip'] = $dealerarrayresult['dealerZip'];
					$comment['phoneNumber'] = $dealerarrayresult['phoneNumber'];
					$comment['year'] = $saveOreSession['vehicle_year'];
					$comment['make'] = $saveOreSession['vehicle_make'];
					$comment['model'] = $saveOreSession['vehicle_model'];
					$comment['vin'] = $saveOreSession['vehicle_vin'];
					try {
						\Mail::to($toEmail)->send(new FeedbackMail($comment));
						\Log::info("sesionout:: E-Mail Sending successfully");
					} catch (\Exception $e) {
						\Log::info("sesionout:: E-Mail Sending Issue in VehicleController - sesionout");
						\Log::info($e->getMessage());					
					}
				}
			}
			return $output;
		} else {
			return ['status' => false, 'message' => 'invalid field values', 'description' => ''];
		}
		return 'success';
	}
	//updating current page info
	public function update_pageinfo($curPage)
	{
		$data = json_decode(json_encode(array("curPage" => $curPage)));
		$testCache = \Ore::JsonManager('userinfo', 'PageInfo', 'addJson', $data);
		return 1;
	}

	public function initial_lead(Request $request)
	{

		//if($request->ajax()){
		$url 			= \Ore::getSessionID();
		//	 \Log::info('  == initial_lead ===');
		//	\Log::info($url);

		$first 			= $request->get('first');
		$last 			= $request->get('last');
		$postalcode 	= $request->get('postalcode');
		$contact_phone	= $request->get('contact_phone');
		$contact_email 	= $request->get('contact_email');
		$contact_tier 	= $request->get('tier');
		$init_chk_box_home_delivery = $request->get('chk_box_home_delivery');

		if ($request->has('adobe_session')) {
			$adobe_session 	= $request->get('adobe_session');
			\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'current_adobe_session', $adobe_session);
		} else {
			\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'current_adobe_session', '');
		}


		/* if($first!='' && $last!='' && $postalcode!='' && $contact_email!='' && $contact_phone!=''){
				return response()->json(['status1' => false, 'msg' => 'Fields are empty!']);
			}
return response()->json(['status1' => false, 'msg' => 'Fields are empty reagain!'])	;		 */
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'first', $first);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'last', $last);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'postalcode', $postalcode);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'contact_phone', $contact_phone);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'contact_email', $contact_email);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'current_tier_value', $contact_tier);
		\Databucket::isCacheHMSet('user:experience:popup:status:' . $url, 'chk_box_home_delivery', $init_chk_box_home_delivery);

		$user['first'] = $first;
		$user['last'] = $last;
		$user['contact_email'] = $contact_email;
		$user['contact_phone'] = $contact_phone;
		$user['postalcode'] = $postalcode;
		$user['current_tier_value'] = $contact_tier;
		$user['chk_box_home_delivery'] = $init_chk_box_home_delivery;

		$userExp = $this->saveOreSession($user);

		return response()->json(['status' => true, 'first' => $first, 'last' => $last, 'contact_email' => $contact_email, 'contact_phone' => $contact_phone, 'postalcode' => $postalcode, 'chk_box_home_delivery' => $init_chk_box_home_delivery]);
	}

	/**
	 * Data Component: Vehicle
	 *
	 * @return JsonArray
	 */
	public function vehicle_params(Request $request)
	{
		$tier1 = $request->path();
		$url_make = $passing_param  = 'jeep';
		if($tier1!=''){
			$tier2 = explode('/', $tier1);
			if(count($tier2) > 1){
				$url_make = $tier2[0];
				$tier = $tier2[1];
			}else $tier = 'ore';
		}else $tier= 'ore'; 
		/*
		* Brand Specfic Page Logic
		*/
		if('t1' == $tier || 'ore' == $tier){
			if(!($request->has('vin') || ($request->has('dealercode') || $request->has('dealerCode') || $request->has('DealerCode')))) {
				$make = strtolower($url_make);
		        $request->session()->put('tiers', $tier);
		        //$zipcode = $request->get('zipcode');
				$zipcode = '';        
		        $zipcode = !empty($zipcode) ? str_pad($zipcode, 5, 0, STR_PAD_LEFT) : '';
		        // \Log::info('========= VehicleParam - Brand Specific =========================');
		        // \Log::info('VehicleParam Brand Session Specific:::'. $tier);
		        // \Log::info('VehicleParam Brand Specific:: Zipcode:'. $zipcode);
		        return view('databucket::sni.brand_selection',['params_make' =>$make, 'tier' => $tier,'zipcode' =>$zipcode]); 
			}
		}

        $return_array = [];
		$this->update_pageinfo("vehicleInfo");
		$thumbs = 'https://d1jougtdqdwy1v.cloudfront.net/images/ajax-loader.gif';
		$vehicle_type 	= 'new';
		$dealerCode 	= '';
		$isDealerDelete = '';		
		 
		$url = \Ore::getSessionID();

		if ($url == '')  session()->regenerate();

		$mycount = \App\Leadsession::where(['ore_session' => $url, 'flag' => 8])->count();
		if ($mycount > 0) {
			session()->regenerate();
			$url = \Ore::getSessionID();
		} 
		$param_zipcode = '';
		$vin 			= $request->get('vin');
		if ($request->has('dealercode')) $dealerCode		= $request->get('dealercode');
		if ($request->has('dealerCode')) $dealerCode		= $request->get('dealerCode');
		if ($request->has('DealerCode')) $dealerCode		= $request->get('DealerCode');
		if ($request->has('vehicle_type')) $vehicle_type	= $request->get('vehicle_type');
		if ($request->has('type')) 		$vehicle_type	= $request->get('type');
		if ($request->has('Type')) 		$vehicle_type	= $request->get('Type');
		if ($request->has('zipcode')) 		$param_zipcode	= $request->get('zipcode');
		
		if(!empty($dealerCode)){
			$dealerCode = str_pad($dealerCode, 5, 0, STR_PAD_LEFT);
		}
		if(!empty($param_zipcode)){
			$param_zipcode = str_pad($param_zipcode, 5, 0, STR_PAD_LEFT);
		}

		if (!\Databucket::hexists('user:experience:popup:status:' . $url, 'first')) {
			$return_array['user_experience_popup_status'] = "show";
		} else {
			$return_array['user_experience_popup_status'] = "hide";
		}

		$O_RA = array();

		//Dealer Details API request
		$dealerarrayresult 	= \Ore::DealerApi($dealerCode);
		if (is_null($dealerarrayresult)) {
			$dealerinfocacheky = \Databucket::makeCache('dealerinfo:' . $dealerCode);
			if (!\Databucket::isCacheExists($dealerinfocacheky)) {
				$dealerinfo = \DB::table('fca_ore_dealer_info')->where('dlr_code', $dealerCode)->first();
				\Databucket::isCacheSet($dealerinfocacheky, json_encode($dealerinfo));
			} else {

				$dealerinfo = \Databucket::isCacheGet($dealerinfocacheky);
				$dealerinfo = json_decode($dealerinfo);
			}
			if (!is_null($dealerinfo)) {
				$dealerarrayresult['dealerCode'] = $dealerinfo->dlr_code;
				$dealerarrayresult['dealerName'] = $dealerinfo->dlr_dba_name;
				$dealerarrayresult['dealerAddress1'] = $dealerinfo->dlr_shw_addr1;
				$dealerarrayresult['dealerAddress2'] = $dealerinfo->dlr_shw_addr2;
				$dealerarrayresult['dealerCity'] = $dealerinfo->dlr_shw_city;
				$dealerarrayresult['dealerState'] = $dealerinfo->dlr_shw_state;
				$dealerarrayresult['dealerZip'] = !empty($param_zipcode) ? $param_zipcode : $dealerinfo->dlr_shw_zip;
				$dealerarrayresult['phoneNumber'] = $dealerinfo->dlr_shw_phone;
				$dealerarrayresult['demail'] = $dealerinfo->dlr_email_dlr;
			} else {
				$dealerarrayresult['dealerCode'] = '';
				$dealerarrayresult['dealerName'] = '';
				$dealerarrayresult['dealerAddress1'] = '';
				$dealerarrayresult['dealerAddress2'] = '';
				$dealerarrayresult['dealerCity'] = '';
				$dealerarrayresult['dealerState'] = '';
				$dealerarrayresult['dealerZip'] = '';
				$dealerarrayresult['phoneNumber'] = '';
				$dealerarrayresult['demail'] = '';
			}
		}

		if (
			($tier == 't1' && config('ore.dealereliminate.t1_activate')) ||
			($tier == 't3' && config('ore.dealereliminate.t3_activate')) ||
			($tier == 'ore' && config('ore.dealereliminate.ore_activate'))
		) {
			$isDealerDelete 	= \Databucket::isDealerDelete($dealerCode);
		} else {
			$isDealerDelete 	= "";
		}

		if (!\Databucket::isCacheExists($vin)) {
			$data 		= \App\Vehicle::where(['vin' => $vin])->first();

			//  $valida_year = Arr::get($data, 'year');
			//  $valida_model = Arr::get($data, 'model');
			//  if($valida_year=='' || $valida_model==''){
			//		return url('/').'/'.$make;
			//  }

			//$data 		= null;
			if ($data == null || empty($data)) {
				$arrayresult = \Ore::Vindecoder($vin);
					if ($request->has('zipcode')) $p_zipcode = $request->get('zipcode');
					else $p_zipcode = '';
					if($p_zipcode=='' && $dealerarrayresult['dealerZip']!='') $p_zipcode = substr($dealerarrayresult['dealerZip'], 0, 5);
					if($p_zipcode=='') $p_zipcode = '';
					if($p_zipcode) $p_zipcode = str_pad($p_zipcode, 5, 0, STR_PAD_LEFT);
				
				// validate VIN in our Inventory and API
				if (Arr::get($arrayresult, 'status')) {
					$p_year  = Arr::has($arrayresult, 'data.year') ? Arr::get($arrayresult, 'data.year') : 2019;
					$p_make  = Arr::has($arrayresult, 'data.make') ? Arr::get($arrayresult, 'data.make') : 'JEEP';
					$p_model = Arr::has($arrayresult, 'data.model') ? Arr::get($arrayresult, 'data.model') : 'COMPASS';
					 $passing_param  = 	$p_make;

					if ($vehicle_type != '') $p_vehicle_type = $vehicle_type;
					else  $p_vehicle_type = 'new';
					if ($vin != '') $p_vin = $vin;
					else  $p_vin = false;
					if ($dealerCode != '') $p_dealercode = $dealerCode;
					else  $p_dealercode = '';
					if ($request->has('color')) $p_color = $request->get('color');
					else  $p_color = '';
					if ($request->has('Drivetype')) $p_Drivetype = $request->get('Drivetype');
					else  $p_Drivetype = '';
					if ($request->has('Trim')) $p_Trim = $request->get('Trim');
					else   $p_Trim = '';

					//if (strtolower($p_make) == 'alfa') $p_make = 'alfaromeo'; else $p_make = strtolower($p_make);

						
					if ($isDealerDelete == 'block') {
						$redirect = url('/') .'/'.$p_make;
						$redirect = ($tier == 't1') ? $redirect . '/t1'  : $redirect;
						// $redirect = url('/') . '/'.$p_make.'/' . $p_year . '/' . $p_model . '?vehicle_type=new&';
						// $redirect .= 'color=' . $p_color . '&drivetype=' . $p_Drivetype;
						// $redirect .= '&trim=' . $p_Trim . '&radius=25&engine=&transmission=';
					} else {
						//$dealerZip = substr($dealerarrayresult['dealerZip'], 0, 5);

						if ($tier == 'ore' || $tier == 't1' || $tier == 't3') {
							$tier = $tier;
						} else $tier = 'ore';


						$redirect = url('/') . '/'.$p_make. '/' . $tier .'/' . $p_year . '/' . $p_model . '?vehicle_type=new&';
						$redirect .= 'vin=' . $p_vin . '&dealercode=' . $p_dealercode . '&color=' . $p_color . '&drivetype=' . $p_Drivetype;

						if ($tier == 'ore' || $tier == 't1') {
							$redirect .= '&trim=' . $p_Trim . '&radius=25&engine=&transmission=&zipcode=' . $p_zipcode;
						} else {
							$redirect .= '&trim=' . $p_Trim . '&radius=25&engine=&transmission=&zipcode=' . $p_zipcode;
						}
					//	$redirect = url('/') .'/'.$p_make;

					}

					if ($tier == 'ore' || $tier == 't1') {
						return view('vehicle.section.redirect_popup', ['redirect' => $redirect, 'params_zipcode' => $p_zipcode]);
					} else {
						return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect, 'params_zipcode' => $p_zipcode]);
					}
				} else {
					$http_referer = ($tier == 't1') ? $tier : 'ore';
					$request->session()->put('tiers', $http_referer); 
					$redirect = url('/'). '/'. $url_make;
					$redirect = ($tier == 't1') ? $redirect . '/t1'  : $redirect;
					return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect, 'params_zipcode' => $p_zipcode]);
				}
			}

			$val1 = (object) $data;

			if (!\Databucket::hexists($val1->vin, 'vin')) {
				$redirect_dealerURL = url('/'). '/'. $url_make;
				$redirect_dealerURL = ($tier == 't1') ? $redirect_dealerURL . '/t1'  : $redirect_dealerURL;
				$dealerinfo = \DB::table('fca_ore_dealer_info')->where('dlr_code', $dealerCode)->count();
				
				if(0 == $dealerinfo){
					return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect_dealerURL, 'params_zipcode' => '']);
				}else{
					$vindealerCount 		= \App\Vehicle::where(['vin' => $vin,'dealer_code' => $dealerCode])->count();
					if(0 == $vindealerCount){
						return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect_dealerURL, 'params_zipcode' => '']);
					}
				}

				$O_RA = $option_desc_raw = $val1->option_desc_raw;
				$option_code = $val1->option_code;
				$return_array['opt'] = explode("|", $O_RA);

				$arr_option_desc_raw = explode("|", $option_desc_raw);
				$arr_option_code = explode("|", $option_code);
				if (count($arr_option_code) == count($arr_option_desc_raw)) {
					$array_codes = array_combine($arr_option_code, $arr_option_desc_raw);
				} else {
					$array_codes = [];
				}

				if (array_key_exists($val1->exterior_color_code, $array_codes)) {
					\Databucket::isCacheHMSet($vin, 'ext_color_raw', $array_codes[$val1->exterior_color_code]);
				} else {
					\Databucket::isCacheHMSet($vin, 'ext_color_raw', '');
				}

				/* if ($val1->photo_URL!=''){
						$cpo_img = explode("|",$val1->photo_URL);

						if(count($cpo_img) > 0){
							$thumbs = $cpo_img[0];
						}
					}	 */
				 $passing_param  = 	$val1->make;
				\Databucket::isCacheHMSet($vin, 'vin', $vin);
				\Databucket::isCacheHMSet($vin, 'vehType', $val1->vehtype);
				// N - New, U-Used, C-CPO
				\Databucket::isCacheHMSet($vin, 'vehicle_type', $val1->vehicle_type);
				// N - New, U-Used, C-CPO
				\Databucket::isCacheHMSet($vin, 'year', $val1->year);
				\Databucket::isCacheHMSet($vin, 'make', $val1->make);
				\Databucket::isCacheHMSet($vin, 'model', $val1->model);

				\Databucket::isCacheHMSet($vin, 'trim_desc', $val1->trim_desc);
				\Databucket::isCacheHMSet($vin, 'trim_code', $val1->trim_code);

				\Databucket::isCacheHMSet($vin, 'option_desc_raw', $option_desc_raw);
				\Databucket::isCacheHMSet($vin, 'option_code', $option_code);

				\Databucket::isCacheHMSet($vin, 'exterior_color_desc', $val1->exterior_color_desc);
				\Databucket::isCacheHMSet($vin, 'exterior_color_code', $val1->exterior_color_code);
				\Databucket::isCacheHMSet($vin, 'drive_type', $val1->drive_type);
				\Databucket::isCacheHMSet($vin, 'towing_capacity', $val1->towing_capacity);
				\Databucket::isCacheHMSet($vin, 'transmission_desc', $val1->transmission_desc);
				\Databucket::isCacheHMSet($vin, 'transmission_type', $val1->transmission_type);

				\Databucket::isCacheHMSet($vin, 'city_mpg', $val1->city_mpg);
				\Databucket::isCacheHMSet($vin, 'hwy_mpg', $val1->hwy_mpg);
				\Databucket::isCacheHMSet($vin, 'interior_fabric', $val1->interior_fabric);
				\Databucket::isCacheHMSet($vin, 'internetPrice', $val1->internetPrice);
				\Databucket::isCacheHMSet($vin, 'msrp', $val1->msrp);
				\Databucket::isCacheHMSet($vin, 'photo_URL', '');
				\Databucket::isCacheHMSet($vin, 'dealer_code', $val1->dealer_code);

				\Databucket::isCacheHMSet($vin, 'engine_horse_power', $val1->engine_horse_power);
				\Databucket::isCacheHMSet($vin, 'eng_desc', $val1->eng_desc);
				\Databucket::isCacheHMSet($vin, 'wheel_base', $val1->wheel_base);
				\Databucket::isCacheHMSet($vin, 'engine_displacement', $val1->engine_displacement);
				\Databucket::isCacheHMSet($vin, 'seating_capacity', $val1->seating_capacity);

				\Databucket::isCacheHMSet($vin, 'upper_level_pkg_cd', $val1->upper_level_pkg_cd);
				\Databucket::isCacheHMSet($vin, 'body_style', $val1->body_style);
			}
		} else {
			$data = \Ore::isCacheGetAll($vin);
			$O_RA = \Databucket::isCacheHMGet($data['vin'], 'option_desc_raw');
			$O_RA1 = \Databucket::isCacheHMGet($data['vin'], 'make'); 
			if(Arr::has($O_RA1, '0')) $passing_param = $O_RA1[0]; else 	$passing_param = $url_make; 

			$return_array['opt'] = explode("|", $O_RA[0]);
			$redirect_vinmgt = url('/'). '/'. $passing_param;
			$redirect_vinmgt = ($tier == 't1') ? $redirect_vinmgt . '/t1'  : $redirect_vinmgt;
			$O_DC = \Databucket::isCacheHMGet($data['vin'], 'dealer_code');

			try {
				if ($O_DC[0] != $dealerCode) {
					return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect_vinmgt, 'params_zipcode' => '']);
				}
			} catch (\Exception $e) {
				\Log::info('Message: ' . $e->getMessage());
			}
		}

		/*************** VIN MANAGEMENT BY DEALER ADMIN **********/
		 
		
		if (config('ore.vinmanagement.activate')) {
			$vinMgtAction = \Databucket::SqlVinActivate($dealerCode, $vin);
			$dealerZip = substr($dealerarrayresult['dealerZip'], 0, 5);
			$modelass = strtolower(str_replace(' ','-',$data['model']));
			$modelass = strtolower(str_replace('_','-',$modelass));
			
			$passing_param = strtolower($passing_param);

			$redirect = url('/') . '/' . $passing_param . '/'. $tier .'/'. $data['year'] . '/' . $modelass . '?vehicle_type=new&';
			$redirect .= 'vin=' . $vin . '&dealercode=' . $dealerCode . '&color=&drivetype=';
			$redirect .= '&trim=&radius=25&engine=&transmission=&zipcode=' . $dealerZip;
			 	
			if ($tier == 'ore' || $tier == 't1') {
				if ($vinMgtAction == 'deactive') return view('vehicle.section.redirect_popup', ['redirect' => $redirect, 'params_zipcode' =>  $dealerZip]);
			} else {
				if ($vinMgtAction == 'deactive') return view('vehicle.section.redirect_popup_t3', ['redirect' => $redirect, 'params_zipcode' =>  $dealerZip]);
			}
		}
		/*************** VIN MANAGEMENT BY DEALER ADMIN **********/

		/* initial Popup Value */
		$return_array['initial_form_first'] = $this->init_form_cache_valid('first');
		$return_array['initial_form_last'] = $this->init_form_cache_valid('last');
		$return_array['initial_form_postalcode'] = $this->init_form_cache_valid('postalcode');
		$return_array['initial_form_contact_phone'] = $this->init_form_cache_valid('contact_phone');
		$return_array['initial_form_contact_email'] = $this->init_form_cache_valid('contact_email');
		$return_array['initial_form_home_delivery'] = $this->init_form_cache_valid('chk_box_home_delivery');



		$params_make = strtolower($data['make']);
		$params_vechType = strtolower($data['vehicle_type']);
		$params_year = strtolower($data['year']);
		$params_model = strtolower($data['model']);
		$drive_type = strtolower($data['drive_type']);


		if ($isDealerDelete == 'block') {
			$redirect = url('/') . '/'. $url_make .'/ore/' . $params_year . '/' . $params_model . '?vehicle_type=' . $params_vechType . '&color=&drivetype=&trim=&radius=25&engine=&transmission=';

			return view('vehicle.section.redirect_popup', ['redirect' => $redirect,'params_zipcode' => '']);
		}

		\Databucket::isCacheHMSet('user:experience:sessionid:' . $url, 'vehicle_info', json_encode($data));

		$return_array['photo_URL'] = ''; //$data['photo_URL'];

		$outCache = \Ore::JsonManager('userinfo', 'VehicleInfo', 'addJson', json_encode($data));

		$outCacheMerge = $this->merge($outCache);



		$mainarray = json_decode($outCacheMerge, true);
		$pre_vehicle = json_decode($mainarray['userinfo']['VehicleInfo'], true);
		$shift_vehicle = ($pre_vehicle);
		$DealerInfoArray = $mainarray['userinfo']['dealerInfo'];



		$dealerName = $dealerAddress = $dealerZip = $dlink = '';


		if ($dealerarrayresult['dealerName']) {
			$dealerName = $dealerarrayresult['dealerName'];
		}

		$dealerAddress .= $dealerarrayresult['dealerAddress1'] . ', ' ?? '';
		//$dealerAddress .= $dealerarrayresult['dealerAddress2'].', ' ?? '';
		$dealerAddress .= $dealerarrayresult['dealerCity'] . ', ' ?? '';
		$dealerAddress .= $dealerarrayresult['dealerState'] . ' ' ?? '';
		$dealerZip = substr($dealerarrayresult['dealerZip'], 0, 5) ?? '';

		$return_array['dealerName'] = $dealerarrayresult['dealerName'];
		$return_array['dealerAddress1'] = $dealerarrayresult['dealerAddress1'];
		$return_array['dealerAddress2'] = $dealerarrayresult['dealerAddress2'];
		$return_array['dealerCity'] = $dealerarrayresult['dealerCity'];
		$return_array['dealerState'] = $dealerarrayresult['dealerState'];
		$return_array['dealerZip'] = substr($dealerarrayresult['dealerZip'], 0, 5) ?? '';
		$return_array['phoneNumber'] = $dealerarrayresult['phoneNumber'];
		$return_array['demail'] = $dealerarrayresult['demail'];
		$return_array['dealerCode'] = $dealerarrayresult['dealerCode'];
		$return_array['vin'] = $data['vin'];


		if ($dealerName != '') {
			$dlink = '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query=' . $dealerAddress . $dealerZip . '" class="header-gmap" data-gmapaddress="' . $dealerAddress . $dealerZip . '">' . $dealerAddress . $dealerZip . '</a>';

			// //$isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealerName));
			// //$isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
			// $isDealer =  Str::replaceFirst('alfa romeo', '', strtolower($dealerName));
			// $isDealer =  Str::replaceFirst('romeo-', 'romeo', strtolower($isDealer));

			$isDealer = $dealerName;
			$isDealer =  ucwords($isDealer);

			$return_array['header'] = "<h3>" . $isDealer . "</h3><p class='themeClr2 vehicle_para'><i class='fa fa-map-marker themeClr2' aria-hidden='true'></i>  " . $dlink . "</p>";
		} else {
			$dlink = '';
			$isDealer = '';
			$return_array['header'] = '';
		}


		$return_array['msrp'] = $shift_vehicle['msrp'];
		$return_array['title'] = $shift_vehicle['year'] . ' ' . $shift_vehicle['trim_desc'];


		$return_array['trim_code'] = $shift_vehicle['trim_code'];
		$return_array['trim_code'] = $shift_vehicle['trim_code'];

		/*if($shift_vehicle['ext_color_raw']==''){
				if(\Databucket::hexists($data['exterior_color_code'], 'options_desc')){
					 	$ra_ext = \Databucket::isCacheHMGet($data['exterior_color_code'], 'options_desc');
						$shift_vehicle['ext_color_raw'] = $ra_ext[0];
				  }
		}*/

		$ext_color_raw = \Databucket::isCacheHMGet($vin, 'ext_color_raw');
		$shift_vehicle['ext_color_raw']  = Arr::has($ext_color_raw, 0) ? Arr::get($ext_color_raw, 0) : "";

		$return_array['vehicle'] = view('vehicle.render.vehicle_params', ['vehicle' => $shift_vehicle])->render();
		$return_array['vehicle_params'] = $shift_vehicle;

		$return_array['tier'] = 1;

		$return_array['params_makeName'] = $params_make;
		$return_array['params_make'] = strtolower(str_replace(' ', '_', $params_make));
		$return_array['upper_level_pkg_cd'] = $shift_vehicle['upper_level_pkg_cd'];
		$return_array['exterior_color_code'] = $shift_vehicle['exterior_color_desc'];

		$return_array['params_model'] = $params_model;
		$return_array['drive_type'] = $drive_type;
		$return_array['params_year'] = $params_year;
		$return_array['params_vechType'] = $params_vechType;
		$return_array['dlr_code'] = $DealerInfoArray['dlr_code'];

		if ($dealerName != '') {
			// //$isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealerName));
			// //$isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
			// //$isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
			// $isDealer =  Str::replaceFirst('alfa romeo', '', strtolower($dealerName));
			// $isDealer =  Str::replaceFirst('romeo-', 'romeo', strtolower($isDealer));

			$isDealer = $dealerName;
			$isDealer =  ucwords($isDealer);
		} else $isDealer = '';

		$return_array['dlr_dba_name'] = $isDealer;

		$return_array['mdoca'] = \Databucket::mdoca_availability($return_array['dlr_code']);




		//QR Code

		$return_array['qrcode'] = $url;



		//Routeone
		$current_session = $url;
		$cSession = md5('userinfo' . $current_session);
		$json = \Ore::cacheGet($cSession);
		$JsonToArray   = json_decode($json, true);


		$rone_referid = \Ore::referenceIDGenerator('Routeone', $current_session);

		$mySession = Leadsession::updateOrCreate(
			['ore_session' => $current_session],
			['routeone_refid' => \Ore::referenceIDGenerator('Routeone', uniqid()), 'source' => $tier]
		);

		$rone_referid = $mySession->routeone_refid;

		$route_validate = \Databucket::routeone_randnumber_query();

		if ($route_validate == false) {
			$TradeIn = array('year' => '', 'make' => '', 'model' => '', 'style' => '', 'mileage' => '', 'zip' => '', 'condition' => '', 'price' => '', 'remainingvalue' => '');
			try {
				if (Arr::has($JsonToArray['userinfo'], 'TradeIn')) {
					if (Arr::has($JsonToArray['userinfo']['TradeIn'], 'make')) {
						$TradeIn = $JsonToArray['userinfo']['TradeIn'];
					}
				}
			} catch (\Exception $e) {
				\Log::info('Message: ' . $e->getMessage());
			}


			/*if ($shift_vehicle['make'] == 'DODGE') $rteOneDmsId = config('ore.routeone.dodge_rteOneDmsId');
			else  if ($shift_vehicle['make'] == 'FIAT') $rteOneDmsId = config('ore.routeone.fiat_rteOneDmsId');
			else  if ($shift_vehicle['make'] == 'RAM') $rteOneDmsId = config('ore.routeone.ram_rteOneDmsId');
			else  if ($shift_vehicle['make'] == 'CHRYSLER') $rteOneDmsId = config('ore.routeone.chrysler_rteOneDmsId');
			else  if ($shift_vehicle['make'] == 'ALFA ROMEO') $rteOneDmsId = config('ore.routeone.alfaromeo_rteOneDmsId');
			else {
				$rteOneDmsId = config('ore.routeone.default_rteOneDmsId');
			}*/
			$rteOneDmsId = config('ore.routeone.generic_rteOneDmsId');


			if (config('ore.routeone.standard_dealerId') == 'YES') {
				// if ($shift_vehicle['make'] == 'DODGE') $dealerId = config('ore.routeone.dodge_dealerId');
				// else  if ($shift_vehicle['make'] == 'FIAT') $dealerId = config('ore.routeone.fiat_dealerId');
				// else  if ($shift_vehicle['make'] == 'RAM') $dealerId = config('ore.routeone.ram_dealerId');
				// else  if ($shift_vehicle['make'] == 'CHRYSLER') $dealerId = config('ore.routeone.chrysler_dealerId');
				// else  if ($shift_vehicle['make'] == 'ALFA ROMEO') $dealerId = config('ore.routeone.alfaromeo_dealerId');
				// else {
				// 	$dealerId = config('ore.routeone.default_dealerId');
				// }
				$dealerId = config('ore.routeone.default_dealerId');
			} else {
				$dealerId = $dealerCode;
			}

			$rone_link  = config('ore.routeone.endpoint');
			$rone_link .= 'rteOneDmsId=' . $rteOneDmsId;
			$rone_link .= '&fncSrcId=F00CHC';
			$rone_link .= '&dealerDmsId=' . $dealerId;
			$rone_link .= '&vehicleYear=' . $shift_vehicle['year'];
			$rone_link .= '&vehicleMake=' . $shift_vehicle['make'];
			$rone_link .= '&vehicleModel=' . $shift_vehicle['model'];
			$rone_link .= '&contractTerms_msrp=' . $shift_vehicle['msrp'];
			$rone_link .= '&vehicle_vin=' . $shift_vehicle['vin'];
			$rone_link .= '&year=' . $TradeIn['year'];
			$rone_link .= '&make=' . $TradeIn['make'];
			$rone_link .= '&model=' . $TradeIn['model'];
			$rone_link .= '&style=' . $TradeIn['style'];
			$rone_link .= '&price=' . $TradeIn['price'];
			$rone_link .= '&buyOrLease=1';
			$rone_link .= '&dealership_name=' . $isDealer;
			$rone_link .= '&dealership_address=' . $dealerarrayresult['dealerAddress1'];
			$rone_link .= '&dealership_city=' . $dealerarrayresult['dealerCity'];
			$rone_link .= '&dealership_state=' . $dealerarrayresult['dealerState'];
			$rone_link .= '&dealership_zip=' . substr($dealerarrayresult['dealerZip'], 0, 5) ?? '';
			$rone_link .= '&dealership_phone=' . $dealerarrayresult['phoneNumber'];
			$rone_link .= '&dealership_email=' . $dealerarrayresult['demail'];
			$rone_link  .= '&referenceId=' . $rone_referid . '';

			$return_array['rone_link'] = $rone_link;
			$return_array['route_validate'] = false;
		} else {
			$return_array['route_validate'] = true;
		}

		//\Log::info($rone_link);
		switch ($return_array['params_make']) {
			case 'chrysler':
				$return_array['make_code'] = 'C';
				$return_array['make_url'] = 'chrysler';
				break;
			case 'dodge':
				$return_array['make_code'] = 'D';
				$return_array['make_url'] = 'dodge';
				break;
			case 'fiat':
				$return_array['make_code'] = 'X';
				$return_array['make_url'] = 'fiatusa';
				break;
			case 'jeep':
				$return_array['make_code'] = 'J';
				$return_array['make_url'] = 'jeep';
				break;
			case 'ram':
				$return_array['make_code'] = 'R';
				$return_array['make_url'] = 'ramtrucks';
				break;
			case 'alfa_romeo':
				$return_array['make_code'] = 'Y';
				$return_array['make_url'] = 'alfaromeousa';
				break;
		}

		//Auto Popup review and submit form
		$return_array['stages'] = \Databucket::autopopulation_query();
		if (!$return_array['stages']) {
			$return_array['stages'][0] = (object) ['first_name' => '', 'last_name' => '', 'email' => '', 'phone' => '', 'streetline1' => '', 'city' => '', 'state' => '', 'zip' => ''];
		}
		$return_array['tier'] = $tier;

		/********** BUTTON ACTIVE ***************/
		$return_array['string_lead_status_button'] = 'fail';

		if (\Databucket::hexists('user:experience:sessionid:' . \Ore::getSessionID(), 'lead_status')) {
			$string_lead_status = \Databucket::isCacheHMGet('user:experience:sessionid:' . \Ore::getSessionID(), 'lead_status');
			if (count($string_lead_status) > 0) {
				if ($string_lead_status[0] == 'success') {
					$return_array['string_lead_status_button'] = 'success';
				}
			}
		}
		// add tradein information if available and vin is the same as stored vin if any stored......
        $cSession = md5('userinfo' . \Ore::getSessionID());
        $tradeInfo = false;
        $json = \Ore::cacheGet($cSession);
        if(!empty($json)){
            $JsonToArray   = json_decode($json, true);
            $servicepreloop = \Ore::serviceArraychecker($JsonToArray, 'Service');
            
			if(Arr::has($servicepreloop, 'vin')){
				if ($servicepreloop['vin'] == $request->get('vin')){ 
						// if vehicle is switched you have to reenter Tradein information
						$tradeInfo = $TradeIn ?? false;
						if($tradeInfo){
							$find = ['$',',']; $replace = ['',''];
							$tradeInfo['price'] = (int)str_replace($find,$replace,$tradeInfo['price']);
						}
				}
			}
        }

        //.............................................................

		$serviceprotection = $this->serviceprotection($return_array['params_make'], $request->vin);
		return view('vehicle.layout', ['return_array' => $return_array, 'params_make' => $return_array['params_make'], 'serviceprotection' => $serviceprotection,'tradeInfo'=>$tradeInfo]);
	}

	public function serviceprotection($brand, $vin)
	{

		/* finding already check service*/
		$checklease = [];
		$checkfinance = [];
		$cSession = md5('userinfo' . \Ore::getSessionID());

		$json = \Ore::cacheGet($cSession);

		$JsonToArray   = json_decode($json, true);

		if (Arr::has($JsonToArray, 'userinfo')) {
			if (Arr::has($JsonToArray['userinfo'], 'Service')) {

				//dd(dd($JsonToArray['userinfo']['Service']));

				if (!is_array($JsonToArray['userinfo']['Service']) && $JsonToArray['userinfo']['Service'] != '') {

					$service = json_decode($JsonToArray['userinfo']['Service']);
					if ($service->vin == $vin) {
						$checklease = $service->lease;
						$checkfinance = $service->finance;
					}
				}
			}
		}
		/* finding already check service*/
		// dd($checklease,$checkfinance);

		$serviceprotectionCachkey = \Databucket::makeCache('serviceprotection:' . $brand);
        if (!\Databucket::isCacheExists($serviceprotectionCachkey)) {
            $qry = MoparPlans::where('varient', 'plan')->orderBy('l_order','ASC')->get();
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
                if(property_exists($value, 'f_order')) $finance[$value->f_order] = $value;
                //$finance[$value->f_order] = $value;
                
            }
            if ($value->lease == 1) {
                $lease[] = $value;
            }
        }
        ksort($finance);
		$headerCachkey = \Databucket::makeCache('serviceprotection_header:' . $brand);
		if (!\Databucket::isCacheExists($headerCachkey)) {
			$header = MoparPlans::where('varient', 'header')->where('Applicable_brands', $brand)->first();
			\Databucket::isCacheSet($headerCachkey, json_encode($header));
		} else {
			$header = \Databucket::isCacheGet($headerCachkey);
			$header = json_decode($header);
		}

		return array('finance' => $finance, 'lease' => $lease, 'header' => $header, 'checklease' => $checklease, 'checkfinance' => $checkfinance);
	}



	public function merge($outCache)
	{
		$array = json_decode($outCache, true);
		$array['userinfo']['dealerInfo'] = [];

		if (array_key_exists('VehicleInfo', $array['userinfo'])) {
			$VehicleInfoArray = json_decode($array['userinfo']['VehicleInfo'], true);
			//  $VehicleInfo =  array_shift($VehicleInfoArray);

			$dealerCode =  $VehicleInfoArray['dealer_code'];
			if ($dealerCode != "") {
				$array['userinfo']['dealerInfo'] = \Databucket::dealerInfoByDealerCode($dealerCode);
			}
		}
		return json_encode($array);
	}

	/* captcha  */

	public function generate_string($input, $strength = 10)
	{
		$input_length = strlen($input);
		$random_string = '';
		for ($i = 0; $i < $strength; $i++) {
			$random_character = $input[mt_rand(0, $input_length - 1)];
			$random_string .= $random_character;
		}
		return $random_string;
	}

	public function image_color($type)
	{
		$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$image = imagecreatetruecolor(200, 50);
		imageantialias($image, true);

		$colors = [];

		$red = rand(125, 175);
		$green = rand(125, 175);
		$blue = rand(125, 175);

		for ($i = 0; $i < 5; $i++) {
			$colors[] = imagecolorallocate($image, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
		}

		imagefill($image, 0, 0, $colors[0]);

		for ($i = 0; $i < 10; $i++) {
			imagesetthickness($image, rand(2, 10));
			$line_color = $colors[rand(1, 4)];
			imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $line_color);
		}

		$black = imagecolorallocate($image, 0, 0, 0);
		$white = imagecolorallocate($image, 255, 255, 255);
		$textcolors = [$black, $white];
		$fonts = public_path('fonts/roboto-regular-webfont.woff');
		$string_length = 6;
		$captcha_string = $this->generate_string($permitted_chars, $string_length);

		session(['captcha_text' => $captcha_string]);
		$captcha_text = $captcha_string;;

		for ($i = 0; $i < $string_length; $i++) {
			$letter_space = 170 / $string_length;
			$initial = 15;

			imagettftext($image, 24, rand(-15, 15), $initial + $i * $letter_space, rand(25, 45), $textcolors[rand(0, 1)], $fonts, $captcha_string[$i]);
		}
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
	}


	public function mylead(Request $request)
	{

		$cSession = md5('userinfo' . \Ore::getSessionID());

		$json = \Ore::cacheGet($cSession);
		$JsonToArray   = json_decode($json, true);
		//dd($JsonToArray);
		$form = $request->all();

		//return ['status' =>"success", 'message' =>'test', 'sold_type' => ''];

		// Validation
		// $rules = ['captcha_text' => 'required|captcha_text', 'first' => 'required|min:2|Regex:/^[\D]+$/i|max:100', 'last' => 'required|Regex:/^[\D]+$/i|max:100','postalcode' => 'required','contact_phone' => 'required','contact_email' => 'required|email'];
		// $validator = Validator::make($form, $rules);
		//$error = '';
		// if ($validator->fails())  {
		//	return ['status' =>"failed", 'message' =>'Invalid Captcha...'];
		//}

		// $bbooleanValue = \Databucket::zipValidation($form['postalcode']);
		//  if (!$bbooleanValue) {
		//  	$bbooleanValue2 = $this->getState($form['postalcode']);
			
		//  	if($bbooleanValue2 == 'none'){
		//  		return ['status' => "failed", 'message' => 'Invalid US postalcode.'];
		//  	}
		//   } 

		// else { 

			$form_type = 'uexp';
			if ($form['current_submit_type'] == 'explore-finance-options') {
				$form_type = 'rone';
			}

			$currentUserSessionTableID = \Ore::getSessionID();
			$saveOreSession = $this->saveOreSession($form);
			$output = \Ore::vendor_information_insert('user_experience', $saveOreSession, $form_type);
			$lead_status = $output['status'];

			$return_msg  = '';
			$LeadId = $output['message'][0];
			$sold_type_from_lead = "available";
			$url = \Ore::getSessionID();
			$return_array['qrcode'] = $url;
			$return_array['vin'] = $saveOreSession['vehicle_vin'];
			$v_arr = $saveOreSession;


			$dealerid = "";
			if (!\Databucket::isCacheExists($v_arr['vehicle_vin'])) {
				$datass = \App\Vehicle::where(['vin' => $v_arr['vehicle_vin']])->first();
				$val1 = (object) $datass;
				if (\Databucket::hexists($val1->dealer_code, 'dealer_code')) {
					$dealerid = $val1->dealer_code;
				}
			} else {
				$datass = \Ore::isCacheGetAll($v_arr['vehicle_vin']);
				$dealerid = $datass['dealer_code'];
			}
			$dealerarrayresult = \Ore::DealerApi($dealerid);

			if ($dealerarrayresult['dealerName'] != '') { 

			$isDealer = $dealerarrayresult['dealerName'];
			$isDealer =  ucwords($isDealer);
			} else $isDealer = '';

			$return_array['dealerName'] = $isDealer;
			// Validation Logic
			if ($lead_status || $lead_status) {
				//Sold Type
				$toEmail = $form['contact_email'];
				$comment['LeadId'] = $LeadId;
				$comment['name'] = $form['first']; //.' '.$form['last'];
				$comment['sold_type'] = $sold_type_from_lead;
				$comment['qrcodemail'] =  config('ore.ishowroom.Qrcode.endpoint') . 'vin=' . $saveOreSession['vehicle_vin'] . '&sid=S99111G&dealerCode=' . $dealerid . '&fname=User 45&lname=at FCA US LLC&launchSource=OSCustomerORE&hideVehcCapContainer=True';
				//	\Log::info($comment['qrcodemail']);
				$comment['qrcode'] = $url;
				$comment['dealerName'] = $isDealer;
				$comment['dealerAddress1'] = $dealerarrayresult['dealerAddress1'];
				$comment['dealerCity'] = $dealerarrayresult['dealerCity'];
				$comment['dealerState'] = $dealerarrayresult['dealerState'];
				$comment['dealerZip'] = $dealerarrayresult['dealerZip'];
				$comment['phoneNumber'] = $dealerarrayresult['phoneNumber'];
				$comment['year'] = $saveOreSession['vehicle_year'];
				$comment['make'] = $saveOreSession['vehicle_make'];
				$comment['model'] = $saveOreSession['vehicle_model'];
				$comment['vin'] = $saveOreSession['vehicle_vin'];
				$form['lead_status'] = 'success';

				$this->saveOreSession($form, $LeadId, $currentUserSessionTableID);

				try {
					\Databucket::isCacheHMSet('user:experience:sessionid:' . \Ore::getSessionID(), 'lead_status', 'success');
					Mail::to($toEmail)->send(new FeedbackMail($comment)); 
					//\Databucket::cacheHMDel('user:experience:sessionid:' . \Ore::getSessionID(), 'lead_status');
				} catch (\Exception $e) {
					\Log::info("E-Mail Sending Issue in VehicleController - mylead");
					\Log::info($e->getMessage());
					return ['status' => "success", 'message' => 'Lead has been received successfully. Confirmation e-mail have following issue: ' . $e->getMessage(), 'sold_type' => $sold_type_from_lead];
					exit;
				}

				\Log::info("regenerate start");
				//Regenerate New Session
				session()->regenerate();
                /*Remove first name from cached key just incase same session is generated twice
                 Highly unlikely but not impossible so we delete this session from cache */ 
                \Databucket::cacheHMDel('user:experience:popup:status:' . $url);
				return ['status' => "success", 'message' => $LeadId, 'sold_type' => $sold_type_from_lead];
				exit;
			} else {
				$return_desc  = $output['description'];
				$msd = Arr::has($output, 'description.0');
				if ($return_desc == 'Lead considered to not be a serious buyer') {
					return ['status' => "failed", 'message' => 'Lead considered to not be a serious buyer <br/><br/>
						<div style="text-align:left;">
						<b>HINT:</b>  Kindly check the following and try again<br/>
							1. First Name <br/>
							2. Last Name <br/>
							3. Address</div>'];
				} else if ($return_desc == 'Invalid Vehicle Model') {
					return ['status' => "failed", 'message' => 'Invalid Vehicle Model'];
				} else {
					return ['status' => "failed", 'message' => 'Unknown issue occurred. Please try again later.'];
				}
		//	}
		}
	}


	/**
	 * Review and Submit
	 */
	public function review(Request $request)
	{

		$cSession = md5('userinfo' . \Ore::getSessionID());

		$json = \Ore::cacheGet($cSession);

		$JsonToArray   = json_decode($json, true);

		$flag['Trade'] = $flag['Service'] = $flag['Lease'] = $flag['finance'] = false;
		$Service_lease = $Service_finance = $tradeloop = $servicepreloop = array();


		try {
			$tradeloop = \Ore::serviceArraychecker($JsonToArray, 'TradeIn');



			if ($tradeloop != null) {
				if (count($tradeloop) > 0) {
					$flag['Trade'] = true;
				}
			}
			$servicepreloop['lease'] = $servicepreloop['finance'] = [];
			$servicepreloop = \Ore::serviceArraychecker($JsonToArray, 'Service');
			//dd($servicepreloop['vin']);
			if ($servicepreloop != null) {
				if (count($servicepreloop) > 0) {
					$flag['Service'] = true;
					// dd($servicepreloop);
					if (count($servicepreloop['lease']) > 0) {
						$flag['Lease'] = true;
					}
					if (count($servicepreloop['finance']) > 0) {
						$flag['finance'] = true;
					}

					$serviceArray = \Ore::serviceCompare($servicepreloop['lease'], $servicepreloop['finance'], $cSession);
					if ($servicepreloop['vin'] == $request->vin) {
						if (Arr::has($serviceArray, 'lease'))  $Service_lease =  $serviceArray['lease'];
						if (Arr::has($serviceArray, 'finance'))  $Service_finance =  $serviceArray['finance'];
					} else {
						$flag['Trade'] = $flag['Service'] = $flag['Lease'] = $flag['finance'] = false;
						//$flag['Service'] = $flag['Lease'] = $flag['finance'] = false;
					}


					//if(array_key_exists("lease", $serviceArray))  $Service_lease =  $serviceArray['lease'] ;
					//if(array_key_exists("finance", $serviceArray)) $Service_finance =  $serviceArray['finance'] ;
				}
			}

			$vehicle = \Ore::serviceArraychecker($JsonToArray, 'VehicleInfo');



			$optionsValues = array();

			if ($vehicle == null) $vehicle = [];

			$tomorrow = Carbon::now('America/Vancouver')->addDays(10)->format('F d, Y');

			if (Arr::has($vehicle, 'dealer_code')) {
				// if(array_key_exists('dealer_code', $vehicle)){
				$vehicle_dealer =  ($vehicle['dealer_code'] != '') ? (array) \Databucket::dealerInfoByDealerCode($vehicle['dealer_code']) :  'not_available';

				if ($vehicle['dealer_code'] != '') {
					$return_array['mdoca'] = \Databucket::mdoca_availability($vehicle['dealer_code']);
				} else {
					$return_array['mdoca']['status'] = 'unavailable';
				}
			} else {
				$vehicle_dealer = 'not_available';
			}

			return view('vehicle.render.review_details', ['flag' => $flag, 'tomorrow' => $tomorrow, 'vehicle' => $vehicle, 'looptrade' => $tradeloop, 'Service_lease' => $Service_lease, 'Service_finance' => $Service_finance, 'return_array' => $return_array])
				->render();
		} catch (\Exception $e) {
			dd($e->getMessage());
		}
	}


	public function routeone_iframe()
	{
		return view('vehicle.render.routeone_iframe')->render();
	}

	public function routeone()
	{
		try {
			$current_session = \Ore::getSessionID();
			$cSession = md5('userinfo' . $current_session);
			$json = \Ore::cacheGet($cSession);
			$JsonToArray   = json_decode($json, true);

			$VehicleInfo = array('year' => '', 'make' => '', 'model' => '', 'drive_type' => '', 'vin' => '', 'msrp' => '');
			$vehicle_dealer = array('dlr_dba_name' => '', 'dlr_shw_addr1' => '', 'dlr_shw_city' => '', 'dlr_shw_addr2' => '',		'dlr_shw_state' => '', 'dlr_shw_zip' => '',					'dlr_shw_phone' => '', 'dlr_email_dlr' => '', 'dlr_code' => '');
			$TradeIn = array('year' => '', 'make' => '', 'model' => '', 'style' => '', 'mileage' => '', 'zip' => '', 'condition' => '', 'price' => '', 'remainingvalue' => '');

			$rone_referid = \Ore::referenceIDGenerator('Routeone', $current_session);
		} catch (\Exception $e) {
			\Log::info('Message: ' . $e->getMessage());
		}

		try {
			if (array_key_exists('TradeIn', $JsonToArray['userinfo'])) {
				if (array_key_exists('make', $JsonToArray['userinfo']['TradeIn'])) {
					$TradeIn = $JsonToArray['userinfo']['TradeIn'];
				}
			}

			if (array_key_exists('VehicleInfo', $JsonToArray['userinfo'])) {
				$VehicleInfoArray = json_decode($JsonToArray['userinfo']['VehicleInfo'], true);
				$VehicleInfo =  array_shift($VehicleInfoArray);
			}

			if (array_key_exists('dealer_code', $VehicleInfo)) {
				$vehicle_dealer =  ($VehicleInfo['dealer_code'] != '') ? (array) \Databucket::dealerInfoByDealerCode($VehicleInfo['dealer_code']) : array();
			}
		} catch (\Exception $e) {
			\Log::info('Message: ' . $e->getMessage());
		}


		return array('VehicleInfo' => $VehicleInfo, 'DealerInfo' => $vehicle_dealer, 'current_session' => $rone_referid, 'tradein' => $TradeIn);
	}


	public function saveOreSession($user)
	{

		//\Log::info("SAVEORESESSION");
		//\Log::info($user);

		$cSession = md5('userinfo' . \Ore::getSessionID());
		$json = \Ore::cacheGet($cSession);
		$JsonToArray   = json_decode($json, true);
		$leadServices = '';
		$comment_home_delivery = 'Customer is interested to learn more about home delivery.';
		if(array_key_exists('chk_box_home_delivery', $user)){
			$leadServices .= $user['chk_box_home_delivery'] == 'true' ? $comment_home_delivery : ''; 
		}

		$vehicleArray  = json_decode($JsonToArray['userinfo']['VehicleInfo'], true);
		if (is_array($vehicleArray))
			$vehicle = array_shift($vehicleArray);
		else
			$vehicle = array();


		$tradeIn = array('year' => '', 'make' => '', 'model' => '', 'style' => '', 'mileage' => '', 'zip' => '', 'condition' => '', 'price' => '', 'remainingvalue' => '');

		if (array_key_exists('TradeIn', $JsonToArray['userinfo'])) {
			if (array_key_exists('make', $JsonToArray['userinfo']['TradeIn'])) {
				$tradeIn = $JsonToArray['userinfo']['TradeIn'];
			}
		}

		$Service = \Ore::serviceArraychecker($JsonToArray, 'Service');


		if ($Service != NULL && gettype($Service) == 'array') {
			if (count($Service) > 0) {
				$plan = $Service;
				$Services_lease = \Ore::is_array_key($plan, 'lease');
				$Services_finance = \Ore::is_array_key($plan, 'finance');

				if (count($Services_lease) > 0 || count($Services_finance) > 0) {
					$leadServices .= 'Customer is interested in following Service & Protection plans:- ';
				}

				$leadServices .= \Ore::mophar_function($Services_lease, 'lease');
				if (count($Services_lease) > 0) {
					$leadServices .= '. ';
				}
				$leadServices .= \Ore::mophar_function($Services_finance, 'finance');
				if (count($Services_finance) > 0) {
					$leadServices .= '.';
				}
			}
		}

		$mySession = $this->leadsession->firstOrCreate(['ore_session' => \Ore::getSessionID()]);
		$currentUserSessionTableID = $mySession->id;

		\Log::info($currentUserSessionTableID);

		$userExperience = \Ore::ore_user_experience($currentUserSessionTableID, $user, $tradeIn, $vehicleArray, $leadServices, '', 'uexp');
		return $userExperience;
	}

	public function saveOreIdealSession($user)
	{

		$cSession = md5('userinfo' . \Ore::getSessionID());
		$json = \Ore::cacheGet($cSession);
		$JsonToArray   = json_decode($json, true);
		$leadServices = '';
		$comment_home_delivery = 'Customer is interested to learn more about home delivery.';
		if(array_key_exists('chk_box_home_delivery', $user)){
			$leadServices .= $user['chk_box_home_delivery'] == 'true' ? $comment_home_delivery : ''; 
		}
		$vehicleArray  = json_decode($JsonToArray['userinfo']['VehicleInfo'], true);
		if (is_array($vehicleArray))
			$vehicle = array_shift($vehicleArray);
		else
			$vehicle = array();


		$tradeIn = array('year' => '', 'make' => '', 'model' => '', 'style' => '', 'mileage' => '', 'zip' => '', 'condition' => '', 'price' => '', 'remainingvalue' => '');

		if (array_key_exists('TradeIn', $JsonToArray['userinfo'])) {
			if (array_key_exists('make', $JsonToArray['userinfo']['TradeIn'])) {
				$tradeIn = $JsonToArray['userinfo']['TradeIn'];
			}
		}

		$Service = \Ore::serviceArraychecker($JsonToArray, 'Service');


		if ($Service != NULL && gettype($Service) == 'array') {
			if (count($Service) > 0) {
				$plan = $Service;
				$Services_lease = \Ore::is_array_key($plan, 'lease');
				$Services_finance = \Ore::is_array_key($plan, 'finance');

				if (count($Services_lease) > 0 || count($Services_finance) > 0) {
					$leadServices .= 'Customer is interested in following Service & Protection plans:- ';
				}

				$leadServices .= \Ore::mophar_function($Services_lease, 'lease');
				if (count($Services_lease) > 0) {
					$leadServices .= '. ';
				}
				$leadServices .= \Ore::mophar_function($Services_finance, 'finance');
				if (count($Services_finance) > 0) {
					$leadServices .= '.';
				}
			}
		}

		$mySession = $this->leadsession->firstOrCreate(['ore_session' => \Ore::getSessionID()]);
		$currentUserSessionTableID = $mySession->id;

		$userExperience = \Ore::ore_user_experience($currentUserSessionTableID, $user, $tradeIn, $vehicleArray, $leadServices, '', 'ideal');
		return $userExperience;
	}

	public function init_form_cache_valid($keys)
	{
		$url = \Ore::getSessionID();
		if (!\Databucket::hexists('user:experience:popup:status:' . $url, $keys)) {
			return "";
		} else {
			$init_first = \Databucket::isCacheHMGet('user:experience:popup:status:' . $url, $keys);
			return $init_first[0];
		}
	}
	

	public function getState($thiszip) { 
		$st = 'none';
    // Code blocks alphabetized by state
    if ($thiszip >= 35000 && $thiszip <= 36999) {
        $st = 'AL';
        $state = 'Alabama';
        }
    else if ($thiszip >= 99500 && $thiszip <= 99999) {
        $st = 'AK';
        $state = 'Alaska';
        }
    else if ($thiszip >= 85000 && $thiszip <= 86999) {
        $st = 'AZ';
        $state = 'Arizona';
        }
    else if ($thiszip >= 71600 && $thiszip <= 72999) {
        $st = 'AR';
        $state = 'Arkansas';
        }
    else if ($thiszip >= 90000 && $thiszip <= 96699) {
        $st = 'CA';
        $state = 'California';
        }
    else if ($thiszip >= 80000 && $thiszip <= 81999) {
        $st = 'CO';
        $state = 'Colorado';
        }
    else if ($thiszip >= 6000 && $thiszip <= 6999) {
        $st = 'CT';
        $state = 'Connecticut';
        }
    else if ($thiszip >= 19700 && $thiszip <= 19999) {
        $st = 'DE';
        $state = 'Delaware';
        }
    else if ($thiszip >= 32000 && $thiszip <= 34999) {
        $st = 'FL';
        $state = 'Florida';
        }
    else if ($thiszip >= 30000 && $thiszip <= 31999) {
        $st = 'GA';
        $state = 'Georgia';
        }
    else if ($thiszip >= 96700 && $thiszip <= 96999) {
        $st = 'HI';
        $state = 'Hawaii';
        }
    else if ($thiszip >= 83200 && $thiszip <= 83999) {
        $st = 'ID';
        $state = 'Idaho';
        }
    else if ($thiszip >= 60000 && $thiszip <= 62999) {
        $st = 'IL';
        $state = 'Illinois';
        }
    else if ($thiszip >= 46000 && $thiszip <= 47999) {
        $st = 'IN';
        $state = 'Indiana';
        }
    else if ($thiszip >= 50000 && $thiszip <= 52999) {
        $st = 'IA';
        $state = 'Iowa';
        }
    else if ($thiszip >= 66000 && $thiszip <= 67999) {
        $st = 'KS';
        $state = 'Kansas';
        }
    else if ($thiszip >= 40000 && $thiszip <= 42999) {
        $st = 'KY';
        $state = 'Kentucky';
        }
    else if ($thiszip >= 70000 && $thiszip <= 71599) {
        $st = 'LA';
        $state = 'Louisiana';
        }
    else if ($thiszip >= 3900 && $thiszip <= 4999) {
        $st = 'ME';
        $state = 'Maine';
        }
    else if ($thiszip >= 20600 && $thiszip <= 21999) {
        $st = 'MD';
        $state = 'Maryland';
        }
    else if ($thiszip >= 1000 && $thiszip <= 2799) {
        $st = 'MA';
        $state = 'Massachusetts';
        }
    else if ($thiszip >= 48000 && $thiszip <= 49999) {
        $st = 'MI';
        $state = 'Michigan';
        }
    else if ($thiszip >= 55000 && $thiszip <= 56999) {
        $st = 'MN';
        $state = 'Minnesota';
        }
    else if ($thiszip >= 38600 && $thiszip <= 39999) {
        $st = 'MS';
        $state = 'Mississippi';
        }
    else if ($thiszip >= 63000 && $thiszip <= 65999) {
        $st = 'MO';
        $state = 'Missouri';
        }
    else if ($thiszip >= 59000 && $thiszip <= 59999) {
        $st = 'MT';
        $state = 'Montana';
        }
    else if ($thiszip >= 27000 && $thiszip <= 28999) {
        $st = 'NC';
        $state = 'North Carolina';
        }
    else if ($thiszip >= 58000 && $thiszip <= 58999) {
        $st = 'ND';
        $state = 'North Dakota';
        }
    else if ($thiszip >= 68000 && $thiszip <= 69999) {
        $st = 'NE';
        $state = 'Nebraska';
        }
    else if ($thiszip >= 88900 && $thiszip <= 89999) {
        $st = 'NV';
        $state = 'Nevada';
        }
    else if ($thiszip >= 3000 && $thiszip <= 3899) {
        $st = 'NH';
        $state = 'New Hampshire';
        }
    else if ($thiszip >= 7000 && $thiszip <= 8999) {
        $st = 'NJ';
        $state = 'New Jersey';
        }
    else if ($thiszip >= 87000 && $thiszip <= 88499) {
        $st = 'NM';
        $state = 'New Mexico';
        }
    else if ($thiszip >= 10000 && $thiszip <= 14999) {
        $st = 'NY';
        $state = 'New York';
        }
    else if ($thiszip >= 43000 && $thiszip <= 45999) {
        $st = 'OH';
        $state = 'Ohio';
        }
    else if ($thiszip >= 73000 && $thiszip <= 74999) {
        $st = 'OK';
        $state = 'Oklahoma';
        }
    else if ($thiszip >= 97000 && $thiszip <= 97999) {
        $st = 'OR';
        $state = 'Oregon';
        }
    else if ($thiszip >= 15000 && $thiszip <= 19699) {
        $st = 'PA';
        $state = 'Pennsylvania';
        }
    else if ($thiszip >= 300 && $thiszip <= 999) {
        $st = 'PR';
        $state = 'Puerto Rico';
        }
    else if ($thiszip >= 2800 && $thiszip <= 2999) {
        $st = 'RI';
        $state = 'Rhode Island';
        }
    else if ($thiszip >= 29000 && $thiszip <= 29999) {
        $st = 'SC';
        $state = 'South Carolina';
        }
    else if ($thiszip >= 57000 && $thiszip <= 57999) {
        $st = 'SD';
        $state = 'South Dakota';
        }
    else if ($thiszip >= 37000 && $thiszip <= 38599) {
        $st = 'TN';
        $state = 'Tennessee';
        }
    else if ( ($thiszip >= 75000 && $thiszip <= 79999) || ($thiszip >= 88500 && $thiszip <= 88599) ) {
        $st = 'TX';
        $state = 'Texas';
        }
    else if ($thiszip >= 84000 && $thiszip <= 84999) {
        $st = 'UT';
        $state = 'Utah';
        }
    else if ($thiszip >= 5000 && $thiszip <= 5999) {
        $st = 'VT';
        $state = 'Vermont';
        }
    else if ($thiszip >= 22000 && $thiszip <= 24699) {
        $st = 'VA';
        $state = 'Virgina';
        }
    else if ($thiszip >= 20000 && $thiszip <= 20599) {
        $st = 'DC';
        $state = 'Washington DC';
        }
    else if ($thiszip >= 98000 && $thiszip <= 99499) {
        $st = 'WA';
        $state = 'Washington';
        }
    else if ($thiszip >= 24700 && $thiszip <= 26999) {
        $st = 'WV';
        $state = 'West Virginia';
        }
    else if ($thiszip >= 53000 && $thiszip <= 54999) {
        $st = 'WI';
        $state = 'Wisconsin';
        }
    else if ($thiszip >= 82000 && $thiszip <= 83199) {
        $st = 'WY';
        $state = 'Wyoming';
        }
    else {
        $st = 'none';
        $state = 'none';
    }

    return $st;
}
}

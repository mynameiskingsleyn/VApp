<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\uptimeMailable; 
use Fcaore\Databucket\Facade\Databucket;
use DB;
use App\Facades\OreDSClass;
use App\Mail\FeedbackMail;

class CronLeadAuto extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:LeadAuto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Every five minitues system send the lead who has been abandoned the lead submission'; 

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		 
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
		if(config('ore.lead.autolead') == 'YES')	{ 
				
				\Log::info(" -- AUTO LEAD HITTED --");
		
				$auto_lead = \Databucket::autolead_query(); 
				$r_des = json_decode(collect($auto_lead), true);
				$r_des_array = array_column($r_des,'ore_session','id');  
				 $vehcilefieldsonly = array('stock_number','doors','interior_meta_color_desc','trim_desc','dealer_code','year','make','model','vin','trim_desc','body_style','transmission_desc','exterior_color_desc','msrp');
						 
						 
				 foreach($r_des_array as $fetchedSessionID) {
					if(\Databucket::hexists('user:experience:popup:status:'.$fetchedSessionID, 'first')){
						$user = $vinfo2 = [];
						$leadServices = "";
							   $user['first'] 				= $this->init_form_cache_valid('first', $fetchedSessionID);
							   $user['last'] 				= $this->init_form_cache_valid('last', $fetchedSessionID);
							   $user['postalcode'] 			= $this->init_form_cache_valid('postalcode', $fetchedSessionID);
							   $user['contact_phone'] 		= $this->init_form_cache_valid('contact_phone', $fetchedSessionID);
							   $user['contact_email'] 		= $this->init_form_cache_valid('contact_email', $fetchedSessionID);
							   $user['chk_box_home_delivery'] = $this->init_form_cache_valid('chk_box_home_delivery',$fetchedSessionID);
								$user['current_tier_value'] = $this->init_form_cache_valid('current_tier_value', $fetchedSessionID);
				  
								if(\Databucket::hexists('user:experience:sessionid:'.$fetchedSessionID, 'vehicle_info')){
									$vinfo = \Databucket::isCacheHMGet('user:experience:sessionid:'.$fetchedSessionID, 'vehicle_info'); 
									$vinfo1 = json_decode($vinfo[0], true);
									foreach($vinfo1 as $key => $val){
										if(in_array($key, $vehcilefieldsonly)) $vinfo2[$key] = $val;
									} 
								}
								 
								if(\Databucket::hexists('user:experience:sessionid:'.$fetchedSessionID, 'mophar')){
									$vmophar = \Databucket::isCacheHMGet('user:experience:sessionid:'.$fetchedSessionID, 'mophar'); 
									
									
									if(count($vmophar) > 0){
										$leadServices  = $vmophar[0];
									}
								}	
						
						 /*  $userExperience = \Ore::ore_user_experience($fetchedSessionID, $user, array(),$vinfo2, $leadServices , '', 'cron');							
						\Ore::vendor_information_insert('user_experience', $userExperience,  'uexp');  */
						
						 $userExperience = \Ore::ore_user_experience($fetchedSessionID, $user, array(),$vinfo2, $leadServices , '', 'auto');	 
						
						$output = \Ore::vendor_information_insert('user_experience', $userExperience,  'auto');
						$lead_status = $output['status'];
						if($lead_status){
							$this->SendMailforAutoLead($fetchedSessionID,$output,$userExperience,$user);
						}
					}
				}  
		} 	 	
    	 
    }
	
	public function init_form_cache_valid($keys, $fetchedSessionID){
			$url = $fetchedSessionID;
			if(!\Databucket::hexists('user:experience:popup:status:'.$url, $keys)){
				 return ""; 
			  }else{
				 $init_first = \Databucket::isCacheHMGet('user:experience:popup:status:'.$url, $keys); 
				 return $init_first[0];		 
			  }
	}

	public function SendMailforAutoLead($fetchedSessionID,$output,$saveOreSession,$form){
		try{
			$lead_status = $output['status'];  
			$LeadId = $output['message'][0];
			$sold_type_from_lead = "available";
			$url = $fetchedSessionID;
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
				try {
					\Mail::to($toEmail)->send(new FeedbackMail($comment));
					\Log::info("CronAutoLead:: E-Mail Sending successfully");
				} catch (\Exception $e) {
					\Log::info("CronAutoLead:: E-Mail Sending Issue in CronLeadAuto - mylead");
					\Log::info($e->getMessage());					
				}
			}
		}catch (\Exception $e) {
			\Log::info("CronAutoLead::SendMailforAutoLead:: E-Mail Sending Issue in CronLeadAuto - mylead");
			\Log::info($e->getMessage());					
		}	
	}
}
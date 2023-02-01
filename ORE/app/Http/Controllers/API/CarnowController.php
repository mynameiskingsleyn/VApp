<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Api;
use App\Stage;
use App\Leadsession;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;
use App\Facades\OreDSClass;
use Illuminate\Support\Facades\Validator;
use DB;

class CarnowController extends Controller
{
    private $Api;
	
	private $Stage;
	
	private $Leadsession;
    public function __construct(Api $Api, Stage $Stage, Leadsession $Leadsession)
    {
         $this->api = $Api;
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
        //
    }
	
	public function post_api(Request $request){ 
	exit;
	$type = $request->get('type');
	$session = $request->get('session');
	$attributes = $request->get('attributes');
	
	$current_session = \Ore::getSessionID();
	 
	//$carnow_refid = \Ore::referenceIDGenerator('Carnow', $current_session);
	
	 Leadsession::updateOrCreate(
							['ore_session' => $current_session],
							['carnow_refid' => $session]);
	\Databucket::isCacheHMSet('user:experience:sessionid:carnow:'.$session, 'session',$current_session);
	if(!empty($session)){
		$car = config('ore.carnow.endpoint').$session.'/send_message?key='.config('ore.carnow.key');
	}else{ 
	return response([ 'message' => 'session invalid', 'status' => false ], 404)->header('Content-Type', 'application/json'); 
	}
	if(empty($attributes)){
		 
			return response([ 'message' => 'Invalid Data', 'status' => false ], 404)->header('Content-Type', 'application/json');
	}
		if($type == 'base'){
			
			$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $car,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => "{\r\n\t\"message\": {\r\n\t\t\"type\" : \"1\",\r\n\t\t\"event\" : \"0\",\r\n\t\t\"body\" : \"$attributes\"\r\n\t}\r\n}",
				  CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/json" 
				  ),
				));

				$response = curl_exec($curl);
				\Log::info($response);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  echo $response;
				} 
				
				 
		}else if($type == 'inventory'){
			  
					$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => $car,
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => "{\"message\": {\"type\" : \"1\",\"event\" : \"0\",\"body\" : \"\",\"as_cust\" : \"1\", \"attachment_type\" : \"5\",\"data\" : {\"vin\" : \"$attributes\"}}}",
				  CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"content-type: application/json" 
				  ),
				));

				$response = curl_exec($curl);
				\Log::info($response);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  echo $response;
				} 
		}else if($type == 'down'){
			 
			if(array_key_exists('body', $params)){
				
			}
		}else{
			
		}
	}
/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		exit;
		$bodyContent = "";
		$m_Result = "Failed :(";
		try{
			$bodyContent = $request->getContent();	 
			
			if(config('ore.carnow.save_json_response')){
				$this->api->request = $bodyContent;
				$this->api->save();	 
			}	
			
		$inBoundData = \Ore::Carnow_InBound($bodyContent);
		$output = \Ore::vendor_information_insert('Carnow_InBound',$inBoundData,'cnow');
		// \Databucket::flagupdate_query();
		//\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'lead_status','success');
		 $lead_status = $output['status'];
		 $return_msg  = '';
		 $LeadId = $output['message'][0];
		 $sold_type_from_lead= "available";
		 $url = \Ore::getSessionID();
		 $return_array['qrcode'] = $url;
		 $return_array['vin'] = $inBoundData['vehicle_vin'];
		  		
			$dealerarrayresult = \Ore::DealerApi($inBoundData['dealer_code']);

         
		 if( $dealerarrayresult['dealerName']!=''){
						$isDealer =  Str::replaceFirst('the fiat', '', strtolower($dealerarrayresult['dealerName']));
						$isDealer =  Str::replaceFirst('and fiat', '', strtolower($isDealer));
						$isDealer =  Str::replaceFirst('  ', '', strtolower($isDealer));
						$isDealer =  Str::replaceFirst('fiat', '', strtolower($isDealer));
						$isDealer =  Str::replaceFirst('and of', 'of', strtolower($isDealer));
						$isDealer =  Str::replaceFirst('and  of', 'of', strtolower($isDealer));
						$isDealer =  Str::replaceFirst('romeo-', 'romeo', strtolower($isDealer));
						$isDealer =  ucwords($isDealer);
					}else $isDealer = '';
		$return_array['dealerName'] = $isDealer;
		 
		$return_array['dealerName'] = $isDealer;
		$return_array['dealerAddress1'] = $inBoundData['streetline1'];
		$return_array['dealerAddress2'] = $inBoundData['streetline2'];
		$return_array['dealerCity'] = $inBoundData['city'];
		$return_array['dealerState'] = $inBoundData['state'];
		$return_array['dealerZip'] = substr($inBoundData['zip'],0,5) ?? '';
		$return_array['phoneNumber'] = $inBoundData['phone'];
		$return_array['demail'] = $inBoundData['email']; 
		$return_array['dealerCode'] = $inBoundData['dealer_code']; 
		$return_array['vin'] = $inBoundData['vehicle_vin'];
		$return_array['params_make'] = $inBoundData['vehicle_make'];
		$return_array['params_model'] = $inBoundData['vehicle_model'];
		$return_array['params_year'] = $inBoundData['vehicle_year'];
		
							
		// Validation Logic	
			if($lead_status){ 
				//Sold Type  
				$toEmail = $inBoundData['email']; 
				$comment['LeadId'] = $LeadId; 
				$comment['name'] = $inBoundData['first_name'];//.' '.$form['last'];				
				$comment['sold_type'] = $sold_type_from_lead; 
				$comment['qrcodemail'] =  config('ore.ishowroom.Qrcode.endpoint').'vin='.$inBoundData['vehicle_vin'].'&sid=S99111G&dealerCode='.$inBoundData['dealer_code'].'&fname=User 45&lname=at FCA US LLC&launchSource=OSCustomerORE&hideVehcCapContainer=True';
				$comment['qrcode'] = $url;
				$comment['dealerName'] = $isDealer;
				$comment['dealerAddress1'] = $dealerarrayresult['dealerAddress1'];
				$comment['dealerCity'] = $dealerarrayresult['dealerCity'];
				$comment['dealerState'] = $dealerarrayresult['dealerState'];
				$comment['dealerZip'] = $dealerarrayresult['dealerZip'];
				$comment['phoneNumber'] = $dealerarrayresult['phoneNumber'];
				$comment['year'] = $inBoundData['vehicle_year'];
				$comment['make'] = $inBoundData['vehicle_make'];
				$comment['model'] = $inBoundData['vehicle_model'];
				$comment['vin'] = $inBoundData['vehicle_vin'];
				//$inBoundData['lead_status'] = 'success';
				
				//$this->saveOreSession($form, $LeadId, $currentUserSessionTableID); 
				try{
					Mail::to($toEmail)->send(new FeedbackMail($comment)); 
				}catch(\Exception $e){
					\Log::info($e->getMessage());
					return response("Received", 200)->header('Content-Type', 'application/json'); 
					/*return ['status' =>"success", 'message' => 'Lead has been received successfully. Confirmation e-mail have following issue: '.$e->getMessage(), 'sold_type' => $sold_type_from_lead];
					*/
					exit;
				} 
					//Regenerate New Session
				    session()->regenerate();
					return response("Received", 200)->header('Content-Type', 'application/json'); 
					//return ['status' =>"success", 'message' => $LeadId, 'sold_type' => $sold_type_from_lead];
					exit;
			}else{
				
				$return_desc  = $output['description'];
				$msd = Arr::has($output, 'description.0');
				if($return_desc == 'Lead considered to not be a serious buyer'){
						$err = 'Lead considered to not be a serious buyer <br/><br/>
						<div style="text-align:left;">
						<b>HINT:</b>  Kindly check the following and try again<br/>
							1. First Name <br/>
							2. Last Name <br/>
							3. Address</div>';
				}else if($return_desc == 'Invalid Vehicle Model'){
						$err = 'Invalid Vehicle Model';
				} else{
					$err = 'Unknown issues. Please Valid your input.'; 
				}
				
				 return response()->json([ 'error' => $err], 404);
				
				
			}
		}catch(\Exception $e){
			\Log::info("Error in SOAP carnow Request: ".$e->getMessage()); 
		} 
		return response("Received", 200)->header('Content-Type', 'application/json'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

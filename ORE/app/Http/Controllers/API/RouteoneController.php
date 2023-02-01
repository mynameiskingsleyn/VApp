<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Api;
use App\Stage;
use App\Leadsession;
use App\Facades\OreDSClass;
use Illuminate\Support\Facades\Mail;
use App\Mail\FeedbackMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use DB;

class RouteoneController extends Controller
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$bodyContent = "";
		$m_Result = "Failed :(";
		try{
				$soap_request  = "<?xml version=\"1.0\"?>\n";
			  $soap_request .= "<soap:Envelope xmlns:soap=\"http://www.w3.org/2001/12/soap-envelope\" soap:encodingStyle=\"http://www.w3.org/2001/12/soap-encoding\">\n";
			  $soap_request .= "  <soap:Body xmlns:m=\"http://test.ore.v2soft.com/api/v1/routeone\">\n";
			  $soap_request .= "    <m:Routeone>\n";
			  $soap_request .= "      <m:SentTimeStamp>".Carbon::now()."</m:SentTimeStamp>\n";
			  $soap_request .= "      <m:MessageType>Status</m:MessageType>\n";
			  $soap_request .= "      <m:Result>Success</m:Result>\n";
			  $soap_request .= "    </m:Routeone>\n";
			  $soap_request .= "  </soap:Body>\n";
			  $soap_request .= "</soap:Envelope>";
  
			return response($soap_request, 200)->header('Content-Type', 'application/xml');  
			exit;
		
		
			$bodyContent = $request->getContent();	
			
			if(config('ore.routeone.log_soap_response')){
				\Log::info($bodyContent); 
			}
		 	if(config('ore.routeone.save_soap_response')){
				 $this->api->request = $bodyContent;
				 $this->api->save();
			}		
			 
			
		 $inBoundData = \Ore::Routeone_InBound($bodyContent);
		 
		 
		$output = \Ore::vendor_information_insert('Routeone_InBound',$inBoundData,'rone');
		// \Databucket::flagupdate_query();
		\Databucket::isCacheHMSet('user:experience:sessionid:'.\Ore::getSessionID(), 'lead_status','success');
		 $lead_status = $output['status'];
		 $return_msg  = '';
		 $LeadId = $output['message'][0];
		 $sold_type_from_lead= "available";
		 $url = \Ore::getSessionID();
		 $return_array['qrcode'] = $url;
		  $return_array['vin'] = $inBoundData['vehicle_vin'];
		  $v_arr = $inBoundData;
		 
		$return_array['dealerName'] = $inBoundData['dealer_name'];
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
		
		$dealerid = "";
				if(!\Databucket::isCacheExists($inBoundData['vehicle_vin'])){ 
						$datass = \App\Vehicle::where(['vin' => $inBoundData['vehicle_vin']])->first(); 
						$val1 = json_decode(collect($datass),true);
						$dealerid = $val1['dealer_code'];
				}else{
					$datass = \Ore::isCacheGetAll($inBoundData['vehicle_vin']);
					$dealerid = $datass['dealer_code'];
					}
					
			$dealerarrayresult = \Ore::DealerApi($dealerid);

         
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
		
							
		// Validation Logic	
			if($lead_status){ 
				//Sold Type  
				$toEmail = $inBoundData['email']; 
				$comment['LeadId'] = $LeadId; 
				$comment['name'] = $inBoundData['first_name'];//.' '.$form['last'];				
				$comment['sold_type'] = $sold_type_from_lead; 
				$comment['qrcodemail'] =  config('ore.ishowroom.Qrcode.endpoint').'vin='.$inBoundData['vehicle_vin'].'&sid=S99111G&dealerCode='.$dealerid.'&fname=User 45&lname=at FCA US LLC&launchSource=OSCustomerORE&hideVehcCapContainer=True';
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
					return ['status' =>"success", 'message' => 'Lead has been received successfully. Confirmation e-mail have following issue: '.$e->getMessage(), 'sold_type' => $sold_type_from_lead];
					exit;
				} 
					//Regenerate New Session
				    session()->regenerate();
					
					return ['status' =>"success", 'message' => $LeadId, 'sold_type' => $sold_type_from_lead];
					exit;
			}else{
				$return_desc  = $output['description'];
				$msd = Arr::has($output, 'description.0');
				if($return_desc == 'Lead considered to not be a serious buyer'){
						return ['status' =>"failed", 'message' => 'Lead considered to not be a serious buyer <br/><br/>
						<div style="text-align:left;">
						<b>HINT:</b>  Kindly check the following and try again<br/>
							1. First Name <br/>
							2. Last Name <br/>
							3. Address</div>'];
				}else if($return_desc == 'Invalid Vehicle Model'){
						return ['status' =>"failed", 'message' => 'Invalid Vehicle Model'];
				} else{
					return ['status' =>"failed", 'message' => 'Unknown issues. Please Valid your input.'];
				}
			} 
		
			 
			$m_Result = ($bodyContent!="") ? "Success!" : "Failed :(";
		}catch(\Exception $e){
			\Log::info("Error in SOAP Routeone Request: ".$e->getMessage()); 
		} 
		
         $soap_request  = "<?xml version=\"1.0\"?>\n";
		  $soap_request .= "<soap:Envelope xmlns:soap=\"http://www.w3.org/2001/12/soap-envelope\" soap:encodingStyle=\"http://www.w3.org/2001/12/soap-encoding\">\n";
		  $soap_request .= "  <soap:Body xmlns:m=\"http://test.ore.v2soft.com/api/v1/routeone\">\n";
		  $soap_request .= "    <m:Routeone>\n";
		  $soap_request .= "      <m:SentTimeStamp>".Carbon::now()."</m:SentTimeStamp>\n";
		  $soap_request .= "      <m:MessageType>Status</m:MessageType>\n";
		  $soap_request .= "      <m:Result>".$m_Result."</m:Result>\n";
		  $soap_request .= "    </m:Routeone>\n";
		  $soap_request .= "  </soap:Body>\n";
		  $soap_request .= "</soap:Envelope>";
  
		return response($soap_request, 200)->header('Content-Type', 'application/xml'); 
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

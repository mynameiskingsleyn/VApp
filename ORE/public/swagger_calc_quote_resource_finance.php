<?php 
 function getBaseUrl() 
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF']; 

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
    $pathInfo = pathinfo($currentPath); 

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST']; 

    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';

    // return: http://localhost/myproject/
    return $protocol.'://'.$hostName.$pathInfo['dirname']."/";
}

$vehicle_vin = $vehicle_model = $vehicle_sellingPrice =  '';
$cashDown = '';
$tradeIn = 0;
$customer_zipcode = 48302;
$terms = 36;
$dealercode = 69008;
$vehicle_year =2020;
$milesPerYear = 10000;
$tier = 1;
$financeSource = 'F00CHC';
$vehicle_model = 'Cherokee';
$make = 'jeep';
if(isset($_POST['form_submit'])){
			$tradeIn = 0;
			if(isset($_POST['make'])) $make = $_POST['make']; else $make = 0;
			if(isset($_POST['tradeIn'])) $tradeIn = $_POST['tradeIn']; else $tradeIn = 0;
			if(isset($_POST['vin'])) $vehicle_vin = $_POST['vin']; else $vehicle_vin = 'ZARFAMAN6K7612678';
			if(isset($_POST['year'])) $vehicle_year = $_POST['year']; else $vehicle_year = '2019'; 
			if(isset($_POST['model'])) $vehicle_model = $_POST['model']; else $vehicle_model = 'STELVIO';  
			if(isset($_POST['msrp'])) $vehicle_sellingPrice = $_POST['msrp']; else $vehicle_sellingPrice = 41480;
			if(isset($_POST['zip'])) $customer_zipcode = $_POST['zip']; else $customer_zipcode = 41480;	

			if(isset($_POST['cashdown'])) $cashDown = $_POST['cashdown']; else $cashDown = 4148;
			if(isset($_POST['terms'])) $terms = $_POST['terms']; else $terms = 36;
			//if(isset($_POST['milesPerYear'])) $milesPerYear = $_POST['milesPerYear']; else $milesPerYear = 10000;
			if(isset($_POST['dealercode'])) $dealercode = $_POST['dealercode']; else $dealercode = 0;
			
	if(isset($_POST['financeSource'])) $financeSource = $_POST['financeSource']; else $financeSource = 'F000BA';			  
			
			if($financeSource == 'F000BA') $tier = 'S'; else $tier = 1;
			
}


$domain = parse_url(getBaseUrl(), PHP_URL_HOST);
if($domain == 'localhost' || $domain=='devore.v2soft.com'){
	
} else{ exit; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Swagger</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  
</head>
<body>

<div class="text-center" style="margin-bottom:0">
  <h1>Payment Calculator</h1>
  <p>Routeone</p> 
</div>
 <hr>

<div class="container" style="margin-top:60px">
 <div class="row">
 
   <a href="swagger_calc_quote_resource_standardlease.php" class="btn btn-success">GOTO LEASE</a>
</div> 


  <div class="row">
		
	<div class="finance">	
		<br>
		<h5>FINANCE</h5>
		<hr>
		
		
       <form method="post" action="swagger_calc_quote_resource_finance.php#result">
					
			<div class="row">		
			<div class="col-md-6">	
<div class="form-group"><input type="hidden" name="form_submit">
                                <label for="msrp">Make</label>
                                <select name="make" class="form-control" required="">
                                    <option value="alfa romeo" <?php if($make=='alfa romeo'){?> selected <?php }?>>alfa romeo</option>
                                    <option value="jeep" <?php if($make=="jeep"){?> selected <?php }?>>jeep</option>
                                    <option value="dodge" <?php if($make=="dodge"){?> selected <?php }?>>dodge</option>
                                    <option value="chrysler" <?php if($make=="chrysler"){?> selected <?php }?>>chrysler</option>
                                    <option value="ram" <?php if($make=="ram"){?> selected <?php }?>>ram</option>
                                </select>
                            </div>
							
					<div class="form-group"><input type="hidden" name="form_submit" >
					<label for="vin">VIN *</label>
					<input type="text" class="form-control" id="vin" name="vin" value="<?php echo $vehicle_vin; ?>" required>
				  </div>
				  <div class="form-group">
					<label for="msrp">Year *</label>
					<select name="year" class="form-control" required>
							<option value="2020" <?php if($vehicle_year==2020){?> selected <?php }?>>2020</option>
							<option value="2019" <?php if($vehicle_year==2019){?> selected <?php }?>>2019</option>
							<option value="2018" <?php if($vehicle_year==2018){?> selected <?php }?>>2018</option> 
					</select> 
				  </div>
				   <div class="form-group">
					<label for="msrp">Model *</label>
					<select name="model" class="form-control" required="">
                                	<optgroup label="ALFA ROMEO">
	                                    <option value="GIULIA" <?php if($vehicle_model=="GIULIA"){?> selected <?php }?>>GIULIA</option>
	                                    <option value="STELVIO" <?php if($vehicle_model=="STELVIO"){?> selected <?php }?>>STELVIO</option>
	                                    <option value="4C SPIDER" <?php if($vehicle_model=="4C SPIDER"){?> selected <?php }?>>4C SPIDER</option>
                                    </optgroup>
                                    <optgroup label="JEEP">
	                                    <option value="Cherokee" <?php if($vehicle_model=="Cherokee"){?> selected <?php }?>>Cherokee</option>
	                                    <option value="Compass" <?php if($vehicle_model=="Compass"){?> selected <?php }?>>Compass</option>
	                                    <option value="Compass Latitud" <?php if($vehicle_model=="Compass Latitud"){?> selected <?php }?>>Compass Latitud</option>
	                                    <option value="Compass MK" <?php if($vehicle_model=="Compass MK"){?> selected <?php }?>>Compass MK</option>
	                                    <option value="Gladiator" <?php if($vehicle_model=="Gladiator"){?> selected <?php }?>>Gladiator</option>
	                                    <option value="Gladiator Rubic" <?php if($vehicle_model=="Gladiator Rubic"){?> selected <?php }?>>Gladiator Rubic</option>
	                                    <option value="Grand Cherokee" <?php if($vehicle_model=="Grand Cherokee"){?> selected <?php }?>>Grand Cherokee</option>
	                                    <option value="JEEP COMPASS TRAILHAWK 4X4" <?php if($vehicle_model=="JEEP COMPASS TRAILHAWK 4X4"){?> selected <?php }?>>JEEP COMPASS TRAILHAWK 4X4</option>
	                                    <option value="JEEP GLADIATOR RUBICON 4X4" <?php if($vehicle_model=="JEEP GLADIATOR RUBICON 4X4"){?> selected <?php }?>>JEEP GLADIATOR RUBICON 4X4</option>
	                                    <option value="LIBERTY SPORT 4X4" <?php if($vehicle_model=="LIBERTY SPORT 4X4"){?> selected <?php }?>>LIBERTY SPORT 4X4</option>
	                                    <option value="Patriot" <?php if($vehicle_model=="Patriot"){?> selected <?php }?>>Patriot</option>
	                                    <option value="Renegade" <?php if($vehicle_model=="Renegade"){?> selected <?php }?>>Renegade</option>
	                                    <option value="Wrangler" <?php if($vehicle_model=="Wrangler"){?> selected <?php }?>>Wrangler</option>
	                                    <option value="Wrangler JK" <?php if($vehicle_model=="Wrangler JK"){?> selected <?php }?>>Wrangler JK</option>
	                                    <option value="Wrangler Unlimited" <?php if($vehicle_model=="Wrangler Unlimited"){?> selected <?php }?>>Wrangler Unlimited</option>
                                    </optgroup>
                                    <optgroup label="RAM">
	                                    <option value="1500" <?php if($vehicle_model=="1500"){?> selected <?php }?>>1500</option>
	                                    <option value="1500 Classic" <?php if($vehicle_model=="1500 Classic"){?> selected <?php }?>>1500 Classic</option>
	                                    <option value="1500 SLT" <?php if($vehicle_model=="1500 SLT"){?> selected <?php }?>>1500 SLT</option>
	                                    <option value="1500/SLT" <?php if($vehicle_model=="1500/SLT"){?> selected <?php }?>>1500/SLT</option>
	                                    <option value="3500" <?php if($vehicle_model=="3500"){?> selected <?php }?>>3500</option>
	                                    <option value="All-New  1500" <?php if($vehicle_model=="ll-New  1500"){?> selected <?php }?>>All-New 1500</option>
	                                    <option value="All-New 1500" <?php if($vehicle_model=="All-New 1500"){?> selected <?php }?>>All-New 1500</option>
	                                    <option value="All-New Ram 1500" <?php if($vehicle_model=="All-New Ram 1500"){?> selected <?php }?>>All-New Ram 1500</option>
	                                    <option value="JOURNEY SE AWD" <?php if($vehicle_model=="JOURNEY SE AWD"){?> selected <?php }?>>JOURNEY SE AWD</option>
	                                    <option value="LIBERTY SPORT 4X4" <?php if($vehicle_model=="LIBERTY SPORT 4X4"){?> selected <?php }?>>LIBERTY SPORT 4X4</option>
	                                    <option value="Pickup 1500 Classic" <?php if($vehicle_model=="Pickup 1500 Classic"){?> selected <?php }?>>Pickup 1500 Classic</option>
	                                    <option value="Ram 1500" <?php if($vehicle_model=="Ram 1500"){?> selected <?php }?>>Ram 1500</option>
	                                    <option value="Ram 1500 Classic" <?php if($vehicle_model=="Ram 1500 Classic"){?> selected <?php }?>>Ram 1500 Classic</option>
	                                    <option value="RAM 1500 SLT CREW CAB 4X4" <?php if($vehicle_model=="RAM 1500 SLT CREW CAB 4X4"){?> selected <?php }?>>RAM 1500 SLT CREW CAB 4X4</option>
	                                    <option value="RAM 1500 SLT QUAD CAB 4X4" <?php if($vehicle_model=="RAM 1500 SLT QUAD CAB 4X4"){?> selected <?php }?>>RAM 1500 SLT QUAD CAB 4X4</option>
	                                    <option value="RAM 1500 TRADESMAN QUAD CAB 4X2" <?php if($vehicle_model=="RAM 1500 TRADESMAN QUAD CAB 4X2"){?> selected <?php }?>>RAM 1500 TRADESMAN QUAD CAB 4X2</option>
	                                    <option value="Ram 2500" <?php if($vehicle_model=="Ram 2500"){?> selected <?php }?>>Ram 2500</option>
	                                    <option value="Ram 3500" <?php if($vehicle_model=="Ram 3500"){?> selected <?php }?>>Ram 3500</option>
	                                    <option value="Ram 3500 Chassis Cab" <?php if($vehicle_model=="Ram 3500 Chassis Cab"){?> selected <?php }?>>Ram 3500 Chassis Cab</option>
	                                    <option value="Ram 3500 SRW 10K GVWR Chassis Cab" <?php if($vehicle_model=="Ram 3500 SRW 10K GVWR Chassis Cab"){?> selected <?php }?>>Ram 3500 SRW 10K GVWR Chassis Cab</option>
	                                    <option value="Ram 4500 Chassis Cab" <?php if($vehicle_model=="Ram 4500 Chassis Cab"){?> selected <?php }?>>Ram 4500 Chassis Cab</option>
	                                    <option value="Ram 5500 Chassis Cab" <?php if($vehicle_model=="Ram 5500 Chassis Cab"){?> selected <?php }?>>Ram 5500 Chassis Cab</option>
	                                    <option value="Ram ProMaster City®" <?php if($vehicle_model=="Ram ProMaster City"){?> selected <?php }?>>Ram ProMaster City®</option>
	                                    <option value="Ram ProMaster®" <?php if($vehicle_model=="Ram ProMaster"){?> selected <?php }?>>Ram ProMaster®</option>
                                    </optgroup>
                                    <optgroup label="CHRYSLER">
	                                    <option value="Chrysler 200" <?php if($vehicle_model=="Chrysler 200"){?> selected <?php }?>>Chrysler 200</option>
	                                    <option value="Chrysler 300" <?php if($vehicle_model=="Chrysler 300"){?> selected <?php }?>>Chrysler 300</option>
	                                    <option value="Pacifica" <?php if($vehicle_model=="Pacifica"){?> selected <?php }?>>Pacifica</option>
	                                    <option value="Pacifica Hybrid" <?php if($vehicle_model=="Pacifica Hybrid"){?> selected <?php }?>>Pacifica Hybrid</option>
	                                    <option value="Voyager" <?php if($vehicle_model=="Voyager"){?> selected <?php }?>>Voyager</option>
                                    </optgroup>
                                    <optgroup label="CHRYSLER">
	                                    <option value="Caliber" <?php if($vehicle_model=="Caliber"){?> selected <?php }?>>Caliber</option>
	                                    <option value="Challenger" <?php if($vehicle_model=="Challenger"){?> selected <?php }?>>Challenger</option>
	                                    <option value="Charger" <?php if($vehicle_model=="Charger"){?> selected <?php }?>>Charger</option>
	                                    <option value="Dart" <?php if($vehicle_model=="Dart"){?> selected <?php }?>>Dart</option>
	                                    <option value="Durango" <?php if($vehicle_model=="Durango"){?> selected <?php }?>>Durango</option>
	                                    <option value="Grand Caravan" <?php if($vehicle_model=="Grand Caravan"){?> selected <?php }?>>Grand Caravan</option>
	                                    <option value="Journey" <?php if($vehicle_model=="WJourney"){?> selected <?php }?>>Journey</option>
	                                    <option value="Viper" <?php if($vehicle_model=="Viper"){?> selected <?php }?>>Viper</option>
                                    </optgroup>
                                </select>
				  </div>
				  <div class="form-group">
					<label for="msrp">MSRP *</label>
					<input type="text" class="form-control" id="msrp" name="msrp" value="<?php echo $vehicle_sellingPrice;?>" required>
				  </div>
				   <div class="form-group">
					<label for="msrp">Zipcode *</label>
					<input type="number" class="form-control" id="zip" name="zip" maxlength="5" value="<?php echo $customer_zipcode;?>"  required>
				  </div>
				  
			</div>	
			<div class="col-md-6">	
			  
					  <div class="form-group">
							<label for="cashdown">Cashdown *</label>
							<input type="number" class="form-control" id="cashdown" name="cashdown" maxlength="5" value="<?php echo $cashDown;?>" required>
						</div>
						
						 <div class="form-group">
							<label for="terms">Terms *</label>
							<select name="terms" class="form-control" required> 
									<option value="72" <?php if($terms==72){?> selected <?php }?>>72</option>
									<option value="60" <?php if($terms==60){?> selected <?php }?>>60</option>
									<option value="48" <?php if($terms==48){?> selected <?php }?>>48</option>
									<option value="39" <?php if($terms==39){?> selected <?php }?>>39</option>
									<option value="36" <?php if($terms==36){?> selected <?php }?>>36</option>
									<option value="27" <?php if($terms==27){?> selected <?php }?>>27</option>
									<option value="24" <?php if($terms==24){?> selected <?php }?>>24</option>
								</select> 
						</div>
						
						 
						
						<div class="form-group">
							<label for="financeSource">Finance Soruce *</label>
								<select name="financeSource" class="form-control" required>
									
									<option value="F00CHC"  <?php if($financeSource=='F00CHC'){?> selected <?php }?>>Chrsler Captial</option> 
									<option value="F000BA" <?php if($financeSource=='F000BA'){?> selected <?php }?>>Ally</option>
								</select> 
						</div>
						<div class="form-group"><input type="hidden" name="form_submit" >
					<label for="TradeIn">TradeIn *</label>
					<input type="text" class="form-control" id="tradeIn" name="tradeIn" value=<?php echo $tradeIn; ?>" required>
				  </div>
				  <div class="form-group"> 
					<label for="TradeIn">Dealer Code *</label>
					<input type="number" class="form-control" id="dealercode" name="dealercode" value="<?php echo $dealercode;?>" required>
				  </div>
			</div>	
			<div class="col-md-12">	
			 <button type="submit" class="btn btn-warning">Submit</button>
			</div>		
			</div>  
				 
	   </form>
	</div>
	
	
	
	
	
	
	 
	
  </div>
</div> 
<script>
	$( document ).ready(function() {
		 $('.btn_lease').on( "click",function(event){ 
			$('.standard_lease').show();
			$('.finance').hide();
		 });
		 
		 $('.btn_finance').on( "click",function(){
			$('.standard_lease').hide();
			$('.finance').show();
		 });
	});
</script>
 
<a name="result"></a>
<div class="container text-center" style="margin:10px;">
  <div class="row">
       <?php
/*************************************************
*  Routeone API Calculator Implementation 
*  Date: 14 / June / 2019	
**************************************************/ 
		 
	function payment_calc($post, $XRouteoneAPIResource, $dealercode)	{
			date_default_timezone_set('Etc/GMT');
			
			$env='production';
			if($env=='production'){	
					/*************  PRODUCTION SETUP ***********/			
				$fullURLString 				= 'https://www.routeone.net'.$XRouteoneAPIResource;
				$XRouteOneActAsDealership 	= $dealercode;//'UQ4RH';
				$CanonicalizedHeaders_Name  = 'x-routeone-act-as-dealership-partner-id';
				$accessKeyId 				= 'F0AFCA';		
			}else{	 
				/*************  STAGING SETUP ***********/
				$fullURLString 				= 'https://itl.routeone.net'.$XRouteoneAPIResource;
				$XRouteOneActAsDealership 	= 'GT5ZW';
				$CanonicalizedHeaders_Name = 'x-routeone-act-as-dealership';	
				$accessKeyId 				= 'F00FCA';	
			}
			
				 		
			$routeoneSecret 			= 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11';
			$XRouteDate 				= date('D, d M Y H:i:s \g\m\t');
			$XRouteContentType 			= 'application/json'; 
			
			echo '<div class="text-primary">******************* DATE ******************* </div>';
			echo $XRouteDate; 
		echo '<div class="text-primary">******************* REQUEST ******************* </div><pre>'; 	 
		print_r($post);
		echo "</pre>";
		
		$json = json_encode($post);
		echo '<div class="text-primary">******************* JSON ******************* </div>'; 
		echo $json;
		$ContentMD5_body = base64_encode(md5($json, true));
		
		#StringToSign Variable assignment 
			$HTTP_VERB               	= "POST"."\n";
			$ContentMD5              	= strtolower($ContentMD5_body)."\n";
			$ContentType             	= $XRouteContentType."\n";
			$Date                    	= strtolower($XRouteDate)."\n";  
			$CanonicalizedResource  	= $XRouteoneAPIResource."\n";
			$CanonicalizedHeaders 		= $CanonicalizedHeaders_Name.':'.$XRouteOneActAsDealership."\n";
			echo '<div class="text-primary">******************* ContentMD5_body ******************* </div>'; 
			echo $ContentMD5_body;	 
       
        # signature=>base64(hmac-sha256({accessKeySecret}, UTF8({stringToSign}))) 				
			#StringToSign Created
			$stringToSign = $HTTP_VERB.$ContentMD5.$ContentType.$Date.$CanonicalizedHeaders.$CanonicalizedResource;		
			$byteArrayStringToSign = utf8_encode($stringToSign);
			echo '<div class="text-primary">******************* SIGN TO STRING ******************* </div>'; 
			echo $byteArrayStringToSign;	 
			 
			#Signature Created	
			$signature_hash 			= hash_hmac('sha256',$byteArrayStringToSign,$routeoneSecret, true);  
			$signature 					= base64_encode($signature_hash);      
		# Authorization Prepared  
			echo '<div class="text-primary">******************* SIGNATURE ******************* </div>';
			echo $signature;  
			
		# Authorization: RouteOne {accessKeyId}:{signature}
			
			$Authorization = "RouteOne $accessKeyId:$signature"; 
			echo '<div class="text-primary">******************* Authorization ******************* </div>';
			 
			echo $Authorization	;	
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
				$CanonicalizedHeaders_Name.": ".$XRouteOneActAsDealership,  
				"content-md5: ".$ContentMD5_body,
				"authorization: ".$Authorization,
			  )); 
			  
			echo '<div class="text-primary">******************* ALL HEADERS ******************* </div>';
			echo 'accept: '.$XRouteContentType.'<br>',
				'content-type: '.$XRouteContentType.'<br>',
				"date: ".$XRouteDate.'<br>',
				$CanonicalizedHeaders_Name.": ".$XRouteOneActAsDealership.'<br>',  
				"content-md5: ".$ContentMD5_body.'<br>',
				"authorization: ".$Authorization;
		#	debugging	
			try{
					$response = curl_exec($ch); 
					$response_arr = json_decode($response, true); 
			echo '<div class="text-primary">******************* RESPONSE FROM ROUTEONE ******************* </div>';				
					echo "<pre>"; 
					print_r($response_arr);
					echo "</pre>";
					curl_close($ch);
					return $response_arr;
			}catch(Exception $e) {
				curl_close($ch);
				return 'Exception Message: ' .$e->getMessage();
			}
        
			  
		}
?>	
  </div>
</div> 
</body>
</html>


<?php
/*************************************************
*  Routeone API Calculator Implementation 
*  Date: 14 / June / 2019	
**************************************************/ 
		//require_once 'swagger_calc_function.php';
		$XRouteoneAPIResource 		= '/customer-quote/finance';  
		if(isset($_POST['form_submit'])){
			$tradeIn = 0;
			if(isset($_POST['tradeIn'])) $tradeIn = $_POST['tradeIn']; else $tradeIn = 0;
			if(isset($_POST['vin'])) $vehicle_vin = $_POST['vin']; else $vehicle_vin = 'ZARFAMAN6K7612678';
			if(isset($_POST['year'])) $vehicle_year = $_POST['year']; else $vehicle_year = '2019'; 
			if(isset($_POST['model'])) $vehicle_model = $_POST['model']; else $vehicle_model = 'STELVIO';  
			if(isset($_POST['msrp'])) $vehicle_sellingPrice = $_POST['msrp']; else $vehicle_sellingPrice = 41480;
			if(isset($_POST['zip'])) $customer_zipcode = $_POST['zip']; else $customer_zipcode = 41480;	

			if(isset($_POST['cashdown'])) $cashDown = $_POST['cashdown']; else $cashDown = 4148;
			if(isset($_POST['terms'])) $terms = $_POST['terms']; else $terms = 36;
			//if(isset($_POST['milesPerYear'])) $milesPerYear = $_POST['milesPerYear']; else $milesPerYear = 10000;
			if(isset($_POST['dealercode'])) $dealercode = $_POST['dealercode']; else $dealercode = 0;
			
			if(isset($_POST['make'])) $make = $_POST['make']; else $make = "ALFA ROMEO";
			
	if(isset($_POST['financeSource'])) $financeSource = $_POST['financeSource']; else $financeSource = 'F000BA';			  
			
			if($financeSource == 'F000BA') $tier = 'S'; else $tier = 1;
			
			$XRouteoneAPIResource_rebateids 		= '/customer-quote/rebates';
		$post_rebateids = array("vehicle" => 
		array(
		'vin' => $vehicle_vin,
						'year' => $vehicle_year,
						'make' => $make,
						'model' => $vehicle_model, 
						'salesClass' => 'NEW',
						'sellingPrice' => $vehicle_sellingPrice,
						'msrp' => $vehicle_sellingPrice),
		'transactionType' => 'finance') ; 
		 
		$response_rebateids = payment_calc($post_rebateids, $XRouteoneAPIResource_rebateids, $dealercode );
		$pre_owner = 'No Previous Ownership Requirement';
		$grp_aff = 'No Specific Group Affiliation';
		$today =  date("Y-m-d");
					
					$man_incentives_id = $return_lesse_id = $military_id = $automobility_id = array();
					$incentive_amount = $incentive_returning_essee =  $incentive_military = $incentive_automobility = 0;
		foreach($response_rebateids as $key => $val){
						if($val['expirationDate'] >= $today && $val['groupAffiliation'] == $grp_aff && $val['previousOwnership'] == $pre_owner){
								array_push($man_incentives_id,$val['incentiveId']);
								$incentive_amount += $val['amount'] ;							
						}
		}						
					
		//\Log::info($response_rebateids);
			
			
			$post = array (
					  'tradeInValue' => $tradeIn,
					  'vehicle' => 
					  array (
						'vin' => $vehicle_vin,
						'year' => $vehicle_year,
						'make' => $make,
						'model' => $vehicle_model, 
						'salesClass' => 'NEW',
						'sellingPrice' => $vehicle_sellingPrice,
						'msrp' => $vehicle_sellingPrice,
					  ),
					  'customer' => 
					  array (
						'address' => 
						array ( 
						  'zipCode' => $customer_zipcode,
						),
					  ), 
					  'markupIndicator' => false,
					  'cashDown' => $cashDown,
					  'financeSource' => $financeSource,//'F00CHC',
					  'tier' => $tier, //'S',//'1',
					   'rebateIds' => $man_incentives_id,
					  'terms' =>   array ( 0 => $terms),
					   
					);
		echo "<h3>customer-quote-resource => /customer-quote/finance</h3>";			
		payment_calc($post, $XRouteoneAPIResource, $dealercode);		
		}else{
		/* Variable Declarations */
		$tradeIn = 0;
		$vehicle_vin = 'ZARFAMAN6K7612678';
		$vehicle_year = '2019';
		$vehicle_model = 'STELVIO';
		$vehicle_sellingPrice = 41480;
		
		$customer_zipcode = 48302;
		
		$cashDown = 4148;
		$terms = 36;
		$milesPerYear = 10000;
		
		$financeSource = 'F000BA';
		
		if($financeSource == 'F000BA') $tier = 'S'; else $tier = 1;
		}
		
		
			
		
?>
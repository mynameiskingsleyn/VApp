<?php
 if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test'){
	 
return array(
/* dev and test */
    'routeone' => array(
             'endpoint' => 'https://www.routeone.net/XRD/turnKeyOcaStart.do?',		   
		   'endpoint_params' => 'rteOneDmsId=F00FCA&fncSrcId=F000ZM&dealerId=GT5ZW',
		   'save_soap_response' => true,
		   'log_soap_response' => true,
		   'dodge_rteOneDmsId' => 'F0DFCA',
		   'fiat_rteOneDmsId' => 'F0FFCA',
		   'ram_rteOneDmsId' => 'F0RFCA',
		   'jeep_rteOneDmsId' => 'F00FCA',
		   'chrysler_rteOneDmsId' => 'F0CFCA',
		   'alfaromeo_rteOneDmsId' => 'F0AFCA',
		   'generic_rteOneDmsId' => 'F0GFCA',
		   'default_rteOneDmsId' => 'F00CHC',
		   'dodge_dealerId' => '45476',
		   'fiat_dealerId' => '45476',
		   'ram_dealerId' => '45476',
		   'jeep_dealerId' => '45476',
		   'chrysler_dealerId' => '45476',
		   'alfaromeo_dealerId' => '26970',
		   'default_dealerId' => '45476',
		   'standard_dealerId' => 'NO',  // YES: ex: config dealerId; NO: DealerID based in VIN
		   
    ),/* dev and test */
	'redirect_time' => array(
		'vehicle_invalid_vin' => 5,
		'sni_invalid_dealer' => 3,
	),/* dev and test */
	'carnow' => array(
		'endpoint' => 'https://perf.carnow.com/api/chat/',
		'key' => 'mvdajwiHD3w4m341BfFsnJaFF81qqrLK',
		'save_json_response' => true,
		'log_json_response' => true,
		'plugin' => 'https://app.carnow.com/dealers/carnow_plugin.js?key=2EwJG1ujFvevJJ87xkjBakeJ526oJxh4rtuyEAnbgscJaxws',
	),/* dev and test */
	'dealerapi' => array(
	'endpoint' => 'https://www.alfaromeousa.com/bdlws/MDLSDealerLocator?brandCode=Y&dealerCode=',		
	),	/* dev and test */
	'dealereliminate' => array(
		'endpoint' => 'http://devore.v2soft.com/api/v1/dealereliminate',
		'save_json_response' => true,
		'log_json_response' => true,
		'username' => 'v@soft!',
		'password' => 'sk8^D@FQNG6EEY',
		't1_activate' => false,   // True: Blocked Dealers, False: No Blocked
		't3_activate' => true,	  // True: Blocked Dealers, False: No Blocked	
		'ore_activate' => true	  // True: Blocked Dealers, False: No Blocked
		
	),/* dev and test */
	'lead' => array(
		'endpoint' => 'https://staging.fcaleads.com/leadservices/leads/post',
		//'endpoint' => 'https://staging.fcaleads.com/leadservices/leads/post',
		'save_json_response' => true,
		'log_json_response' => true,
		'autolead' => 'YES', //YES: Auto Lead send; NO: Auto Lead Not send
		'autolead_interval_time' => 25,  // in minutes
	),	/* dev and test */
	'merkle' => array(
		'endpoint' => 'https://assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c.js',
		'offline_merkle' => true,
	),	/* dev and test */
	'uptime' => array(
		//'to_email' => 'thangavels@v2soft.com',
		//'cc_email' => array('ssingh@v2soft.com'),
		'to_email' => 'fca-ore@v2soft.com',
		'cc_email' => array('sathisha@v2soft.com','nnamperumal@v2soft.com','thangavels@v2soft.com'),
		//'cc_email' => array('nnamperumal@v2soft.com','hsingh@v2soft.com','bchary@v2soft.com','Schandran@v2soft.com'),
		'bcc_email' => '',
		'subject'   => '',
		
	),	/* dev and test */
	'vinmanagement' => array(
		'activate' => true, 
	),	/* dev and test */
	'vindecoder' => array(
		'endpoint' => 'https://www.chrysler.com/veqpws/VehicleEquipmentsServlet.do?vin=VINNUMER&responsemode=S&responseType=JSON',
		'min_year' => 2015,
		'max_year' => 2021,
		'make' => 'alfa', 
	),/* dev and test */
	'ishowroom' => array(
		'endpoint' => "http://ishowroompro-staging.fcaperformanceinstitute.com/CRI.service/api/public/sendOREHotLead",
		'save_json_response' => true,
		'log_json_response' => true,
		'Qrcode' => array(
			'endpoint' => 'https://ishowroompro.fcaperformanceinstitute.com/pro_main/VINChecklistFeatures/VINfeatures?'
		)
	),/* dev and test */
	'vendor_routeone' => 'routeone',
	'vendor_carnow' => 'carnow',
	'vendor_credit700' => 'credit700',
	'ore_sourceid' => 1,
	'routeone_sourceid' => 2,
	'credit700_sourceid' => 3,
	'carnow_sourceid' => 4,
	
	'website_sourceid' => 1054, 		// STANDALONE
	'brand_sourceid' => 1033,			// TIER 1
	'dealer_sourceid' => 1032, 			// TIER 3
	
	'website_businessname' => 'ORE- Search',  		// STANDALONE
	'brand_businessname' => 'ORE - Brand Site', 	// TIER 1
	'dealer_businessname' => 'ORE - Dealer Site', 	// TIER 3
	
	
	'website_sourceid_activate' => true, 	// STANDALONE
	'brand_sourceid_activate' => false,	 	// TIER 1
	'dealer_sourceid_activate' => false, 	// TIER 3
	'session_timeout' => array(
		'timeout' => 900000,    // seconds * 1000
		'popup_timeout'=>60000,
		'message_header' => 'Session Timeout',
		'message' => 'Your session has timed out and you will be automatically redirected to Home Page in 60 seconds.'
	),/* dev and test */
	'default_zipcode' => '48326',
	'calc' => array( 
		'endpoint' => 'https://www.routeone.net',
		'accessKeyId' => 'F0GFCA',	 
		'dodge_accessKeyId' => 'F0DFCA',
		   'fiat_accessKeyId' => 'F0FFCA',
		   'ram_accessKeyId' => 'F0RFCA',
		   'jeep_accessKeyId' => 'F00FCA',
		   'chrysler_accessKeyId' => 'F0CFCA',
		   'alfaromeo_accessKeyId' => 'F0AFCA',	
	    'routeoneSecret' => 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11', 
		'XRouteOneActAsDealership'	=> '99970',
		'XRouteContentType' => 'application/json',
		'lease'=>'/customer-quote/standardLease',
		'finance'=>'/customer-quote/finance',
		'rebates'=>'/customer-quote/rebates',
		'cash'=>'/customer-quote/cash',
		'financeSource'=>'F00CHC',
		'financeName'=>'ChryslerCapital', //ChryslerCapital - AllyFinancialInc
		'tier'=>1,
		'taxes' => false,					// Tax applicable or not.	
		'routeone_tmp_delaercode'=>true,  	// routeone_tmp_delaercode=>Routeone Temp ID getting.
	'incentives_deducted_in_msrp' => false,
		'payment_allocation' => true, 		// payment_allocation=>CCAP / ALLY allocation.
		/** Frequently Updates */
		'incentivesBonusCash' => true,		// incentivesBonusCash=>Select Inventory
		'additioanal_offer'=>true, 			// additioanal_offer=>Additional Discount Text field
		'dealer_discount' => true,			// dealer_discount=>Dealer Discount Module Activation
		/** Frequently Updates */
		'rules' => true,   			 		// rules=>keys restriction as Engine, Invnetory, Etc.
		'total_applicable_terms' => [24,27,36,39,48,60,72,84],
		'lease_applicable_terms' => [24,27,36,39,48,60,72,84],
		'finance_applicable_terms' => [24,36,48,60,72,84],
		'lease_default_terms' => 36,
		'finance_default_terms' => 84,	
	),
	/* dev and test */
	'blackbook' => array(
		'endpoint' => 'https://app.blackbookinformation.com/app/shopping-tools-js/v1.js',
	),/* dev and test */
	'crazyegg' => array(
		'endpoint' => 'https://script.crazyegg.com/pages/scripts/0064/5474.js',
	),/* dev and test */
	
	'etl' => array(
		'make' => array('chrysler','dodge','fiat','jeep','ram'), 
			//'chrysler','dodge','jeep','ram','fiat'
			/** ** **/	
		'make_strings' => '"CHRYSLER","DODGE","FIAT","JEEP","RAM"',
			//'"CHRYSLER","DODGE","JEEP","RAM","FIAT"'
			/** ** **/		
		'vehicle_type' =>  array('new'),
			//'new','cpo'
			/** ** **/	
		'starting_year' => date('Y') - 3,
		'ending_year' 	=> date('Y') + 1,
        'unResolved_models' => [
            'all-new-ram-1500'=>'all-new ram 1500',
            'all new ram 1500' => 'all-new ram 1500'
        ],
        'possible_duplicates' => [
            'ALL-NEW RAM 1500'=>  ['RAM 1500','ALL-NEW RAM 1500']
        ]		
	),
	/* dev and test */
	'disclaimer' => array(
			'disclaimer' => 'jeep disclaimer',
	
           'msrp' => '*MSRP excludes, taxes, title and registration fees. Price refers to launch vehicle selected, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.',
			
			'chrysler_rate' => 'This rate is based on the ‘Term Duration’ selected and available compatible cash incentives. Rate is for well qualified buyers; not all buyers will qualify. Your rate may vary. See dealer for details', 
		   
		   'third_party' => 'If you obtain financing from a third party, enter your financing here. Your total Estimated Amount Financed may be lower due to a Cash Incentive Offer.',		   
		   'lease_against' => 'This value is for estimation purposes only. You may not be able to lease at this rate.',
		   
		   'lease_against_cost' => 'This value is for estimation purposes only. You may not be able to lease at this rate.',

		   'against_cost' => 'This value is for estimation purposes only. You may not be able to lease at this rate.',
           'against_cost_cache'=>'*Est. Net price excludes, taxes, title and registration fees. Price refers to selected vehicle, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.',
		   'against_finance_cost' => 'This value is for estimation purposes only. You may not be able to finance your vehicle at this rate. See dealer for details.',
           'ChryslerCapital'=>'when finance through Chrysler Capital not all buyers will qualify residency restrictions apply see dealer for details.must take retail delivery by ',
		   'incentive' => 'Residency restrictions apply.see dealer for details must take delivery by date',
		   'finance_estimate'=>'This rate is for estimation purpose only you may not be able to finance your vehicle at this rate. see dealer for details.',
		   'annual_milage' => 'Lessee pays for excess wear & tear and mileage for each mile over the annual contracted lease miles, if the vehicle is returned at the end of the term.',
		   
			'payload' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
			
			'towing' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
			
			'off-road' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment and  on available front and rear locking differentials, cab and bed rock rails, disconnecting sway bar, two front and two rear tow hooks, 4:1 Transfer Case, 33" off-road Tires, Skid Plates, and off-road camera.',
			
			'windshield' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',
			
			'doors' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',

			'blind_spot' =>'Always check visually for vehicles prior to changing lanes.',
	
			'adaptive_cruise' =>'Adaptive Cruise Control is a driver’s convenience system, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use the brakes to avoid collisions.',
			
			'forward_collision' =>'This system is solely an alert system for the front of the vehicle that does not take any actions to change vehicle dynamics to avoid a collision, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use brakes to avoid collisions.'
    ),
	
	
); 

 }  else if(env('APP_ENV') == 'staging'){
	 return array( /* Production */
    'routeone' => array(
          'endpoint' => 'https://www.routeone.net/XRD/turnKeyOcaStart.do?',
		   'endpoint_params' => 'rteOneDmsId=F00FCA&fncSrcId=F000ZM&dealerId=GT5ZW',
		   'save_soap_response' => true,
		   'log_soap_response' => true,
		   
		   'dodge_rteOneDmsId' => 'F0DFCA',
		   'fiat_rteOneDmsId' => 'F0FFCA',
		   'ram_rteOneDmsId' => 'F0RFCA',
		   'jeep_rteOneDmsId' => 'F00FCA',
		   'chrysler_rteOneDmsId' => 'F0CFCA',
		   'alfaromeo_rteOneDmsId' => 'F0AFCA',
		   'default_rteOneDmsId' => 'F00CHC',
		   'generic_rteOneDmsId' => 'F0GFCA',

		   'dodge_dealerId' => '45476',
		   'fiat_dealerId' => '45476',
		   'ram_dealerId' => '45476',
		   'jeep_dealerId' => '45476',
		   'chrysler_dealerId' => '45476',
		   'alfaromeo_dealerId' => '26970',
		   'default_dealerId' => '45476',
		   
		    'standard_dealerId' => 'NO',  // YES: ex: config dealerId; NO: DealerID based in VIN
		   
    ), /* Production */
	'redirect_time' => array(
		'vehicle_invalid_vin' => 5,
		'sni_invalid_dealer' => 3,
	), /* Production */
	'carnow' => array(
		'endpoint' => 'https://app.carnow.com/api/chat/',
		'key' => 'vHxh3kGxIkse8woo7oo70d6g4ynHbsLl',
		'save_json_response' => true,
		'log_json_response' => true,
		'plugin' => 'https://app.carnow.com/dealers/carnow_plugin.js?key=2EwJG1ujFvevJJ87xkjBakeJ526oJxh4rtuyEAnbgscJaxws',
	), /* Production */
	'dealerapi' => array(
		'endpoint' => 'https://www.alfaromeousa.com/bdlws/MDLSDealerLocator?brandCode=Y&dealerCode=',		
	),	 /* Production */
	'dealereliminate' => array(
		'endpoint' => 'https://drivealfaromeo.com/api/v1/dealereliminate',
		'save_json_response' => true,
		'log_json_response' => true,
		'username' => 'v@soft!',
		'password' => 'sk8^D@FQNG6EEY',
		't1_activate' => false,   // True: Blocked Dealers, False: No Blocked
		't3_activate' => true,	  // True: Blocked Dealers, False: No Blocked	
		'ore_activate' => true	  // True: Blocked Dealers, False: No Blocked
		
	), /* Production */
	'lead' => array(  
		//	'endpoint' => 'https://staging.fcaleads.com/leadservices/leads/post', 
		'endpoint' => 'https://leadservices.fcaleads.com/leads/post',
		'save_json_response' => true,
		'log_json_response' => true,
		'autolead' => 'YES', //YES: Auto Lead send; NO: Auto Lead Not send
		'autolead_interval_time' => 20,
	), /* Production */
	'merkle' => array(
		'endpoint' => 'https://assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c.js',
		'offline_merkle' => true,
	),	 /* Production */
	'uptime' => array(
		//'to_email' => 'hsingh@v2soft.com',
		//'cc_email' => array('sathisha@v2soft.com', 'nnamperumal@v2soft.com','bchary@v2soft.com','thangavels@v2soft.com','ssingh@v2soft.com','tsonna@v2soft.com','msrikanth@v2soft.com'),
		'to_email' => 'fca-ore@v2soft.com',
		'cc_email' => array('sathisha@v2soft.com','nnamperumal@v2soft.com','thangavels@v2soft.com'),
		'bcc_email' => '',
		'subject'   => '', 
	),	/* Production */
	'vinmanagement' => array(
		'activate' => true, 
	), /* Production */
	'vindecoder' => array(
		'endpoint' => 'https://www.chrysler.com/veqpws/VehicleEquipmentsServlet.do?vin=VINNUMER&responsemode=S&responseType=JSON',
		'min_year' => 2015,
		'max_year' => 2021,
		'make' => 'alfa', 
	), /* Production */
	'ishowroom' => array(
		'endpoint' => "https://ishowroompro.fcaperformanceinstitute.com/CRI.service/api/public/sendOREHotLead",
		//'endpoint' => "http://ishowroompro-staging.fcaperformanceinstitute.com/CRI.service/api/public/sendOREHotLead",
		'save_json_response' => true,
		'log_json_response' => true,
		'Qrcode' => array(
			'endpoint' => 'https://ishowroompro.fcaperformanceinstitute.com/pro_main/VINChecklistFeatures/VINfeatures?'
		)
	), /* Production */
	'vendor_routeone' => 'routeone',
	'vendor_carnow' => 'carnow',
	'vendor_credit700' => 'credit700',
	'ore_sourceid' => 1,
	'routeone_sourceid' => 2,
	'credit700_sourceid' => 3,
	'carnow_sourceid' => 4,
	
	'website_sourceid' => 1054, 		// STANDALONE
	'brand_sourceid' => 1033,			// TIER 1
	'dealer_sourceid' => 1032, 			// TIER 3
	
	'website_businessname' => 'ORE- Search',  		// STANDALONE
	'brand_businessname' => 'ORE - Brand Site', 	// TIER 1
	'dealer_businessname' => 'ORE - Dealer Site', 	// TIER 3
	
	
	'website_sourceid_activate' => true, 	// STANDALONE
	'brand_sourceid_activate' => false,	 	// TIER 1
	'dealer_sourceid_activate' => false, 	// TIER 3
	'session_timeout' => array(
		'timeout' => 900000,    // seconds * 1000
		'popup_timeout'=>60000,
		'message_header' => 'Session Timeout',
		'message' => 'Your session has timed out and you will be automatically redirected to Home Page in 60 seconds.'
	), /* Production */
	'default_zipcode' => '48326',
	'calc' => array(
		'endpoint' => 'https://www.routeone.net',
		'accessKeyId' => 'F0GFCA',	 	
		'dodge_accessKeyId' => 'F0DFCA',
		   'fiat_accessKeyId' => 'F0FFCA',
		   'ram_accessKeyId' => 'F0RFCA',
		   'jeep_accessKeyId' => 'F00FCA',
		   'chrysler_accessKeyId' => 'F0CFCA',
		   'alfaromeo_accessKeyId' => 'F0AFCA',	
	    'routeoneSecret' => 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11',
		//'XRouteOneActAsDealership'	=> 'UQ4RH',
		'XRouteOneActAsDealership'	=> '99970',
		'XRouteContentType' => 'application/json',
		'lease'=>'/customer-quote/standardLease',
		'finance'=>'/customer-quote/finance',
		'rebates'=>'/customer-quote/rebates',
		'cash'=>'/customer-quote/cash',
		'financeSource'=>'F00CHC',
		'financeName'=>'ChryslerCapital', //ChryslerCapital - AllyFinancialInc
		'tier'=>1,
		'taxes' => false, 
		'routeone_tmp_delaercode'=>true,
		'incentives_deducted_in_msrp' => false,
		'payment_allocation' => true,
		/** Frequently Updates */
		'incentivesBonusCash' => true,		// incentivesBonusCash=>Select Inventory
		'additioanal_offer'=>true, 			// additioanal_offer=>Additional Discount Text field
		'dealer_discount' => true,			// dealer_discount=>Dealer Discount Module Activation
		/** Frequently Updates */
		'rules' => true,  
		'payment_allocation' => true,
		'total_applicable_terms' => [24,27,36,39,48,60,72,84],
		'lease_applicable_terms' => [24,27,36,39,48,60,72,84],
		'finance_applicable_terms' => [24,36,48,60,72,84],
		'lease_default_terms' => 36,
		'finance_default_terms' => 84,	
	), /* Production */
	'blackbook' => array(
		'endpoint' => 'https://app.blackbookinformation.com/app/shopping-tools-js/v1.js',
	), /* Production */
	'crazyegg' => array(
		'endpoint' => 'https://script.crazyegg.com/pages/scripts/0064/5474.js',
	), /* Production */ 
	'etl' => array(
		'make' => array('chrysler','dodge','fiat','jeep','ram'), 
			//'chrysler','dodge','jeep','ram','fiat'
			/** ** **/	
		'make_strings' => '"CHRYSLER","DODGE","FIAT","JEEP","RAM"',
			//'"CHRYSLER","DODGE","JEEP","RAM","FIAT"'
		'vehicle_type' =>  array('new'), 
		'starting_year' => date('Y') - 3,
		'ending_year' 	=> date('Y') + 1,
        'unResolved_models' => [
            'all-new-ram-1500'=>'all-new ram 1500',
            'all new ram 1500' => 'all-new ram 1500'
        ],
        'possible_duplicates' => [
            'ALL-NEW RAM 1500'=>  ['RAM 1500','ALL-NEW RAM 1500']
        ]	 
	),
	'disclaimer' => array(
			'disclaimer' => 'jeep disclaimer',
            'msrp' => '*MSRP excludes, taxes, title and registration fees. Price refers to launch vehicle selected, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.',
			
			'chrysler_rate' => 'This rate is based on the ‘Term Duration’ selected and available compatible cash incentives. Rate is for well qualified buyers; not all buyers will qualify. Your rate may vary. See dealer for details.', 
		   
		   'third_party' => 'If you obtain financing from a third party, enter your financing here. Your total Estimated Amount Financed may be lower due to a Cash Incentive Offer.',		   
		   
		   'lease_against' => 'This value is for estimation purposes only. You may not be able to lease at this rate.',
		   
		   'lease_against_cost' => 'This value is for estimation purposes only. You may not be able to lease at this rate.',	
		   'against_cost_cache'=>'*Est. Net price excludes, taxes, title and registration fees. Price refers to selected vehicle, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.',
		   'annual_milage' => 'Lessee pays for excess wear & tear and mileage for each mile over the annual contracted lease miles, if the vehicle is returned at the end of the term.',
		   'against_finance_cost' => 'This value is for estimation purposes only. You may not be able to finance your vehicle at this rate. See dealer for details.',
			'payload' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
			
			'towing' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
			
			'off-road' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment and  on available front and rear locking differentials, cab and bed rock rails, disconnecting sway bar, two front and two rear tow hooks, 4:1 Transfer Case, 33" off-road Tires, Skid Plates, and off-road camera.',
			
			'windshield' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',
			
			'doors' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',

			'blind_spot' =>'Always check visually for vehicles prior to changing lanes.',
	
			'adaptive_cruise' =>'Adaptive Cruise Control is a driver’s convenience system, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use the brakes to avoid collisions.',
			
			'forward_collision' =>'This system is solely an alert system for the front of the vehicle that does not take any actions to change vehicle dynamics to avoid a collision, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use brakes to avoid collisions.'
    ),
	'discounts' => array( 
		'maxAmount5000Allowed' => false	
	)
	
);
 }else{
	 /* else --- unkown --- */
	 if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test'){
		return array(
			'routeone' => array(
				   'endpoint' => 'https://itl.routeone.net/XRD/turnKeyOcaStart.do?',		   
				   'endpoint_params' => 'rteOneDmsId=F00FCA&fncSrcId=F000ZM&dealerId=GT5ZW',
				   'save_soap_response' => true,
				   'log_soap_response' => true,
				   
		    'dodge_rteOneDmsId' => 'F0DFCA',
		    'fiat_rteOneDmsId' => 'F0FFCA',
		    'ram_rteOneDmsId' => 'F0RFCA',
		    'jeep_rteOneDmsId' => 'F00FCA',
		    'chrysler_rteOneDmsId' => 'F0CFCA',
		    'alfaromeo_rteOneDmsId' => 'F0AFCA',
		    'default_rteOneDmsId' => 'F00CHC',
		   'generic_rteOneDmsId' => 'F0GFCA',
		   
		    'dodge_dealerId' => '45476',
		    'fiat_dealerId' => '45476',
		    'ram_dealerId' => '45476',
		    'jeep_dealerId' => '45476',
		    'chrysler_dealerId' => '45476',
		    'alfaromeo_dealerId' => '26970',
		    'default_dealerId' => '45476',
		   
		    'standard_dealerId' => 'NO',  // YES: ex: config dealerId; NO: DealerID based in VIN
				   
			),/* else --- unkown --- */
			'redirect_time' => array(
				'vehicle_invalid_vin' => 5,
				'sni_invalid_dealer' => 3,
			),
			'carnow' => array(
				'endpoint' => 'https://perf.carnow.com/api/chat/',
				'key' => 'mvdajwiHD3w4m341BfFsnJaFF81qqrLK',
				'save_json_response' => true,
				'log_json_response' => true,
				'plugin' => 'https://app.carnow.com/dealers/carnow_plugin.js?key=2EwJG1ujFvevJJ87xkjBakeJ526oJxh4rtuyEAnbgscJaxws',
			),/* else --- unkown --- */
			'dealerapi' => array(
			'endpoint' => 'https://www.alfaromeousa.com/bdlws/MDLSDealerLocator?brandCode=Y&dealerCode=',		
			),	
			/* else --- unkown --- */
			'dealereliminate' => array(
				'endpoint' => 'http://devore.v2soft.com/api/v1/dealereliminate',
				'save_json_response' => true,
				'log_json_response' => true,
				'username' => 'v@soft!',
				'password' => 'sk8^D@FQNG6EEY',
				't1_activate' => false,   // True: Blocked Dealers, False: No Blocked
				't3_activate' => true,	  // True: Blocked Dealers, False: No Blocked	
				'ore_activate' => true	  // True: Blocked Dealers, False: No Blocked
				
			),/* else --- unkown --- */
			'lead' => array(
				'endpoint' => 'https://staging.fcaleads.com/leadservices/leads/post',
				'save_json_response' => true,
				'log_json_response' => true,
				
			),	
			'merkle' => array(
				'endpoint' => 'https://assets.adobedtm.com/e2b27ccc0e522eb7c0afb12eb12ee852c39ccceb/satelliteLib-5dd94bf97d100eedc2ee143eff9743a1eb65a09c.js',
				'offline_merkle' => false,
			),	/* else --- unkown --- */
			'uptime' => array(
				'to_email' => 'sathisha@v2soft.com',
				'cc_email' => array('thangavels@v2soft.com'),
				//'cc_email' => array('nnamperumal@v2soft.com','hsingh@v2soft.com','bchary@v2soft.com','Schandran@v2soft.com'),
				'bcc_email' => '',
				'subject'   => '',
				
			),	/* else --- unkown --- */
			'vindecoder' => array(
				'endpoint' => 'https://www.chrysler.com/veqpws/VehicleEquipmentsServlet.do?vin=VINNUMER&responsemode=S&responseType=JSON',
				'min_year' => 2015,
				'max_year' => 2021,
				'make' => 'alfa', 
			),
			'ishowroom' => array(
				'endpoint' => "http://ishowroompro-staging.fcaperformanceinstitute.com/CRI.service/api/public/sendOREHotLead",
				'save_json_response' => true,
				'log_json_response' => true,
				'Qrcode' => array(
					'endpoint' => 'https://ishowroompro.fcaperformanceinstitute.com/pro_main/VINChecklistFeatures/VINfeatures?'
				)
			),/* else --- unkown --- */
			'vendor_routeone' => 'routeone',
			'vendor_carnow' => 'carnow',
			'vendor_credit700' => 'credit700',
			'ore_sourceid' => 1,
			'routeone_sourceid' => 2,
			'credit700_sourceid' => 3,
			'carnow_sourceid' => 4,
			
			'website_sourceid' => 1054, 		// STANDALONE
			'brand_sourceid' => 1033,			// TIER 1
			'dealer_sourceid' => 1032, 			// TIER 3
			
			'website_businessname' => 'ORE- Search',  		// STANDALONE
			'brand_businessname' => 'ORE - Brand Site', 	// TIER 1
			'dealer_businessname' => 'ORE - Dealer Site', 	// TIER 3
			/* else --- unkown --- */
			
			'website_sourceid_activate' => true, 	// STANDALONE
			'brand_sourceid_activate' => false,	 	// TIER 1
			'dealer_sourceid_activate' => false, 	// TIER 3
			'session_timeout' => array(
				'timeout' => 900000,    // seconds * 1000
				'popup_timeout'=>60000,
				'message_header' => 'Session Timeout',
				'message' => 'Your session has timed out and you will be automatically redirected to Home Page in 60 seconds.'
			),/* else --- unkown --- */
			'default_zipcode' => '48326',
			'calc' => array(
				/*'endpoint' => 'https://itl.routeone.net', 
				'accessKeyId' => 'F00FCA',	
				'routeoneSecret' => 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11',
				'XRouteOneActAsDealership'	=> 'GT5ZW',
				'XRouteContentType' => 'application/json',
				'lease'=>'/customer-quote/standardLease',
				'finance'=>'/customer-quote/finance',
				'rebates'=>'/customer-quote/rebates',
				'cash'=>'/customer-quote/cash',
				'financeSource'=>'F00CHC',
				'tier'=>1,
				'taxes' => false*/	
				'endpoint' => 'https://www.routeone.net',
				'accessKeyId' => 'F0AFCA',	 	
				'routeoneSecret' => 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11', 
				'XRouteOneActAsDealership'	=> '99970',
				'XRouteContentType' => 'application/json',
				'lease'=>'/customer-quote/standardLease',
				'finance'=>'/customer-quote/finance',
				'rebates'=>'/customer-quote/rebates',
				'cash'=>'/customer-quote/cash',
				'financeSource'=>'F00CHC',
				'tier'=>1,
				'taxes' => false,
				'incentives_deducted_in_msrp' => true,
				'payment_allocation' => true,
				'total_applicable_terms' => [24,27,36,39,48,60,72,84],
				'lease_applicable_terms' => [24,27,36,39,48,60,72,84],
				'finance_applicable_terms' => [24,36,48,60,72,84],
				'lease_default_terms' => 36,
				'finance_default_terms' => 84,	
			),/* else --- unkown --- */
			
			'blackbook' => array(
				'endpoint' => 'https://app.blackbookinformation.com/app/shopping-tools-js/v1.js',
			),
			'crazyegg' => array(
				'endpoint' => 'https://script.crazyegg.com/pages/scripts/0064/5474.js',
			),
			/* else --- unkown --- */
			'disclaimer' => array(
					'disclaimer' => 'jeep disclaimer',
			
				   'msrp' => '*MSRP excludes, taxes, title and registration fees. Price refers to launch vehicle selected, including destination charge. Pricing and offers may change at any time without notification. To get full pricing details, see your dealer.',
					
					'chrysler_rate' => 'This rate is based on the ‘Term Duration’ selected and available compatible cash incentives. Rate is for well qualified buyers; not all buyers will qualify. Your rate may vary. See dealer for details', 
				   
				   'third_party' => 'If you obtain financing from a third party, enter your financing here. Your total Estimated Amount Financed may be lower due to a Cash Incentive Offer.',		   
				   
				   'against_cost' => 'This rate is for estimation purposes only. You may not be able to finance your vehicle at this rate. See dealer for details.',
				   'annual_milage' => 'Lessee pays for excess wear & tear and mileage for each mile over the annual contracted lease miles, if the vehicle is returned at the end of the term.',
					'payload' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
					
					'towing' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment.',
					
					'off-road' =>'Based on the FCA US LLC midsize 4x4 crew-cab truck segment and  on available front and rear locking differentials, cab and bed rock rails, disconnecting sway bar, two front and two rear tow hooks, 4:1 Transfer Case, 33" off-road Tires, Skid Plates, and off-road camera.',
					
					'windshield' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',
					/* else --- unkown --- */
					'doors' =>'Be sure to follow all instructions in owners manual for removal of top, doors and lowering windshield.',

					'blind_spot' =>'Always check visually for vehicles prior to changing lanes.',
			
					'adaptive_cruise' =>'Adaptive Cruise Control is a driver’s convenience system, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use the brakes to avoid collisions.',
					
					'forward_collision' =>'This system is solely an alert system for the front of the vehicle that does not take any actions to change vehicle dynamics to avoid a collision, not a substitute for active driver involvement.  The driver must remain aware of traffic conditions and be prepared to use brakes to avoid collisions.'
			),/* else --- unkown --- */
	
	
		); 

	} // ELSE
 }
<?php
 return array(
/* dev and test */
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

		   
		   'dodge_dealerId' => 'WA1LG',
		   'fiat_dealerId' => 'XR0OS',
		   'ram_dealerId' => 'ET4JG',
		   'chrysler_dealerId' => 'GZ3CD',
		   'alfaromeo_dealerId' => '26970',
		   'default_dealerId' => 'OL8QC',
		   
		    'standard_dealerId' => 'YES',  // YES: ex: alfaromeo_dealerId; NO: DealerID based in VIN
		   
    ),/* dev and test */
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
		'taxes' => false,
		'dealer_discount' => true,			// Dealer Discount Module Activation
		'routeone_tmp_delaercode'=>true,  // Routeone Temp ID getting.
		'incentives_deducted_in_msrp' => false,
		'payment_allocation' => true, 
		'incentivesBonusCash' => true,
		'additioanal_offer'=>false,
		'rules' => true,  
		'total_applicable_terms' => [24,27,36,39,48,60,72,84],
		'lease_applicable_terms' => [24,27,36,39,48,60,72,84],
		'finance_applicable_terms' => [24,36,48,60,72,84],
		'lease_default_terms' => 36,
		'finance_default_terms' => 84,	
	), 	
	'discounts' => array( 
		'maxAmount5000Allowed' => false	
	)
); 
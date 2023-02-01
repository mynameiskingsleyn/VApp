<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Aacotroneo\Saml2\Http\Controllers\Saml2Controller;

class MySaml2Controller extends Saml2Controller
{
    
	  public function x(Request $request) {
			$xmlPayload = base64_decode($request->SAMLResponse);
			 
			$doc = new \DOMDocument();
			$doc->loadXML($xmlPayload);
			 
			foreach ($doc->getElementsByTagName('Attribute') as $attribute) {
				echo $attribute->getAttribute('Name')." - ". $attribute->nodeValue;
			}
	} 

	
   public function SamlLogin()
    {
        $loginRedirect = '/dashboard'; // Determine redirect URL
        $this->saml2Auth->login($loginRedirect);
    }  
	
	 
}

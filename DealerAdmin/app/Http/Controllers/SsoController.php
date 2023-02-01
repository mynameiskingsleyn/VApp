<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SsoController extends Controller
{
	protected $DealerCode;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }


    public function sso(Request $request){
        $xmlPayload = base64_decode($request->SAMLResponse);
        $doc = new \DOMDocument();
        $doc->loadXML($xmlPayload);
         
        $request->session()->flush();
        foreach ($doc->getElementsByTagName('Attribute') as $attribute) {
        	$attribute_name  = $attribute->getAttribute('Name');
        	$attribute_value  = $attribute->nodeValue;
        	if('DealerCode' == $attribute_name){
        		$this->DealerCode = $attribute_value;
        		break;
        	}
        }
        $request->session()->forget('logout');
        $request->session()->put('DealerAdmin.DealerCode',$this->DealerCode);
        $request->session()->put('DealerCode',$this->DealerCode);
        return redirect('inventory');
    } 

}

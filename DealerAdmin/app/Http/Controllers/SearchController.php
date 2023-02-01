<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str;
use Validator;

use App\User; 

use App\Model\Vehicle;

use App\Model\Category;

use App\Model\Discount;

use App\Model\Vindiscount;

use App\Model\Financediscount;

use App\Model\Vinactivation;

use App\Traits\CacheTrait;
use App\Traits\APIRequestTrait;

use App\Http\Requests\FilterRequest;

class SearchController extends Controller
{
	protected $DealerCode,$DealerName,$ZipCode;
    public function __construct(Category $modelCategory, Vehicle $modelVehicle){
    	
		$this->modelCategory = $modelCategory;
		$this->modelVehicle = $modelVehicle;
		/*if(!config('dealeradmin.dc.login')){
			$this->middleware('auth');
		}*/
	}  
	
 
	public function dealerDiscounts(Request $request) {
		if(!$this->checkSessionredirectErrorPage()){
			return view('error.unauthorized');
		}
		if(\Session::exists('logout') && \Session::get('logout')){
			//\Session::forget('logout');
         	return view('error.logoutsuccess');
         }
		
		$tabname = 'dealerdiscounts';
		$this->checkAuthorizeDealer();
		return view('dealer-admin', ['dealer_name' => $this->DealerName, 'zipcode'=>$this->ZipCode, 'tabname' => 'dealerdiscounts']);
	}

	public function dealerAutomatedDiscounts() {
		if(!$this->checkSessionredirectErrorPage()){
			return view('error.unauthorized');
		}

		if(\Session::exists('logout') && \Session::get('logout')){
			//\Session::forget('logout');
         	return view('error.logoutsuccess');
         }
		//$user = Auth::user();
		$tabname = 'dealerautomateddiscounts';
		$this->checkAuthorizeDealer();
		return view('dealer-admin', ['dealer_name' => $this->DealerName, 'zipcode'=>$this->ZipCode, 'tabname' => 'dealerautomateddiscounts']);
	}

	public function ssologout(Request $request) {
        \Session::put('logout',1);
		\Session::forget('DealerCode');
        \Session::forget('DealerAdmin');
        \Session::forget('DealerName');
		\Session::forget('ZipCode');
		\Session::forget('dealertype');
		 
	}

	public function checkAuthorizeDealer()
	{
		//if(config('dealeradmin.dc.login')){
			$DealerAdmin = \Session::get('DealerAdmin');
			$this->DealerCode = $DealerAdmin['DealerCode'];
		/*}else{
			$this->DealerCode = \Auth::User()->email;
			$DealerAdmin = array('DealerCode'=>$this->DealerCode);
			\Session::put('DealerCode',$this->DealerCode);
			\Session::put('DealerAdmin',$DealerAdmin);
		}*/

		$dealer_exists = \DB::table('fca_ore_dealer_info')->where('dlr_code',$this->DealerCode)->get();
        if($dealer_exists->isEmpty()){
            return view('error.unauthorized');
        }
        $dealer_name =  $this->getDealerName();
        $this->DealerName =  !empty($dealer_name)? $dealer_name : '';
		$zipcode =  $this->getDealerZipCode();
        $this->ZipCode =  !empty($zipcode)? $zipcode : '';
		\Session::put('DealerName',$this->DealerName);
		\Session::put('ZipCode',$this->ZipCode);
	}

	public function getDealerName()
	{
		return \DB::table('fca_ore_dealer_info')->where('dlr_code',$this->DealerCode)->value('dlr_dba_name');
	}

	public function getDealerZipCode()
	{
		return \DB::table('fca_ore_dealer_info')->where('dlr_code',$this->DealerCode)->value('dlr_shw_zip');
	}

	public function checkSessionredirectErrorPage()
	{
		if (\Session::exists('logout')) {
			return true;
		}
		\Session::forget('logout');
		\Log::info('Session checking');
        \Log::info('Session All Value::');
         \Log::info(\Session::all());   
		if (!\Session::exists('DealerAdmin')) {
            // user value cannot be found in session
            \Log::info('Session:: DealerAdmin Key is not exists.Response unauthorized');
            $this->ClearSessionKeys();
            return false;
        }

       $DealerAdmin = \Session::get('DealerAdmin');
        if (!array_key_exists('DealerCode', $DealerAdmin)){
            // user value cannot be found in session
             \Log::info('Session:: DealerAdmin-DealerCode Key is not exists. Response unauthorized');
             $this->ClearSessionKeys();
            return false;
        }
        $DealerCode = $DealerAdmin['DealerCode'];
        if (!empty($DealerCode)) {
        	$dealer_exists = \DB::table('fca_ore_dealer_info')->where('dlr_code', $DealerCode)->get();
            if ($dealer_exists->isEmpty()) {
                \Log::info('Session:: Invalid DealerCode in fca_ore_dealer_info.Response unauthorized');
                $this->ClearSessionKeys();
                return false;
            }
            \Log::info('Session:: DealerCode exists in fca_ore_dealer_info');
            //$mdoca_exists = \DB::table('fca_ore_mdoca')->where('dlr_code', $DealerCode)->first();
/*            $mdoca_exists = \DB::table('dealer_access')->where('dealer_code', $DealerCode)->first();
            if (is_null($mdoca_exists)) {
                \Log::info('Session:: Invalid DealerCode in dealer_access.Response unauthorized');
                $this->ClearSessionKeys();
                 return false;
            }
            \Log::info('Session:: DealerCode exists in dealer_access');*/
            $eliminate_exists = \DB::table('fca_ore_dealer_eliminate')->where('dlr_code', $DealerCode)->first();
            if (!is_null($eliminate_exists)) {
                \Log::info('Session:: DealerCode exists in fca_ore_dealer_eliminate.Response unauthorized');
                $this->ClearSessionKeys();
                return false;
            }
            \Log::info('Session:: DealerCode not exists in fca_ore_dealer_eliminate');
        } else {
            \Log::info('Session:: Invalid DealerCode.Response unauthorized');
            $this->ClearSessionKeys();
            return false;
        }
        \Log::info('Session checking successfully');
        return true;
	}

	public function ClearSessionKeys()
    {
        \Session::forget('DealerCode');
        \Session::forget('DealerAdmin');
        \Session::forget('DealerName');
        \Session::forget('ZipCode');
	}
	
	public function SwitchConnection(Request $request)
	{
	  
	   \Session::put('dealertype', $request->dealertype);
 
	}
}

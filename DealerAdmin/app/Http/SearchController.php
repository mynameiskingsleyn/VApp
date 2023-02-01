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

use App\Traits\CacheTrait;
use App\Traits\APIRequestTrait;

use App\Http\Requests\FilterRequest;

class SearchController extends Controller
{
    public function __construct(Category $modelCategory, Vehicle $modelVehicle){
    	$this->middleware('auth');
		$this->modelCategory = $modelCategory;
		$this->modelVehicle = $modelVehicle;

	}  
 
	public function dealerAdmin($DealerCode = 69009) {
		/*$vehicle 		= $this->modelVehicle
							->select('make', 'year', 'model','trim_desc')
							->where(['vehicle_type' => 'new', 'dealer_code' => $DealerCode])
							->groupBy('trim_desc')->get();
		if(!$vehicle->isEmpty()){
			$dropdown_list = array();
			$data = $vehicle->toArray();
			foreach ($data as $key => $value) {
				$make = $value['make'];
				$year = $value['year'];
				$model = $value['model'];
				$trim = $value['trim_desc'];
				if(!array_key_exists($make, $dropdown_list)){
					$dropdown_list[$make] = array();
				}
				if(!array_key_exists($year, $dropdown_list[$make])){
					$dropdown_list[$make][$year] = array();
				}
				if(!array_key_exists($model, $dropdown_list[$make][$year])){
					$dropdown_list[$make][$year][$model] = array();
				}
				if(!in_array($trim, $dropdown_list[$make][$year][$model])){
					array_push($dropdown_list[$make][$year][$model],$trim);					
				}
			}
		//echo '<pre>';print_r($dropdown_list);exit;
		}*/
		$dealer_name = '';
		$user = Auth::user();
		if($user){
			$dealer_name =  \DB::table('fca_ore_dealer_info')->where('dlr_code',$user->email)->value('dlr_dba_name');
		}
		session(['dealer_name' => $dealer_name]);
		return view('dealer-admin', ['dealer_name' => $dealer_name]);
	}
}

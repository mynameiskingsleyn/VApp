<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

use Validator;

use Nahid\JsonQ\Jsonq;

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

use App\Helpers\FilterHelper;

class FilterController extends Controller
{
    use CacheTrait, APIRequestTrait;

	public $successStatus = 200;
	protected $modelCategory, $modelVehicle;
	private $cache_prefix = "da:";
	private $DealerCode;
	private $jsonInventory;
	private $fields ;

	public function __construct(Category $modelCategory, Vehicle $modelVehicle){
		$this->modelCategory = $modelCategory;
		$this->modelVehicle = $modelVehicle;
		$this->fields = ['city_mpg','exterior_color_code','exterior_meta_color_desc','hwy_mpg','interior_fabric','model_desc','towing_capacity','wheel_base'];
		//$this->DealerCode = 68888;
        $this->CacheFlushAll();
        $this->filterHelper = new FilterHelper();
	}

	public function init(){
		//$this->DealerCode = 68888;
		$cacheName = 'api:response:dealer:inventory:'.$this->DealerCode;
		if($this->hexists($cacheName,$this->DealerCode)){
			$result_json 	= $this->isCacheHMGet($cacheName,$this->DealerCode);
			$Inventory 		= json_decode($result_json[0], true);
			$this->jsonInventory = new Jsonq($Inventory);
		}else{
			$query = $this->modelVehicle->where(['dealer_code' => $this->DealerCode, 'vehicle_type' => 'new'])->select('make','model','model_desc','year','trim_desc','trim_code','vin','dealer_code','drive_type','exterior_color_code','eng_desc','transmission_desc','msrp','vehicle_type')->get();
			$string_to_array = json_decode($query, true);
			\Log::info($string_to_array);
			$Inventory['Inventory']=$string_to_array;
			$this->isCacheHMSet($cacheName, $this->DealerCode, json_encode($Inventory));
			$this->jsonInventory = new Jsonq($Inventory);
		}
	}

	public function primaryMake(){
		$result = $this->jsonInventory->from('Inventory')->select('make')->groupBy('make')->get();
		$results  =array_keys($result);
		$NameOfAPI = "getMakeFilter";
		$cacheName = 'api:response:'.$NameOfAPI.':'.$this->DealerCode;
		$results2 = $this->traitFilterMakes($results, $NameOfAPI, $this->DealerCode,$cacheName);
		return response()->json($results2, $this->successStatus);
	}

	public function primaryYear($make){
		$result = $this->jsonInventory->from('Inventory')->select('year')
									->where('make', '=', strtoupper($make))
									->groupBy('year')
									->get();
									$results  =array_keys($result);
									return response()->json($results, $this->successStatus);
	}

	public function primaryModelYear($make, $year){
		$result = $this->jsonInventory->from('Inventory')->select('model')
									->where('make', '=', strtoupper($make))
									->where('year', '=', $year)
									->groupBy('model')
									->get();
									$results  =array_keys($result);
									return response()->json($results, $this->successStatus);
	}

	public function primaryTrimSelection($make, $year, $model){
		$result = $this->jsonInventory->from('Inventory')->select('trim_desc')
									->where('make', '=', strtoupper($make))
									->where('year', '=', $year)
									->where('model', '=', strtoupper($model))
									->groupBy('trim_desc')
									->get();
		$results  =array_keys($result);
		return response()->json($results, $this->successStatus);
	}

	public function primaryMsrp($make, $year, $model, $trim){
		$result = $this->jsonInventory->from('Inventory')->select('msrp')
									->where('make', '=', strtoupper($make))
									->where('year', '=', $year)
									->where('model', '=', strtoupper($model))
									->where('trim_desc', '=', strtoupper($trim))
									->max('msrp');
		$results  =array_keys($result);
		return response()->json($results, $this->successStatus);
	}

	public function getAllMakes(FilterRequest $request) {
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$this->DealerCode = $DealerCode;
		$this->init();
		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode;
		if($this->hexists($cacheName,$DealerCode))
		{
			$result_json 	= $this->isCacheHMGet($cacheName,$DealerCode);
			$result 		= json_decode($result_json[0], true);
		}
		else
		{
			$getJsonResult = $this->jsonInventory->from('Inventory')->select('make')->groupBy('make')->get();
			$results  =array_keys($getJsonResult);
			$result = $this->traitFilterMakes($results, $NameOfAPI, $this->DealerCode,$cacheName);
		}
		return response()->json($result, $this->successStatus);
	}


	/***
	*  Received All Years
	*/
	public function getModelYear(FilterRequest $request) {
		//\Log::info(Auth::User()->email);
		//Validate Request Params
        $validator 		= $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$MakeCode	 	= $request->get('MakeCode');
		$this->DealerCode = $DealerCode;
		$this->init();
		$make		 	= $this->getMakeAbbrevationName($MakeCode);

		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode.':'.str_replace(' ', '-', $make);
		if($this->hexists($cacheName,$DealerCode)){
			$result_json 	= $this->isCacheHMGet($cacheName,$DealerCode);
			$result 		= json_decode($result_json[0], true);
		} else{
			$result = $this->jsonInventory->from('Inventory')->select('year')
									->where('make', '=', strtoupper($make))
									->groupBy('year')
									->get();
			$vehicle  =array_keys($result);
			$result 		= $this->traitModelYear($vehicle, $NameOfAPI, $DealerCode, $MakeCode, $cacheName);
		}

		return response()->json($result, $this->successStatus);
	}

	/***
	*  Received All Models
	*/
	public function getVehicleSelection(FilterRequest $request) {
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$MakeCode	 	= $request->get('MakeCode');
		$ModelYear	 	= $request->get('ModelYear');
		$this->DealerCode = $DealerCode;
		$this->init();
		$Make		 	= $this->getMakeAbbrevationName($MakeCode);

		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode.':'.str_replace(' ', '-', $Make).':'.$ModelYear;
		if($this->hexists($cacheName,$DealerCode)){
			$result_json 	= $this->isCacheHMGet($cacheName,$DealerCode);
			$result 		= json_decode($result_json[0], true);
		} else{
		$result = $this->jsonInventory->from('Inventory')->select('model')
									->where('make', '=', strtoupper($Make))
									->where('year', '=', $ModelYear)
									->groupBy('model')
									->get();
				$vehicle  =array_keys($result);

			$result 		= $this->traitGetModel($vehicle, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $cacheName);
		}
 		return response()->json($result, $this->successStatus);
	}

	/***
	*	Received all trims
	*/
	public function FilterTrimSelection(FilterRequest $request) {
		\DB::connection()->enableQueryLog();
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$MakeCode	 	= $request->get('MakeCode');
		$ModelYear	 	= $request->get('ModelYear');
		$Model	 		= $request->get('Model');
		$this->DealerCode = $DealerCode;
		$this->init();
		$Make		 	= $this->getMakeAbbrevationName($MakeCode);

		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode.':'.str_replace(' ', '-', $Make).':'.$ModelYear.':'.Str::slug($Model);
	 	if($this->hexists($cacheName,$DealerCode)){
			$result_json 	= $this->isCacheHMGet($cacheName,$DealerCode);
			$result 		= json_decode($result_json[0], true);
		}
		else{
			$result = $this->jsonInventory->from('Inventory')//->select('trim_code')
													->where('make', '=', strtoupper($Make))
													->where('year', '=', $ModelYear)
													->where('model', '=', $Model)
													->groupBy('trim_code')
													->get();

			$trims = $this->filterHelper->extractTrimsFromCodeList($result,'trim_code','trim_desc');
			//$trim = array_unique($trims);
            \Log::debug('here are the selected trims result on get');
            \Log::debug($trims);
//			$vehicle  =array_keys($result);
//            \Log::debug('here are the selected trims after array keys');
//            \Log::debug($vehicle);
            \Log::info('end of check');
			$result 		= $this->traitFilterTrimSelection($trims, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $cacheName);
		}
		return response()->json($result, $this->successStatus);
	}

	/***
	*	Received all trims
	*/
	public function FilterMsrpSelection(FilterRequest $request) {
		\DB::connection()->enableQueryLog();
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$MakeCode	 	= $request->get('MakeCode');
		$ModelYear	 	= $request->get('ModelYear');
		$Model	 		= $request->get('Model');
		$Trim	 		= $request->get('Trim');
		\Log::info('Trims for sorting in msrp are---------------------------------->');
		\Log::debug($Trim);
		$this->DealerCode = $DealerCode;
		$this->init();
		$string_Trim = implode(',', $Trim);
		$Make		 	= $this->getMakeAbbrevationName($MakeCode);
		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode.':'.str_replace(' ', '-', $Make).':'.$ModelYear.':'.Str::slug($Model).':'.Str::slug($string_Trim);
		\Log::info(' --- cacheName ---');
		if($this->hexists($cacheName,$DealerCode)){
			\Log::info(' --- cacheName Already Set so Bring from Cache Value---');
			$result_json 	= $this->isCacheHMGet($cacheName,$DealerCode);
			$result 		= json_decode($result_json[0], true);
			\Log::info("Msrp Result from cache $cacheName");
			\Log::debug($result);
		}
		else{
			\Log::info(' --- cacheName not Set so set the cache value---');
			\Log::info($cacheName);
			$mxresult = $this->jsonInventory->from('Inventory')->select('msrp')
													->whereIn('trim_code', $Trim)
													->where('make', '=', strtoupper($Make))
													->where('year', '=', $ModelYear)
													->where('model', '=', $Model)
													//->where('trim_desc', '=', $Trim)
													->max('msrp');
			$miresult = $this->jsonInventory->reset()->from('Inventory')->select('msrp')
													->where('make', '=', strtoupper($Make))
													->where('year', '=', $ModelYear)
													->where('model', '=',$Model)
													->whereIn('trim_code', $Trim)
													//->where('trim_desc', '=', $Trim)
													->min('msrp');
			$msrp_values = array(
				'msrp_highest'=>$mxresult,
				'msrp_lowest'=>$miresult
			);
			\Log::info('fresh result on getting MSRP');
			\Log::debug($msrp_values);
			$result = $this->traitFilterMsrpSelection($msrp_values, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $string_Trim, $cacheName);
		}
		return response()->json($result, $this->successStatus);
	}

	/***
	*	Received all Secondary Works.
	*/
	public function FilterSecondarySelection(FilterRequest $request) {
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 	= $request->get('NameOfAPI');
		$DealerCode 	= $request->get('DealerCode');
		$MakeCode	 	= $request->get('MakeCode');
		$ModelYear	 	= $request->get('ModelYear');
		$Model	 		= $request->get('Model');
		$Trim	 		= $request->get('Trim');
        \Log::info('Trims for sorting are---------------------------------->');
        \Log::debug($Trim);

		$string_Trim = implode(',', $Trim);
		$MsrpHighest	= $request->get('MsrpHighest');
		$MsrpLowest	 	= $request->get('MsrpLowest');
		$this->DealerCode = $DealerCode;
		$this->init();
		$Make		 	= $this->getMakeAbbrevationName($MakeCode);
		$cacheName = 'api:response:'.$NameOfAPI.':'.$DealerCode.':'.str_replace(' ', '-', $Make).':'.$ModelYear.':'.Str::slug($Model).':'.Str::slug($string_Trim).':'.Str::slug($MsrpHighest).':'.Str::slug($MsrpLowest);
			\Log::info(' --- cacheName ---');
			\Log::info(' --- cacheName not Set so set the cache value---');
			\Log::info($cacheName);
			$result = $this->jsonInventory->from('Inventory')->where('make', '=', strtoupper($Make))
													->where('year', '=', $ModelYear)
													->where('model', '=', $Model)
													->whereIn('trim_code', $Trim)
													//->where('trim_desc', '=', $Trim)
													->get();
	        $myarray=['drive_type','exterior_color_code','eng_desc','transmission_desc','msrp'];
	        foreach($myarray as $v){
	                $column_values =  array_unique(array_column($result, $v));
	                sort($column_values);
	                $output[$v] =  $column_values;
	        }
	        $exterior_color_code = array();
	        foreach($output['exterior_color_code'] as $color_code){
	        	$exterior_color_code[$color_code] = \DB::table('fca_ore_options')->where('options_cd',$color_code)->value('options_desc');
	        }

			uasort($exterior_color_code, function($a, $b) {
			        return ($a == $b) ? 0 : (($a < $b) ? -1 : 1);
			});
	        $output['exterior_color_code'] = $exterior_color_code;
			$result 		= $this->traitFilterSecondarySelection($output, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $string_Trim, $MsrpHighest, $MsrpLowest, $cacheName);
		return response()->json($result, $this->successStatus);
	}
}

<?php

namespace App\Http\Controllers\Api;

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
//use App\Model\Dlrmgvinactivation;

use App\Traits\CacheTrait;

use App\Traits\APIRequestTrait;

use App\Http\Requests\SearchRequest;

class ListController extends Controller
{
    use CacheTrait, APIRequestTrait;

	private $dealer_code;
	public $successStatus = 200;
	private $cache_prefix = "da:";

    protected $modelCategory, $modelVehicle,$modelDiscount,$modelVinDiscount,$modelVinManagement,$modelDiscountFinance;
    public function __construct(Category $modelCategory, Vehicle $modelVehicle, Discount $modelDiscount, Vindiscount $modelVinDiscount, Financediscount $modelDiscountFinance, Vinactivation $modelVinManagement){
        $this->modelCategory = $modelCategory;
        $this->modelVehicle = $modelVehicle;
        $this->modelDiscount = $modelDiscount;
        $this->modelVinDiscount = $modelVinDiscount;
        $this->modelVinManagement = $modelVinManagement;
        $this->modelDiscountFinance = $modelDiscountFinance;
    }

	/***
	*	List of all vehicles - Search By Attributes
	*/
	public function SearchByAttributes_api(SearchRequest $request) {
		$this->ExecuteSettings();
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 		= $request->get('NameOfAPI');
		$DealerCode 		= $request->get('DealerCode');
		$financeOption 		= $request->get('FinanceOption');
		$MakeCode	 		= $request->get('MakeCode');
		$ModelYear	 		= $request->get('ModelYear');
		$Model	 			= $request->get('Model');
		$Trim	 			= $request->get('Trim');
		$MsrpHighest	= $request->get('MsrpHighest');
		$MsrpLowest	 	= $request->get('MsrpLowest');
		$DriveIDs	 		= $request->get('DriveNames');
		$ColorIDs	 		= $request->get('ColorNames');
		$EngineDescIDs	 	= $request->get('EngineDescNames');
		$TransmissionIDs	= $request->get('TransmissionNames');

		$Make		 	= $this->getMakeAbbrevationName($MakeCode);
		$this->dealer_code = $this->getDealerCode();
		$discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$financeOption.') as discount_count';
		$vehicle  = \DB::table('fca_ore_input as i')
		->leftJoin('vindiscounts as v','i.vin','=','v.vin')
		->leftJoin('discounts as d','v.discount_id','=','d.id')
		->leftJoin('financediscounts as f','v.discount_id','=','f.discount_id')
		->select('i.dealer_code','i.vin','i.msrp','i.make','i.model','i.year','i.trim_desc',\DB::raw($discount_condition),'f.finance_option')
		->where(['i.vehicle_type' => 'New', 'i.dealer_code' => $DealerCode, 'i.year'=>$ModelYear, 'i.make' => $Make, 'i.model' => $Model]);
		if(!empty($Trim))
		{
			$vehicle->whereIn('i.trim_desc', $Trim);
		}
		if(!empty($MsrpHighest) && !empty($MsrpLowest))
		{
			$vehicle->where([['i.msrp','>=', $MsrpLowest],['i.msrp','<=' , $MsrpHighest]]);
		}
		if(!empty($DriveIDs))
		{
			$DriveNames = explode(",",$DriveIDs);
			$vehicle->whereIn('i.drive_type', $DriveNames);
		}
		if(!empty($ColorIDs))
		{
			$ColorNames = explode(",",$ColorIDs);
			$vehicle->whereIn('i.exterior_color_code', $ColorNames);
		}
		if(!empty($EngineDescIDs))
		{
			$EngineDescNames = explode(",",$EngineDescIDs);
			$vehicle->whereIn('i.eng_desc', $EngineDescNames);
		}
		if(!empty($TransmissionIDs))
		{
			$TransmissionNames = explode(",",$TransmissionIDs);
			$vehicle->whereIn('i.transmission_desc', $TransmissionNames);
		}

		$data = $vehicle->groupBy('i.vin')->get();

		$data_array = array();
		if(!$data->isEmpty()){
			$data_list = $data->toArray();
			$data_array = $this->validateSearchResult($DealerCode,$data_list,$financeOption);
		}
		$result = $this->traitSearchByAttributes($data_array, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $Trim, $MsrpHighest, $MsrpLowest, $DriveIDs, $ColorIDs, $EngineDescIDs, $TransmissionIDs);

		return response()->json($result, $this->successStatus);
	}


	/***
	*	List of all vehicles - Search By VIN
	*/
	public function SearchByVIN(SearchRequest $request) {
		$this->ExecuteSettings();
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 		= $request->get('NameOfAPI');
		$DealerCode 		= $request->get('DealerCode');
		$VinNumber	 		= $request->get('VinNumber');
		$financeOption 		= $request->get('FinanceOption');
		$this->dealer_code = $this->getDealerCode();
		$discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$financeOption.') as discount_count';
		$data  = \DB::table('fca_ore_input as i')
		->leftJoin('vindiscounts as v','i.vin','=','v.vin')
		->leftJoin('discounts as d','v.discount_id','=','d.id')
		->leftJoin('financediscounts as f','v.discount_id','=','f.discount_id')
		->select('i.dealer_code','i.vin','i.make','i.model','i.year','i.msrp','i.trim_desc',\DB::raw($discount_condition),'f.finance_option')
		->where(['i.vehicle_type' => 'New', 'i.dealer_code' => $DealerCode, 'i.vin'=>$VinNumber])
		->groupBy('i.vin')->get();
		$data_array = array();
		if(!$data->isEmpty()){
			$data = $data->toArray();
			$data_array = $this->validateSearchResult($DealerCode,$data,$financeOption);
		}
		$result 		= $this->traitSearchByVIN($data_array, $NameOfAPI, $DealerCode, $VinNumber);

		return response()->json($result, $this->successStatus);
	}

    public function validateSearchResult($dealer_code,$data,$financeOption)
    {
    	$data_array = array();
    	$vin_deactive_list = $this->getVinDeactiveList($dealer_code);
    	$date = \DB::raw('NOW()') ;
		foreach ($data as $key => $value) {
			$data_array[$key] = (array) $value;
			$data_array[$key]['vin_active'] = 0;
			$data_array[$key]['finance_option'] = $financeOption;
			$data_array[$key]['total_amount'] = 0;
			if($data_array[$key]['discount_count']){
				$data_array[$key]['total_amount'] = $this->dealerDiscountCalculation($data_array[$key]['vin'], $data_array[$key]['msrp'], $financeOption);
			}
			$data_array[$key]['total_amount_format'] = '$' . number_format($data_array[$key]['total_amount']);
			//$data_array[$key]['discount_count'] = $this->existDiscountCount($dealer_code, $value->vin, $financeOption);
			if(!empty($vin_deactive_list)){
				if(in_array($data_array[$key]['vin'], $vin_deactive_list)){
					$data_array[$key]['vin_active'] = 1;
				}
			}
		}
		return $data_array;
    }

    /***
	*	List of all vehicles - Search By Attributes
	*/
	public function SearchByAttributes(SearchRequest $request) {
		$this->ExecuteSettings();
		//Validate Request Params
        $validator = $request->validated();
		$NameOfAPI	 		= $request->get('NameOfAPI');
		$DealerCode 		= $request->get('DealerCode');
		$financeOption 		= $request->get('FinanceOption');
		$MakeCode	 		= $request->get('MakeCode');
		$ModelYear	 		= $request->get('ModelYear');
		$Model	 			= $request->get('Model');
		$Trim	 			= $request->get('Trim');
		$MsrpHighest	= $request->get('MsrpHighest');
		$MsrpLowest	 	= $request->get('MsrpLowest');
		$DriveIDs	 		= $request->get('DriveNames');
		$ColorIDs	 		= $request->get('ColorNames');
		$EngineDescIDs	 	= $request->get('EngineDescNames');
		$TransmissionIDs	= $request->get('TransmissionNames');

		$Make		 	= $this->getMakeAbbrevationName($MakeCode);
		$this->dealer_code = $this->getDealerCode();
		$Trim = array_map(function($val){
			$val = str_replace('"', "&quot;", $val);
			$val = str_replace("'", '&#39;', $val);
			return $val;
		},$Trim);

		$conditionArray = [$DealerCode,$Make,$financeOption,$ModelYear,$Model,implode(",",$Trim),$MsrpHighest,$MsrpLowest];
		$conditionArray[] = !empty($DriveIDs) ? $DriveIDs : '';
		$conditionArray[] = !empty($ColorIDs) ? $ColorIDs : '';
		$conditionArray[] = !empty($EngineDescIDs) ? $EngineDescIDs: '';
		$conditionArray[] = !empty($TransmissionIDs) ? $TransmissionIDs : '';
		\Log::info('---------------------sp_search_by_attributes_edit--------------------');
        \Log::info($conditionArray);
        $data_list = \DB::select('CALL sp_search_by_attributes_edit(?,?,?,?,?,?,?,?,?,?,?,?)', $conditionArray);
	    $data_array = array();
		if(!empty($data_list)){
			$vin_deactive_list = $this->getVinDeactiveList($DealerCode);
			foreach ($data_list as $key => $value) {
				$data_array[$key] = (array) $value;
				$data_array[$key]['vin_active'] = 0;
				$data_array[$key]['total_amount'] = intval($data_array[$key]['total_amount']);
				$data_array[$key]['total_amount_format'] = '$' . number_format($data_array[$key]['total_amount']);
				//$data_array[$key]['discount_count'] = $this->existDiscountCount($dealer_code, $value->vin, $financeOption);
				if(!empty($vin_deactive_list)){
					if(in_array($data_array[$key]['vin'], $vin_deactive_list)){
						$data_array[$key]['vin_active'] = 1;
					}
				}
			}
		}
		$result = $this->traitSearchByAttributes($data_array, $NameOfAPI, $DealerCode, $MakeCode, $ModelYear, $Model, $Trim, $MsrpHighest, $MsrpLowest, $DriveIDs, $ColorIDs, $EngineDescIDs, $TransmissionIDs);

		return response()->json($result, $this->successStatus);
	}
}

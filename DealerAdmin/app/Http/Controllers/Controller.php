<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Foundation\Validation\ValidatesRequests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\User; 

use App\Model\Vehicle;

use App\Model\Category;

use App\Model\Discount;

use App\Model\Vindiscount;

use App\Model\Financediscount;

use App\Model\Vinactivation;

use App\Model\Filterdiscount;

class Controller extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $modelCategory, $modelVehicle,$modelDiscount,$modelVinDiscount,$modelVinManagement,$modelDiscountFinance,$modelFilterDiscount;
    Public $ExecuteSettings         = true;
    public function __construct(Category $modelCategory, Vehicle $modelVehicle, Discount $modelDiscount, Vindiscount $modelVinDiscount, Financediscount $modelDiscountFinance, Vinactivation $modelVinManagement,Filterdiscount $modelFilterDiscount){
        $this->modelCategory = $modelCategory;
        $this->modelVehicle = $modelVehicle;
        $this->modelDiscount = $modelDiscount;
        $this->modelVinDiscount = $modelVinDiscount;
        $this->modelVinManagement = $modelVinManagement;
        $this->modelDiscountFinance = $modelDiscountFinance;
        $this->modelFilterDiscount = $modelFilterDiscount;
    }

    public function ExecuteSettings(){
        if($this->ExecuteSettings){
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', -1);
        }
    }

    public function getDealerCode(){
        $DealerAdmin = \Session::get('DealerAdmin');
        return \Session::get('DealerCode');;
        return $DealerAdmin['DealerCode'];
    }
    
    /**
     * Incentive Details gathered from Rebates Response.
     *
     * @return void
     */
    public function dealerDiscountCalculation($VinNumber, $price, $financeoption, $uuid_array = NULL){
        $discountAmount = 0;
        $list_discount_array = array();
        $date = \DB::raw('NOW()') ;
        $list_discount = \DB::table('vindiscounts as v')
		->join('discounts as d','v.discount_id','=','d.id')
		->join('financediscounts as f','v.discount_id','=','f.discount_id')		
		->where([['v.vin','=',$VinNumber],['d.end_date','>=',$date],['d.start_date','<=',$date],['f.finance_option','=',$financeoption]]);
		 if(!empty($uuid_array)){
		 	$list_discount = $list_discount->whereNotIn('uuid',$uuid_array);
		 }
		$list_discount = $list_discount->select('d.flat_rate','d.percent_offer','d.discount_saved')->groupBy('d.discount_name')->get();        
            if(!$list_discount->isEmpty()){
                $list_discount_array = $list_discount->toArray();
                foreach($list_discount_array as $list_discount_array_key => $value){  
                	$list_discount_array_value = (array) $value;
                    if($list_discount_array_value['flat_rate'] == null || empty($list_discount_array_value['flat_rate']) ||
                        $list_discount_array_value['flat_rate']==0 ){
                    	//if($list_discount_array_value['discount_saved'] == 1){
                            $p_cent = 0;
                            $p_cent = round(($list_discount_array_value['percent_offer'] / 100 )*  $price);
                            $discountAmount +=     $p_cent;                    		
                    	//}
                    }else{
                    	//if($list_discount_array_value['discount_saved'] == 1){
                            $discountAmount += $list_discount_array_value['flat_rate'];
                       // }
                    }    
                }
            }
       if(!config('ore.discounts.maxAmount5000Allowed')){
            if(5000 < $discountAmount){
                $discountAmount = 5000;
            }
       }
       return $discountAmount;
    }

    public function existDiscountCount($dealer_code,$vinnumber,$financeoption)
    {
        $exist_count = 0;

        //$discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$financeOption.') as discount_count';
       $date = \DB::raw('NOW()') ; 
       $exist_count  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id') 
        ->where(['f.finance_option' => $financeoption, 'v.dealer_code' => $dealer_code, 'v.vin'=>$vinnumber])->where([['end_date','>=',$date],['start_date','<=',$date]])->count();
       return $exist_count;
    }

    public function checkDiscountNameexistforvin($dealer_code,$vinnumber,$discountname)
    {
        $exist_discount = 0;

        //$discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$financeOption.') as discount_count';
       $date = \DB::raw('NOW()') ; 
       $exist_discount  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id') 
        ->where(['d.discount_name' => $discountname, 'v.dealer_code' => $dealer_code, 'v.vin'=>$vinnumber])->get();
       return $exist_discount;
    }

    public function getVinDeactiveList($DealerCode)
    {
		$result = array();
    	$vin_deactive_list = $this->modelVinManagement->where('dealer_code',$DealerCode)->select('vins')->get();
    	if($vin_deactive_list->isEmpty()){
    		return $result;
    	}
		$vin_deactive_list_array = $vin_deactive_list->toArray();
		if(!empty($vin_deactive_list_array[0]['vins'])){
			$result = explode(',', $vin_deactive_list_array[0]['vins']);
		}
		return $result;
    }

   	public function calculateDiscountAmount($price,$flat_rate,$percent_offer,$discount_saved)
    {
        $discountAmount = 0;
        if($flat_rate == null || empty($flat_rate) || $flat_rate == 0 ){
            //if($discount_saved == 1){
                $p_cent = 0;
                $p_cent = round(($percent_offer / 100 )*  $price);
                $discountAmount += $p_cent;                         
            //}
        }else{
            //if($discount_saved == 1){
                $discountAmount += $flat_rate;
           // }
        }
        return $discountAmount;
    }

    public function validateDIscountMaxPrice($request)
    {
        //dd($request->all());
        $exced = [];
        $couponcount = [];
        $FinanceOption = $request->FinanceOption;
        $DealerCode = $request->DealerCode;
        $vin = $request->VinNumber;
        $vin = is_array($vin) ? $vin : array($vin);
        $list = \DB::table('fca_ore_input')->whereIN('vin', $vin)->pluck('msrp', 'vin');
        $vinCountResult = \DB::table('vindiscounts')
            ->join('discounts', 'vindiscounts.discount_id', '=', 'discounts.id')
            ->join('financediscounts', 'vindiscounts.discount_id', '=', 'financediscounts.discount_id')
            ->whereIN('vindiscounts.vin', $vin)
            ->where('vindiscounts.dealer_code', $DealerCode)
            ->where('financediscounts.finance_option', $FinanceOption)->groupBy('vindiscounts.vin')
            ->select('vindiscounts.vin',\DB::raw('count(DISTINCT(`discounts`.`discount_name`)) as discountcount'))
            ->get();
        $vinCountResultArray = array();
        if(!$vinCountResult->isEmpty()){
            $vinCountResultArray = $vinCountResult->toArray();            
        }
        $vinCountResultArray = json_decode(json_encode($vinCountResultArray), true);
        $Discount = $request->Discount;
        $discountarray = [];
        foreach ($Discount as $key => $value) {
            $discountarray[] = [$value['flat_rate'],  $value['discount_start_date'], $value['discount_end_date'],  $value['percent_offer']];
        }
        foreach ($list as $vin => $msrp) {
            $result = $this->msrpcalculation($discountarray, $msrp, $vin);
            if ($result) {
                $exced[] = $vin;
            }
            $coupon = false;
            if(!empty($vinCountResultArray)){
                $index = array_search($vin, $vinCountResultArray);
                if($index != false){
                    $discount_count = $vinCountResultArray[$index]['discountcount'];
                    $coupon = ($discount_count > 5) ? true : false;                    
                }
            }

            //$coupon = $this->CountCoupon($DealerCode, $FinanceOption, $vin);
            if ($coupon) {
                $couponcount[] = $vin;
            }
        }       
        return array($exced, $couponcount);         
    }

    public function msrpcalculation($discountarray, $msrp, $vin)
    {
        $group = [];
        if (!empty($discountarray)) {
            foreach ($discountarray as $key1 => $value) {
                $temp = [];
                $check_date = $value[1];
                foreach ($discountarray as $key2 => $val) {
                    $start_date = $val[1];
                    $end_date = $val[2];
                    if (strtotime($check_date) <= strtotime($end_date) && strtotime($check_date) >= strtotime($start_date)) {
                        $temp[$key2] =   $val;
                    } 
                }
                $group[$key1] = $temp;
            }
        }
        $max = 0;
        foreach ($group as $key1 => $data) {
           $max = 0;
            if (!empty($value)) {
                foreach ($data as $key2 => $value) {
                    $max += ($value[3] == 0) ? $value[0] : (($msrp * $value[3]) / 100);
                }
            }
        }
        return ($max > 5000) ? true : false;
    }

    public function CountCoupon($DealerCode, $FinanceOption, $vin)
    {
        $res = \DB::table('vindiscounts')
            ->join('discounts', 'vindiscounts.discount_id', '=', 'discounts.id')
            ->join('financediscounts', 'vindiscounts.discount_id', '=', 'financediscounts.discount_id')
            ->where('vindiscounts.vin', $vin)
            ->where('vindiscounts.dealer_code', $DealerCode)
            ->where('financediscounts.finance_option', $FinanceOption)->groupBy('discounts.discount_name')
            ->get();
        $res_array = ($res->isEmpty()) ? $res : $res->toArray();
        $discount_count = count($res_array);
         
        return ($discount_count > 5) ? true : false;
    }
}
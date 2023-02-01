<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Validator;

use App\Traits\CacheTrait;

use App\Traits\APIRequestTrait;

use App\User; 

use App\Model\Vehicle;

use App\Model\Category;

use App\Model\Discount;

use App\Model\Vindiscount;

use App\Model\Financediscount;

use App\Model\Vinactivation;

use App\Model\Filterdiscount;

use App\Http\Requests\AddDiscount;

use App\Http\Requests\EditDiscount;

use App\Http\Requests\FilterRequest;

use Carbon;

class DiscountController extends Controller
{
    use CacheTrait, APIRequestTrait;
    public $successStatus = 200;
    public $successMessage = array (
        "add" => "Discount has been applied Successfully for specific vehicle.",
        "edit" => "Discount has been updated and applied Successfully for specific vehicle.",
        "delete" => "Discount has been removed successfully for specific vehicle.",
        "vindelete" => "All Discount has been removed successfully for specific vehicle."
    );

    public $errorMessage = array (
        "add" => "Discount is not applied for specific vehicle.",
        "edit" => "Discount is not updated and applied for specific vehicle.",
        "delete" => "Discount is not removed for specific vehicle.",
        "vindelete" => "All Discount is not removed for specific vehicle."
    );

    private $dealer_code;  
    protected $modelCategory, $modelVehicle,$modelDiscount,$modelVinDiscount,$modelVinManagement,$modelDiscountFinance,$modelFilterDiscount;
    public function __construct(Category $modelCategory, Vehicle $modelVehicle, Discount $modelDiscount, Vindiscount $modelVinDiscount, Financediscount $modelDiscountFinance, Vinactivation $modelVinManagement,Filterdiscount $modelFilterDiscount){
        $this->modelCategory = $modelCategory;
        $this->modelVehicle = $modelVehicle;
        $this->modelDiscount = $modelDiscount;
        $this->modelVinDiscount = $modelVinDiscount;
        $this->modelVinManagement = $modelVinManagement;
        $this->modelDiscountFinance = $modelDiscountFinance;
        $this->modelFilterDiscount = $modelFilterDiscount;
    }

    /**
     * Store a newly created discount against VIN & Dealer code in Database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddDiscount(AddDiscount $request)
    {   
        //Validate Request Params
        $validator = $request->validated();
        $result = $request->all();         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $NameOfAPI          = $request->get('NameOfAPI');
        $DealerCode         = $request->get('DealerCode');
        $financeOption      = $request->get('FinanceOption');
        $VinNumber          = $request->get('VinNumber'); 
        $Discount           = $request->get('Discount'); 
        $array_discount = array_column($Discount, 'name_of_discount');
        $uarr = array_unique($array_discount);
        $duplicate_name = array_unique(array_diff($array_discount, array_diff($uarr, array_diff_assoc($array_discount, $uarr))));
        if(0 < count($duplicate_name)){
            $duplicate_names = implode(',',$duplicate_name);
            $result['Message'] = '“'.$duplicate_names.'” already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }

        $date = \DB::raw('NOW()') ;
        $exist_count = $this->existDiscountCount($DealerCode,$VinNumber,$financeOption);
        $adding_count = count($Discount);

        if(($exist_count+$adding_count) > 5){
            $result['Message'] = 'Maximum five discounts are allowed for a vehicle.';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }
        $price = $this->modelVehicle->where('vin',$VinNumber)->where('dealer_code',$DealerCode)->value('msrp');
        $current_discount_amt = 0;
        if(0 <  $exist_count){
            $current_discount_amt = $this->dealerDiscountCalculation($VinNumber, $price, $financeOption);            
        }
        $total_discount_amount = 0;
        $total_discount_amount += $current_discount_amt;
        $error_array = array();
        $create_array = array();
        foreach ($Discount as $key => $value) {
            $discount = $this->checkDiscountNameexistforvin($DealerCode,$VinNumber,$value['name_of_discount']);
            if(!$discount->isEmpty()) {
                //return error response
                array_push($error_array,$value['name_of_discount']);
                continue;
            }
            $start_date = date('Y-m-d 00:00:00',strtotime($value['discount_start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($value['discount_end_date']));
            $data_array = array(
                'dealer_code' => $DealerCode,
                'vin' => $VinNumber,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? NULL: floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? NULL: floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount'],
                'uuid' => \DB::raw('uuid()')
            );
            $total_discount_amount += $this->calculateDiscountAmount($price,$data_array['flat_rate'],$data_array['percent_offer'],$data_array['discount_saved']);
            array_push($create_array,$data_array);
        }

       if(5000 < $total_discount_amount){
            $result['Message'] = 'Maximum allowed discount is $5000';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
       }

         if(!empty($error_array)){
            $result['Message'] = implode(',',$error_array).' already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            foreach ($create_array as $key => $value) {
                $discount_insert = $this->modelDiscount->create($value);
                $last_discount_id =  $discount_insert->id;
                $finance_array = array(
                    'discount_id' => $last_discount_id,
                    'finance_option' => $financeOption
                );
                array_push($finance_discount, $finance_array);
                $vindiscount_array = array(
                    'discount_id' => $last_discount_id,
                    'dealer_code' => $DealerCode,
                    'vin'         => $value['vin']
                );
                array_push($vin_discount, $vindiscount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status_flag = ($status) ? true : false;
            $result['Message'] = $this->successMessage['add'];
            if(!$status_flag){
                $result['StatusCode'] = 1003;
                $result['Message'] = $this->errorMessage['add'];
            }
        }else{
            $result['Message'] = "Empty values cannot be added";
            $result['StatusCode'] = 1002;
        }
        return response()->json($result, $this->successStatus);
    }

    /**
     * The discount record will update if exists or add if not exists.
     *
     * @return \Illuminate\Http\Response
     */
    public function EditDiscount($DealerCode, EditDiscount $request)
    {
        //Validate the Request Params
        $validator = $request->validated();
        $result = $request->all();         
        $result['DealerCode'] = $DealerCode;         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $NameOfAPI          = $request->get('NameOfAPI');
        $financeOption      = $request->get('FinanceOption');
        $VinNumber          = $request->get('VinNumber'); 
        $Discount           = $request->get('Discount'); 

        $rs = $this->validateDIscountMaxPrice($request);         
        if (count($rs[0]) > 0) {
            $result['Message'] = 'Maximum allowed discount is $5000';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }
       
        if (count($rs[1]) > 0) {
            $result['Message'] = 'Maximum allowed number of discount is 5 ';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }
        $date = \DB::raw('NOW()') ;
        $discount = $this->modelVinDiscount->where('vin',$VinNumber)->where('dealer_code',$DealerCode)->get();
        if($discount->isEmpty()){
            $result['Message'] = "Resource not found.";
            $result['StatusCode'] = 1004;
            return response()->json($result, $this->successStatus);
        }
        $exist_count = $this->existDiscountCount($DealerCode,$VinNumber,$financeOption);
        $price = $this->modelVehicle->where('vin',$VinNumber)->where('dealer_code',$DealerCode)->value('msrp');
        $total_discount_amount = 0;
        $error_array = array();$update_error = array();$update_array = array();$create_array = array();
        foreach ($Discount as $key => $value) {
            $start_date = date('Y-m-d 00:00:00',strtotime($value['discount_start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($value['discount_end_date']));
            $data_array = array(
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? NULL: floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? NULL: floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount']
            );
            if(array_key_exists('uuid', $value)) {
                $discount = $this->modelDiscount->where('dealer_code',$DealerCode)->where('uuid',$value['uuid'])->where('discount_name',$value['name_of_discount'])->get();
                if($discount->isEmpty()){
                   array_push($update_error,$value['name_of_discount']);
                }
                $data_array['uuid'] = $value['uuid'];
                $data_array['bulk_flag'] = 0;
                array_push($update_array,$data_array);  
                $total_discount_amount += $this->calculateDiscountAmount($price,$data_array['flat_rate'],$data_array['percent_offer'],$data_array['discount_saved']);
            } 
            else {
                $discount = $this->checkDiscountNameexistforvin($DealerCode,$VinNumber,$value['name_of_discount']);
                if(!$discount->isEmpty()) {
                    array_push($error_array,$value['name_of_discount']);
                }
                $total_discount_amount += $this->calculateDiscountAmount($price,$data_array['flat_rate'],$data_array['percent_offer'],$data_array['discount_saved']);
                $data_array['dealer_code'] = $DealerCode;
                $data_array['vin'] = $VinNumber;
                $data_array['uuid'] = \DB::raw('uuid()');
                array_push($create_array,$data_array);                
            }            
        }

        $total_discount = $exist_count + count($create_array);
        if( $total_discount > 5){
            $result['Message'] = 'Maximum five discounts are allowed for a vehicle.';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }

        if(!empty($error_array) || !empty($update_error)){
            $message = '';            
            if(!empty($update_error)){
                $message .= 'Can\'t update the following discount name are duplicate/not exists for vehicle - '.implode(',',$update_error);                
            }
            if(!empty($error_array)){
                $message = !empty($message) ? $message . ' and ' : $message;
                $message .= implode(',',$error_array) . ' already exists. Please use a different discount name.';
            }
            $result['Message'] = $message;
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }

        $result['Message'] = $this->successMessage['edit'];
        $status_flag = false;
        if(!empty($create_array)) {
            $finance_discount = array();
            $vin_discount = array();
           foreach ($create_array as $key => $value) {
                $discount_insert = $this->modelDiscount->create($value);
                $last_discount_id =  $discount_insert->id;
                $finance_array = array(
                    'discount_id' => $last_discount_id,
                    'finance_option' => $financeOption
                );
                array_push($finance_discount, $finance_array);
                $vindiscount_array = array(
                    'discount_id' => $last_discount_id,
                    'dealer_code' => $DealerCode,
                    'vin'         => $VinNumber
                );
                array_push($vin_discount, $vindiscount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status_flag = ($status) ? true : false;
            if(!$status){
                $result['Message'] = $this->errorMessage['edit'];
                $result['StatusCode'] = 1003;
            }
        }

        if(!empty($update_array)) {
            foreach ($update_array as $key => $value) {
                $update_array[$key]['updated_at'] = date('Y-m-d H:i:s') ;
                $update_array[$key]['bulk_updated_at'] = date('Y-m-d H:i:s') ;
                $status = $this->modelDiscount->where('dealer_code',$DealerCode)->where('uuid',$value['uuid'])->update($value);     
            }
        }        
        return response()->json($result, $this->successStatus);
    }

    /**
     * The discount record will delete based on discountID
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteDiscount($uuid)
    {
        //Validate the Request Params
        $result = array();         
        $result['uuid'] = $uuid;
        $result['StatusCode'] = 1000;         

        $discount = $this->modelDiscount->where('uuid',$uuid)->get();
        if(!$discount->isEmpty()){
            $discount_array = $discount->toArray();
            $discount_ids_array = array_column($discount_array,'id');
            $result['Message'] = $this->successMessage['delete'];
            $status = $this->modelDiscount->where('uuid',$uuid)->delete();
            $status = $this->modelDiscountFinance->whereIn('discount_id',$discount_ids_array)->delete();
            $status = $this->modelVinDiscount->whereIn('discount_id',$discount_ids_array)->delete();
            if(!$status){
                $result['Message'] = $this->errorMessage['delete'];
                $result['StatusCode'] = 1003;
            } 
        }else{
            $result['StatusCode'] = 1004;
            $result['Message'] = "Resource not found";  
        }
        return response()->json($result, $this->successStatus);
    }

    /**
     * The discount list for DealerCode and Vinnumber
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiscount(FilterRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');
        $financeOption      = $request->get('FinanceOption');    
        $VinNumber     = $request->get('VinNumber');       
        $date = \DB::raw('NOW()') ;
        $list_discount  = \DB::table('discounts as d')
        ->join('vindiscounts as v','v.discount_id','=','d.id')     
        ->join('financediscounts as f','f.discount_id','=','d.id')     
        ->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
            \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount')->where([['d.dealer_code','=',$DealerCode],['v.vin','=',$VinNumber],['f.finance_option','=',$financeOption],['d.end_date','>=',$date]])->orWhere([['d.dealer_code','=',$DealerCode],['v.vin','=',$VinNumber],['d.discount_saved','=','1'],['f.finance_option','=',$financeOption],['d.end_date','>=',$date]])->get();
        if(!$list_discount->isEmpty()){
            $list_discount = $list_discount->toArray();
        }
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['discounts'] = $list_discount;
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The discount record will delete based on discountID
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteVinDiscount($DealerCode,$VinNumber,$FinanceOption)
    {
        //Validate the Request Params
        $result = array();         
        $result['DealerCode'] = $DealerCode;
        $result['VinNumber'] = $VinNumber;
        $result['StatusCode'] = 1000;   
        $date = \DB::raw('NOW()') ;       
        $discount_count = $this->existDiscountCount($DealerCode,$VinNumber,$FinanceOption);
        if(!(0 < $discount_count)){
            $result['StatusCode'] = 1004;
            $result['Message'] = "No Discount availabe to remove";
            return response()->json($result, $this->successStatus); 
        }
        $result['Message'] = $this->successMessage['vindelete'];
        $discount_ids  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id') 
        ->where(['f.finance_option' => $FinanceOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$VinNumber])->select('d.id as discount_id')->get();
        //->where([['end_date','>=',$date],['start_date','<=',$date]]);
        $discount_id_array = array();
        if(!empty($discount_ids)){
            $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
        }
        $status = $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_id_array)->delete();
        $status = $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->where('vin',$VinNumber)->delete();
        $status = $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();
        $this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
        if(!$status){
            $result['Message'] = $this->errorMessage['vindelete'];
            $result['StatusCode'] = 1003;
        }else{
            $max = \DB::table('discounts')->max('id') + 1; 
            \DB::statement("ALTER TABLE discounts AUTO_INCREMENT =  $max");
        }
        return response()->json($result, $this->successStatus);
    }

    /**
     * The vin activation based on operation
     * Opertion: 1 - Deactive; 0 - Active;
     * @return \Illuminate\Http\Response
     */
    public function vinActivation(FilterRequest $request)
    {
        //Validate Request Params
        $validator = $request->validated();        
        $result = $request->all();         
        $result['StatusCode'] = 1000;         
        $DealerCode     = $request->get('DealerCode');       
        $VinNumber     = $request->get('VinNumber'); 
        $Operation     = $request->get('Operation'); 
        $exists = $this->modelVehicle->where('vin',$VinNumber)->where('dealer_code',$DealerCode)->get();
        if($exists->isEmpty()){
            $result['Message'] = "Resource not found.";
            $result['StatusCode'] = 1004;
            return response()->json($result, $this->successStatus);
        }
        $create_array = array();
        $check_exists = $this->modelVinManagement->where('dealer_code',$DealerCode)->get();
        if(!$check_exists->isEmpty()){
            $update_array = $check_exists[0];
            $vins = !empty($update_array['vins']) ? explode(",",$update_array['vins']) : array();
            $update = array();
            $update['updated_at'] = date('Y-m-d H:i:s');
            if($Operation == 0){
                if(in_array($VinNumber, $vins)){
                    $update['vins'] = array_diff($vins, array($VinNumber));
                }
                if(empty($update['vins'])){
                    $status = $this->modelVinManagement->where('dealer_code',$DealerCode)->delete();
                    $max = \DB::table('vinactivations')->max('id') + 1; 
                    \DB::statement("ALTER TABLE vinactivations AUTO_INCREMENT =  $max"); 
                }else{
                    $update['vins'] = implode(',',$update['vins']);
                   $status = $this->modelVinManagement->where('dealer_code',$DealerCode)->update($update); 
                }
            }
            if($Operation == 1){
                if(!in_array($VinNumber, $vins)){
                    array_push($vins,$VinNumber);
                }
                $update['vins'] = implode(',',$vins);
                $status = $this->modelVinManagement->where('dealer_code',$DealerCode)->update($update);
            }
        }
        else{
            if($Operation == 1){
                $create_array = array();
                $create_array['dealer_code'] = $DealerCode;
                $create_array['vins'] = $VinNumber;
                $status = $this->modelVinManagement->insert($create_array);
            }
        }
        $result['Message'] = ($Operation == 1) ? "Successfully Vin Deactivated" : "Successfully Vin Activated";  
        return response()->json($result, $this->successStatus);
    }

    /*public function CountCoupon($DealerCode, $FinanceOption, $vin)
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

    public function validateDIscountMaxPrice($request)
    {
        //dd($request->all());
        $exced = [];
        $couponcount = [];
        $FinanceOption = $request->FinanceOption;
        $DealerCode = $request->DealerCode;
        $vin = $request->VinNumber;
        $list = \DB::table('fca_ore_input')->where('vin', $vin)->pluck('msrp', 'vin');
        $Discount = $request->Discount;
        foreach ($list as $vin => $msrp) {
            $result = $this->msrpcalculation($Discount, $msrp, $vin);
            if ($result) {
                $exced[] = $vin;
            }
            $coupon = $this->CountCoupon($DealerCode, $FinanceOption, $vin);
            if ($coupon) {
                $couponcount[] = $vin;
            }
        }
        //dd($exced);
        return array($exced, $couponcount);
    }

    public function msrpcalculation($Discount, $msrp, $vin)
    {
        $discountarray = [];
        foreach ($Discount as $key => $value) {
            $discountarray[] = [$value['flat_rate'],  $value['discount_start_date'], $value['discount_end_date'],  $value['percent_offer']];
        }

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
    }*/
}

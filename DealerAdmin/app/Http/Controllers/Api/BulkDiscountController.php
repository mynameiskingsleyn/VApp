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

use App\Http\Requests\BulkDiscountRequest;

use DB;

class BulkDiscountController extends Controller
{
    use CacheTrait, APIRequestTrait;
    public $successStatus = 200;
    public $successMessage = array(
        "add" => "Bulk Discount has been applied Successfully for specific vehicle.",
        "edit" => "Bulk Discount has been updated and applied Successfully for specific vehicle.",
        "delete" => "Bulk Discount has been removed successfully for specific vehicle.",
        "deleteall" => "All Applicable Discounts has been removed successfully for selected vehicle."
    );

    public $errorMessage = array(
        "add" => "Bulk Discount is not applied for specific vehicle.",
        "edit" => "Bulk Discount is not updated and applied for specific vehicle.",
        "delete" => "Bulk Discount is not removed for specific vehicle.",
        "deleteall" => "All Applicable Discounts are not removed for selected vehicle."
    );
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
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddDiscount_api(BulkDiscountRequest $request)
    {   
        //Validate Request Params
        $validator = $request->validated();
        $result = $request->all();         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $NameOfAPI          = $request->get('NameOfAPI');
        $financeOption      = $request->get('FinanceOption');
        $DealerCode         = $request->get('DealerCode');
        $Discount           = $request->get('Discount'); 
        $VinNumber           = $request->get('VinNumber'); 
        
        $error_array = array();
        $list_discount = array();
        $total_discount_amount = 0;
        $array_discount = array_column($Discount, 'name_of_discount');
        $uarr = array_unique($array_discount);
        $duplicate_name = array_unique(array_diff($array_discount, array_diff($uarr, array_diff_assoc($array_discount, $uarr))));
        if(0 < count($duplicate_name)){
            $duplicate_names = implode(',',$duplicate_name);
            $result['Message'] = '“'.$duplicate_names.'” already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
        $rs = $this->validateDIscountMaxPrice($request);
        if (count($rs[0]) > 0) {
            $result['Message'] = 'Maximum allowed discount is $5000';
            $result['StatusCode'] = 1001;
            if(config('ore.discounts.maxAmount5000Allowed')){
                return response()->json($result, $this->successStatus);
            }
        }

        if (count($rs[1]) > 0) {
            $result['Message'] = 'Maximum allowed number of discount is 5 ';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }

        foreach ($Discount as $key => $value) {
            $start_date = date('Y-m-d 00:00:00',strtotime($value['discount_start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($value['discount_end_date']));           
            $data_array = array(
                'dealer_code' => $DealerCode,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? NULL: floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? NULL: floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount'],
                'uuid' => \DB::raw('uuid()'),
                'bulk_flag' => '1'
            );
             $total_discount_amount += $data_array['flat_rate'];
            array_push($list_discount,$data_array);
        }
         
         $update_array = array();
         $create_array = array();
        if(empty($list_discount)){
           $result['Message'] = "Empty values cannot be added";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }
        foreach ($list_discount as $key => $value) {
            foreach ($VinNumber as $k => $v) {
                $list_discount[$key]['vin'] = $v;
                array_push($create_array,$list_discount[$key]);
                $exist_discount = $this->existDiscountCount($DealerCode,$v,$financeOption);
                if(0 < $exist_discount){
                    $discount_ids  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where(['f.finance_option' => $financeOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$v])->select('d.id as discount_id')->get();
                    $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
                    $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->where('dealer_code',$DealerCode)->where('vin',$v)->delete();
                    $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_id_array)->delete();
                    $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();
                    $this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
                    $max = \DB::table('discounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE discounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('financediscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE financediscounts AUTO_INCREMENT =  $max");
                }
            }                
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
        }
        $result['StatusCode'] = 1000;
        return response()->json($result, $this->successStatus);
    }

        /**
     * Store a newly created discount against VIN & Dealer code in Database.
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddDiscount(BulkDiscountRequest $request)
    {   
        //Validate Request Params
        $validator = $request->validated();
        $result = $request->all();         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $NameOfAPI          = $request->get('NameOfAPI');
        $financeOption      = $request->get('FinanceOption');
        $DealerCode         = $request->get('DealerCode');
        $Discount           = $request->get('Discount'); 
        $VinNumber           = $request->get('VinNumber'); 
        
        $error_array = array();
        $list_discount = array();
        $total_discount_amount = 0;
        $array_discount = array_column($Discount, 'name_of_discount');
        $uarr = array_unique($array_discount);
        $duplicate_name = array_unique(array_diff($array_discount, array_diff($uarr, array_diff_assoc($array_discount, $uarr))));
        if(0 < count($duplicate_name)){
            $duplicate_names = implode(',',$duplicate_name);
            $result['Message'] = '“'.$duplicate_names.'” already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
        $rs = $this->validateDIscountMaxPrice($request);
        if (count($rs[0]) > 0) {
            $result['Message'] = 'Maximum allowed discount is $5000';
            $result['StatusCode'] = 1001;
            if(config('ore.discounts.maxAmount5000Allowed')){
                return response()->json($result, $this->successStatus);
            }
        }

        if (count($rs[1]) > 0) {
            $result['Message'] = 'Maximum allowed number of discount is 5 ';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }

        foreach ($Discount as $key => $value) {
            $start_date = date('Y-m-d 00:00:00',strtotime($value['discount_start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($value['discount_end_date']));           
            $data_array = array(
                'dealer_code' => $DealerCode,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? '': floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? '': floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount'],
                //'uuid' => \DB::raw('uuid()'),
                'bulk_flag' => '1'
            );
            $total_discount_amount += floatval($data_array['flat_rate']);
            array_push($list_discount,$data_array);
        }
         
         $update_array = array();
         $create_array = array();
        if(empty($list_discount)){
           $result['Message'] = "Empty values cannot be added";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }
        $vinnumberString = !empty($VinNumber) ? implode(",",$VinNumber) : '';
        $paramsArray = array(
            'dealer_code' => $DealerCode,
            'paymentmethod' => $financeOption,
            'vinlist' => $vinnumberString
        );
/*        \Log::info('---------------------sp_bulk_delete_discounts_from_vinlist--------------------');
        \Log::info($paramsArray);
        \DB::select('CALL sp_bulk_delete_discounts_from_vinlist(?,?,?)', array_values($paramsArray));
        \Log::info('---------------------sp_bulk_delete_discounts_from_vinlist END--------------------');*/
            foreach ($VinNumber as $k => $v) {                
                $exist_discount = $this->existDiscountCount($DealerCode,$v,$financeOption);
                if(0 < $exist_discount){
                    $discount_ids  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where(['f.finance_option' => $financeOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$v])->select('d.id as discount_id')->get();
                    $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
                    $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->where('dealer_code',$DealerCode)->where('vin',$v)->delete();
                    $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_id_array)->delete();
                    $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();
                    $this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
                    $max = \DB::table('discounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE discounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('financediscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE financediscounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('vindiscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE vindiscounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('filterdiscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE filterdiscounts AUTO_INCREMENT =  $max");
                }
            }  

        foreach ($list_discount as $key => $value) {
                $list_discount[$key]['finance_option'] = $financeOption;
                $conditionArray = array_values($list_discount[$key]);
                $conditionArray[] = $vinnumberString;
                \Log::info('---------------------sp_bulk_add_discounts_to_vinlist--------------------');
                \Log::info($conditionArray);
                $discount_result = \DB::select('CALL sp_bulk_add_discounts_to_vinlist_edit(?,?,?,?,?,?,?,?,?,?)', $conditionArray);
                \Log::info('---------------------sp_bulk_add_discounts_to_vinlist END--------------------');
                \Log::info($discount_result);             
        }
        $result['StatusCode'] = 1000;
        $result['Message'] = $this->successMessage['add'];
        return response()->json($result, $this->successStatus);
    }

    /**
     * The vehicles list for DealerCode
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicles_api(BulkDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');
        $FinanceOption     = $request->get('FinanceOption');
        $discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$FinanceOption.') as discount_count';
        $list_discount  = \DB::table('fca_ore_input as i')
        ->leftJoin('vindiscounts as v','i.vin','=','v.vin')
        ->leftJoin('discounts as d','v.discount_id','=','d.id')
        ->leftJoin('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('i.dealer_code','i.vin','i.msrp','i.make','i.model','i.year','i.trim_desc',\DB::raw($discount_condition),'f.finance_option');
        $conditionArray = ['i.vehicle_type' => 'New', 'i.dealer_code' => $DealerCode];   
        if(!empty($request->get('VinNumber'))){
            $VinNumber     = $request->get('VinNumber');  
            $conditionArray['i.vin'] = $VinNumber;
            $list_discount->where($conditionArray);
        }else{
            $MakeCode           = $request->get('MakeCode');
            $ModelYear          = $request->get('ModelYear');
            $Model              = $request->get('Model');
            $Trim               = $request->get('Trim');
            $MsrpHighest    = $request->get('MsrpHighest');
            $MsrpLowest     = $request->get('MsrpLowest');
            $DriveIDs           = $request->get('DriveNames');
            $ColorIDs           = $request->get('ColorNames');
            $EngineDescIDs      = $request->get('EngineDescNames');
            $TransmissionIDs    = $request->get('TransmissionNames');
            $Make           = $this->getMakeAbbrevationName($MakeCode);
            $conditionArray['i.year'] = $ModelYear;
            $conditionArray['i.make'] = $Make;
            $conditionArray['i.model'] = $Model;
            $list_discount->where($conditionArray);
            if(!empty($Trim))
            {
               $list_discount->whereIn('i.trim_desc', $Trim);
            }
            if(!empty($MsrpHighest) && !empty($MsrpLowest))
            {
                $list_discount->where('i.msrp','>=', $MsrpLowest)->where('i.msrp','<=' , $MsrpHighest);
            }
            if(!empty($DriveIDs))
            {
                $DriveNames = explode(",",$DriveIDs);
                $list_discount->whereIn('i.drive_type', $DriveNames);
            }
            if(!empty($ColorIDs))
            {
                $ColorNames = explode(",",$ColorIDs);
                $list_discount->whereIn('i.exterior_color_code', $ColorNames);
            }
            if(!empty($EngineDescIDs))
            {
                $EngineDescNames = explode(",",$EngineDescIDs);
                $list_discount->whereIn('i.eng_desc', $EngineDescNames);
            }
            if(!empty($TransmissionIDs))
            {
                $TransmissionNames = explode(",",$TransmissionIDs);
                $list_discount->whereIn('i.transmission_desc', $TransmissionNames);
            }
        }
       $result = $list_discount->groupBy('i.vin')->get()->toArray();
        $vehicles = array();
        if(!empty($result)){ 
            $list_discount_array = $result;
            $vin_deactive_list = $this->getVinDeactiveList($DealerCode);  
            foreach ($list_discount_array as $key => $value) {
                if(is_object($value)){
                    $data = (array)$value;                
                }
                if(!empty($vin_deactive_list)){
                    if(in_array($data['vin'], $vin_deactive_list)){
                        continue;
                    }
                }
                $data['has_discount'] = $this->existDiscountCount($DealerCode,$data['vin'],$FinanceOption);
                $data['bulk_discount'] = 0;
                array_push($vehicles,$data);
            }
         }
        $response = $request->all(); 
        $response['StatusCode'] = 1000;
        $response['vehicles'] =$vehicles;
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The vehicles list for DealerCode
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicles(BulkDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');
        $FinanceOption     = $request->get('FinanceOption');
        $discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$FinanceOption.') as discount_count';
        $list_discount  = \DB::table('fca_ore_input as i')
        ->leftJoin('vindiscounts as v','i.vin','=','v.vin')
        ->leftJoin('discounts as d','v.discount_id','=','d.id')
        ->leftJoin('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('i.dealer_code','i.vin','i.msrp','i.make','i.model','i.year','i.trim_desc',\DB::raw($discount_condition),'f.finance_option');
        $conditionArray = ['i.vehicle_type' => 'New', 'i.dealer_code' => $DealerCode];   
        if(!empty($request->get('VinNumber'))){
            $VinNumber     = $request->get('VinNumber');  
            $conditionArray['i.vin'] = $VinNumber;
            $list_discount->where($conditionArray);
            $result = $list_discount->groupBy('i.vin')->get()->toArray();
        }else{
            $MakeCode           = $request->get('MakeCode');
            $ModelYear          = $request->get('ModelYear');
            $Model              = $request->get('Model');
            $Trim               = $request->get('Trim');
            $MsrpHighest    = $request->get('MsrpHighest');
            $MsrpLowest     = $request->get('MsrpLowest');
            $DriveIDs           = $request->get('DriveNames');
            $ColorIDs           = $request->get('ColorNames');
            $EngineDescIDs      = $request->get('EngineDescNames');
            $TransmissionIDs    = $request->get('TransmissionNames');
            $Make           = $this->getMakeAbbrevationName($MakeCode);
            $Trim = array_map(function($val){
                $val = str_replace('"', "&quot;", $val);
                $val = str_replace("'", '&#39;', $val);
                return $val;
            },$Trim);
            $conditionArray = [$DealerCode,$FinanceOption,$Make,$ModelYear,$Model,implode(",",$Trim),$MsrpLowest,$MsrpHighest];
            $conditionArray[] = !empty($DriveIDs) ? $DriveIDs : '';
            $conditionArray[] = !empty($ColorIDs) ? $ColorIDs : '';
            $conditionArray[] = !empty($EngineDescIDs) ? $EngineDescIDs: '';
            $conditionArray[] = !empty($TransmissionIDs) ? $TransmissionIDs : '';
            \Log::info('---------------------sp_bulk_get_vehicles--------------------');
            \Log::info($conditionArray);
            $result = \DB::select('CALL sp_bulk_get_vehicles(?,?,?,?,?,?,?,?,?,?,?,?)', $conditionArray);
        }
        $vehicles = array();
        if(!empty($result)){ 
            $list_discount_array = $result;
            $vin_deactive_list = $this->getVinDeactiveList($DealerCode);  
            foreach ($list_discount_array as $key => $value) {
                if(is_object($value)){
                    $data = (array)$value;                
                }
                if(!empty($vin_deactive_list)){
                    if(in_array($data['vin'], $vin_deactive_list)){
                        continue;
                    }
                }
                if(!empty($request->get('VinNumber'))){
                    $data['has_discount'] = $this->existDiscountCount($DealerCode,$data['vin'],$FinanceOption);                   
                }
                $data['bulk_discount'] = 0;
                array_push($vehicles,$data);
            }
         }
        $response = $request->all(); 
        $response['StatusCode'] = 1000;
        $response['vehicles'] =$vehicles;
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The discount record will delete based on discountID
     *
     * @return \Illuminate\Http\Response
     */
    public function removeBulkDiscount(BulkDiscountRequest $request)
    {
        //Validate Request Params
        $validator = $request->validated();
        $result = $request->all();         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $NameOfAPI          = $request->get('NameOfAPI');
        $DealerCode         = $request->get('DealerCode');
        $NameOfDiscount     = $request->get('NameOfDiscount'); 
        
        $discount = $this->modelDiscount->where('dealer_code',$DealerCode)->where('discount_name',$NameOfDiscount)->where('bulk_flag','1')->get();
        if(!$discount->isEmpty()){
            $result['Message'] = $this->successMessage['delete'];
            $status = $this->modelDiscount->where('dealer_code',$DealerCode)->where('discount_name',$NameOfDiscount)->where('bulk_flag','1')->update(['bulk_flag'=>'0']);
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
     * The discount list for DealerCode 
     *
     * @return \Illuminate\Http\Response
     */
    public function getDiscount(BulkDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');    
        $FinanceOption     = $request->get('FinanceOption');
        $date = \DB::raw('NOW()') ;

        $list_discount  = \DB::table('discounts as d')->join('vindiscounts as v','v.discount_id','=','d.id')->join('financediscounts as f','f.discount_id','=','d.id') ->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
            \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount')->where([['d.dealer_code','=',$DealerCode],['f.finance_option','=',$FinanceOption],['d.bulk_flag','=','1'],['d.end_date','>=',$date]])->orWhere([['d.dealer_code','=',$DealerCode],['d.bulk_flag','=','1'],['d.discount_saved','=','1'],['f.finance_option','=',$FinanceOption],['d.end_date','>=',$date]])->groupBy('d.updated_at','d.discount_name')->orderBy('d.updated_at','DESC')->get();
        $discount_array = array();
       if(!$list_discount->isEmpty()){
            $list_discount = $list_discount->toArray();
            foreach ($list_discount as $key => $value) {
                $data = (array) $value;
                if (count($discount_array) > 5){
                    break;
                }
                //print_r(array_column($discount_array, 'name_of_discount'));
                if(!in_array($data['name_of_discount'], array_column($discount_array, 'name_of_discount'))){
                    array_push($discount_array,$data); 
                }
            }
        }
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['discounts'] = $discount_array;
        return response()->json($response, $this->successStatus); 
    }

    public function clearAllDiscounts(BulkDiscountRequest $request)
    {
        //Validate Request Params
        $validator = $request->validated();
        $result = $request->all();         
        unset($result['Discount']);
        $result['StatusCode'] = 1000;
        $result['Message'] = $this->successMessage['deleteall'];
        $NameOfAPI          = $request->get('NameOfAPI');
        $DealerCode         = $request->get('DealerCode');
        $FinanceOption      = $request->get('FinanceOption');
        $VinNumber          = $request->get('VinNumber'); 
        if(empty($VinNumber)){
           $result['Message'] = "Empty values cannot be deleted";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }

        if(!empty($VinNumber)){
            foreach ($VinNumber as $k => $v) {
                $exist_discount = $this->existDiscountCount($DealerCode,$v,$FinanceOption);
                if(0 < $exist_discount){
                    $discount_ids  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where(['f.finance_option' => $FinanceOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$v])->select('d.id as discount_id')->get();
                    $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
                    $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->where('dealer_code',$DealerCode)->where('vin',$v)->delete();
                    $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_id_array)->delete();
                    $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();
                    $this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
                    $max = \DB::table('discounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE discounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('vindiscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE vindiscounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('financediscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE financediscounts AUTO_INCREMENT =  $max");
                    $max = \DB::table('filterdiscounts')->max('id') + 1; 
                    \DB::statement("ALTER TABLE filterdiscounts AUTO_INCREMENT =  $max");
                    $result['Message'] = $this->successMessage['deleteall'];
                }
            }
        }
        return response()->json($result, $this->successStatus);
    }
    
  /*  public function validateDIscountMaxPrice($request)
    {
        $exced = [];
        $couponcount = [];
        $FinanceOption = $request->FinanceOption;
        $DealerCode = $request->DealerCode;
        $vin = $request->VinNumber;
        $list = \DB::table('fca_ore_input')->whereIN('vin', $vin)->pluck('msrp', 'vin');
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
	}*/
}
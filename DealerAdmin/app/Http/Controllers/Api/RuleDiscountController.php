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

use App\Http\Requests\RuleDiscountRequest;

use DB;

class RuleDiscountController extends Controller
{
    use CacheTrait, APIRequestTrait;
    public $successStatus = 200;
    public $successMessage = array(
        "add" => "Automated Discount has been applied Successfully for specific vehicle.",
        "edit" => "Automated Discount has been updated and applied Successfully for specific vehicle.",
        "delete" => "Automated Discount has been removed successfully for specific vehicle."
    );

    public $errorMessage = array(
        "add" => "Automated Discount is not applied for specific vehicle.",
        "edit" => "Automated Discount is not updated and applied for specific vehicle.",
        "delete" => "Automated Discount is not removed for specific vehicle."
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
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddDiscount_api(RuleDiscountRequest $request)
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
        $ExcludeVinList           = $request->get('ExcludeVinList'); 
        $filterGroupId           = $request->get('filterGroupId'); 
        
        $error_array = array();
        $list_discount = array();
        $update_list_discount = array();
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
            if(config('ore.discounts.maxAmount5000Allowed')){
                $result['Message'] = 'Maximum allowed discount is $5000';
                $result['StatusCode'] = 1001;
                return response()->json($result, $this->successStatus);                
            }
        }

        if (count($rs[1]) > 0) {
            $result['Message'] = 'Maximum allowed number of discount is 5 ';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }
         /*$discount_unique = array_unique($array_discount);
         foreach ($discount_unique as $key => $value) {
         	$discount = $this->checkVinDiscountExists($DealerCode,$financeOption,$value);
         	if(!$discount->isEmpty()) {
                //return error response
                array_push($error_array,$value);
                continue;
            }
         }*/
        foreach ($Discount as $key => $value) {
            $start_date = date('Y-m-d 00:00:00',strtotime($value['discount_start_date']));
            $end_date = date('Y-m-d 23:59:59',strtotime($value['discount_end_date']));           
            $data_array = array (
                'dealer_code' => $DealerCode,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? NULL: floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? NULL: floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount'],
                'inventory_option' => $value['inventory'],
                'rule_flag' => '1'
            );
            if(array_key_exists('discount_filter_id', $value)){
                if($filterGroupId != $value['discount_filter_id']){
                    $discount = $this->checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$value['name_of_discount']);
                    if(!$discount->isEmpty()) {
                        //return error response
                        array_push($error_array,$value['name_of_discount']);
                        continue;
                    } 
                    $total_discount_amount += $data_array['flat_rate'];
                    $data_array['uuid'] = \DB::raw('uuid()');
                    array_push($list_discount,$data_array); 
                    continue;
                }
            }
                $total_discount_amount += $data_array['flat_rate'];
                $data_array['uuid'] = \DB::raw('uuid()');
                array_push($list_discount,$data_array);                
        }

        if(!empty($error_array)){
            $result['Message'] = '“'.implode(',',$error_array).'” already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
         $MakeCode = 'Y';
         $ModelYear = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('model_year');
         $Model = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('model');
         $Trim = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('trim');
         $this->deleteFilterDiscountsToptoBottom($DealerCode,$financeOption,$MakeCode,$ModelYear,$Model, $Trim);
         $update_array = array();
        if(empty($list_discount)){
           $result['Message'] = "Empty values cannot be updated";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }
        $create_array = array();
        if(!empty($update_list_discount)){
            foreach ($update_list_discount as $key => $value) {
                $update_flag = false;
                $update_rule_discount = $update_list_discount[$key];
                $update_rule_discount['vins'] = array();
                foreach ($VinNumber as $k => $vin) {
                    $update_list_discount[$key]['vin'] = $vin;
                    $ruleExistDiscount = $this->checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$value['discount_name'],$vin);
                    if($ruleExistDiscount->isEmpty()){
                        $update_list_discount[$key]['uuid'] = \DB::raw('uuid()');
                        array_push($create_array,$update_list_discount[$key]);
                        continue;
                    }
                    array_push($update_rule_discount['vins'],$vin);
                }                
                array_push($update_array,$update_rule_discount);
            }
        }
        //$update_discount_names = array_column($update_array, 'discount_name');
        $update_discount_names = array();
        \Log::info('-----------------------update_discount_names ----------------');
        \Log::info($update_discount_names);
        if(!empty($list_discount)){
            foreach ($list_discount as $key => $value) {
                $this->deleteFilterDiscountOnSameDiscount($DealerCode,$value['discount_name'],$filterGroupId,$ExcludeVinList);
                foreach ($VinNumber as $k => $vin) {
                    $list_discount[$key]['vin'] = $vin;
                    //$ruleExistDiscount = $this->checkVinDiscountExists($DealerCode,$vin,$financeOption,$value['discount_name']);
                    array_push($create_array,$list_discount[$key]);
                    $this->overwriteVinDiscount($DealerCode,$vin,$financeOption,$update_discount_names);
                }                
            }
        }
        \Log::info('-----------------------Create array ----------------');
        \Log::info($create_array);
        \Log::info('-----------------------Upadte array ----------------');
        \Log::info($update_array);
        //\Log::info($list_discount);
         if(!empty($update_array)){
            $status_flag = true;
            foreach ($update_array as $key => $value) {
                if(empty($value['vins'])){
                    continue;
                }
                $update_contents = array(
                    'flat_rate' => $value['flat_rate'],
                    'percent_offer' => $value['percent_offer'],
                    'start_date' => $value['start_date'],
                    'end_date' => $value['end_date'],
                    'discount_saved' => $value['discount_saved'],
                    'inventory_option' => $value['inventory_option']
                );
                $wherecondition = array(
                    'v.dealer_code' => $DealerCode,
                    'd.rule_flag'=>1,
                    'f.finance_option' => $financeOption,
                    'd.discount_name'=>$value['discount_name']
                );
                $status = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where($wherecondition)->whereIn('v.vin',$value['vins'])->update($update_contents);
                if(!$status){
                    $status_flag = false;
                }
            }
            $result['StatusCode'] = 1000;
            $result['Message'] = $this->successMessage['add'];
         }
        //$result['Message'] = $this->successMessage['add'];
        //return response()->json($result, $this->successStatus);
        $result['Discount'] = array();
        //\Log::info($list_discount);
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            $filtergroup_discount = array();
            foreach ($create_array as $key => $value) {
            	$discount_name = $value['discount_name'];
                $discount_insert = $this->modelDiscount->create($value);
            	$result['Discount'][$discount_name] = $discount_insert->id;
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
                $filter_discount_array = array(
                    'discount_id' => $last_discount_id,
                    'filtergroup_id' => $filterGroupId
                );
                array_push($filtergroup_discount, $filter_discount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status = $this->modelFilterDiscount->insert($filtergroup_discount);
            $status_flag = ($status) ? true : false;
            $result['StatusCode'] = 1000;
            $result['Message'] = $this->successMessage['add'];
            if(!$status_flag){
                $result['StatusCode'] = 1003;
                $result['Message'] = $this->errorMessage['add'];
            }
        }
        $this->updateExcludeVinforFilterGroup($filterGroupId,$ExcludeVinList);
        $this->updateExcludeVinforFilterGroup($filterGroupId,$VinNumber,'includevins');
        return response()->json($result, $this->successStatus);
    }

    /**
     * Store a newly created discount against VIN & Dealer code in Database.
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddDiscount(RuleDiscountRequest $request)
    {   
        $this->ExecuteSettings();
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
        $ExcludeVinList           = $request->get('ExcludeVinList'); 
        $filterGroupId           = $request->get('filterGroupId'); 
        
        $error_array = array();
        $list_discount = array();
        $update_list_discount = array();
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
            if(config('ore.discounts.maxAmount5000Allowed')){
                $result['Message'] = 'Maximum allowed discount is $5000';
                $result['StatusCode'] = 1001;
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
            $data_array = array (
                'dealer_code' => $DealerCode,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => !empty($value['percent_offer']) ? '': floatval(str_replace(",",'',str_replace("$","", $value['flat_rate']))),
                'percent_offer' => !empty($value['flat_rate']) ? '': floatval($value['percent_offer']),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'discount_saved' => $value['saved_discount'],
                'inventory_option' => $value['inventory'],
                'rule_flag' => '1'
            );
            if(array_key_exists('discount_filter_id', $value)){
                \Log::info('---------------------discount_filter_id--------------------');
                \Log::info($value['discount_filter_id']);
                if($filterGroupId != $value['discount_filter_id']){
                    $discount = $this->checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$value['name_of_discount']);
                    if(!$discount->isEmpty()) {
                        //return error response
                        array_push($error_array,$value['name_of_discount']);
                        continue;
                    } 
                    $total_discount_amount += floatval($data_array['flat_rate']);
                    //$data_array['uuid'] = \DB::raw('uuid()');
                    array_push($list_discount,$data_array); 
                    continue;
                }
            }
                $total_discount_amount += floatval($data_array['flat_rate']);
                //$data_array['uuid'] = \DB::raw('uuid()');
                array_push($list_discount,$data_array);                
        }

        if(!empty($error_array)){
            $result['Message'] = '“'.implode(',',$error_array).'” already exists. Please use a different discount name';
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
         $MakeCode = 'Y';
         $ModelYear = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('model_year');
         $Model = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('model');
         $Trim = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('trim');
         $this->deleteFilterDiscountsToptoBottom($DealerCode,$financeOption,$MakeCode,$ModelYear,$Model, $Trim);
         $update_array = array();
        if(empty($list_discount)){
           $result['Message'] = "Empty values cannot be updated";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }
        $update_discount_names = array();
        $create_array = array();
        $result['Discount'] = array();
        if(!empty($list_discount)){
            $vinnumberString = !empty($VinNumber) ? implode(",",$VinNumber) : '';
            $excludeVinListString = !empty($excludeVinListString) ? implode(",",$excludeVinListString) : '';
            foreach ($list_discount as $key => $value) {
                //$this->deleteFilterDiscountOnSameDiscount($DealerCode,$value['discount_name'],$filterGroupId,$ExcludeVinList);
                /*foreach ($VinNumber as $k => $vin) {
                    $list_discount[$key]['vin'] = $vin;
                    //$ruleExistDiscount = $this->checkVinDiscountExists($DealerCode,$vin,$financeOption,$value['discount_name']);
                    array_push($create_array,$list_discount[$key]);
                    $this->overwriteVinDiscount($DealerCode,$vin,$financeOption,$update_discount_names);
                }  */ 
                $list_discount[$key]['finance_option'] = $financeOption;
                $list_discount[$key]['filterGroupId'] = $filterGroupId;
                //$list_discount[$key]['vinlist'] = implode(",",$VinNumber);
                //$VinNumberparts = array_chunk($VinNumber, 8);
                $conditionArray = array_values($list_discount[$key]);
                $conditionArray[] = $vinnumberString;
                $conditionArray[] = $excludeVinListString;
                \Log::info('---------------------sp_add_rule_discounts_to_vinlist--------------------');
                \Log::info($conditionArray);
                $discount_result = \DB::select('CALL sp_add_rule_discounts_to_vinlist(?,?,?,?,?,?,?,?,?,?,?,?,?)', $conditionArray);
                \Log::info('---------------------sp_add_rule_discounts_to_vinlist END--------------------');
                \Log::info($discount_result);
                $discount = reset($discount_result);
                $result['Discount'][$value['discount_name']] = $discount->discountid;
            }
        }
        
/*        \Log::info('-----------------------Create array ----------------');
        \Log::info($create_array);
        //\Log::info($list_discount);
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            $filtergroup_discount = array();
            foreach ($create_array as $key => $value) {
                $discount_name = $value['discount_name'];
                $discount_insert = $this->modelDiscount->create($value);
                $result['Discount'][$discount_name] = $discount_insert->id;
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
                $filter_discount_array = array(
                    'discount_id' => $last_discount_id,
                    'filtergroup_id' => $filterGroupId
                );
                array_push($filtergroup_discount, $filter_discount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status = $this->modelFilterDiscount->insert($filtergroup_discount);
            $status_flag = ($status) ? true : false;
            if(!$status_flag){
                $result['StatusCode'] = 1003;
                $result['Message'] = $this->errorMessage['add'];
            }
        }*/
        $result['StatusCode'] = 1000;
        $result['Message'] = $this->successMessage['add'];
        $this->updateExcludeVinforFilterGroup($filterGroupId,$ExcludeVinList);
        $this->updateExcludeVinforFilterGroup($filterGroupId,$VinNumber,'includevins');
        return response()->json($result, $this->successStatus);
    }

    /**
     * Store a newly created discount against VIN & Dealer code in Database.
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddSingleDiscount(RuleDiscountRequest $request)
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
        $ExcludeVinList           = $request->get('ExcludeVinList'); 
        $filterGroupId           = $request->get('filterGroupId'); 

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
            if(config('ore.discounts.maxAmount5000Allowed')){
                $result['Message'] = 'Maximum allowed discount is $5000';
                $result['StatusCode'] = 1001;
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
                'inventory_option' => $value['inventory'],
                'rule_flag' => '1'
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
                $list_discount[$key]['vin'] = $VinNumber;
                //$ruleExistDiscount = $this->checkVinDiscountExists($DealerCode,$vin,$financeOption,$value['discount_name']);
                array_push($create_array,$list_discount[$key]);
                $this->overwriteVinDiscount($DealerCode,$VinNumber,$financeOption,NULL,true);
        }
        $result['Discount'] = array();
        //\Log::info($list_discount);
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            $filtergroup_discount = array();
            foreach ($create_array as $key => $value) {
            	$discount_name = $value['discount_name'];
                $discount_insert = $this->modelDiscount->create($value);
            	$result['Discount'][$discount_name] = $discount_insert->id;
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
                $filter_discount_array = array(
                    'discount_id' => $last_discount_id,
                    'filtergroup_id' => $filterGroupId
                );
                array_push($filtergroup_discount, $filter_discount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status = $this->modelFilterDiscount->insert($filtergroup_discount);
            $this->updateExcludeVinforFilterGroup($filterGroupId,$ExcludeVinList);
            $includevins = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value('includevins');
            $includevins_array = explode(',', $includevins);
            $includevinsList=array_diff($includevins_array,$ExcludeVinList);
            $this->updateExcludeVinforFilterGroup($filterGroupId,$includevinsList,'includevins');
            $status_flag = ($status) ? true : false;
            $result['StatusCode'] = 1000;
            $result['Message'] = $this->successMessage['add'];
            if(!$status_flag){
                $result['StatusCode'] = 1003;
                $result['Message'] = $this->errorMessage['add'];
            }
        }
        return response()->json($result, $this->successStatus);
    }

    /**
     * The vehicles list for DealerCode
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicles_api(RuleDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');
        $FinanceOption     = $request->get('FinanceOption');
        $discount_condition = '(Select count(v.discount_id) as discount_count from discounts as d join vindiscounts as v  on v.discount_id=d.id join financediscounts as f on v.discount_id = f.discount_id where v.vin = i.vin and d.start_date <= Now() and d.end_date >= now() and f.finance_option = '.$FinanceOption.') as has_discount';
        $list_discount  = \DB::table('fca_ore_input as i')
        ->leftJoin('vindiscounts as v','i.vin','=','v.vin')
        ->leftJoin('discounts as d','v.discount_id','=','d.id')
        ->leftJoin('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('i.dealer_code','i.vin','i.msrp','i.make','i.model','i.year','i.trim_desc',\DB::raw($discount_condition),'f.finance_option');
        $conditionArray = ['i.vehicle_type' => 'New', 'i.dealer_code' => $DealerCode];   
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $Make           = $this->getMakeAbbrevationName($MakeCode);
        $conditionArray['i.make'] = $Make;
        if(!empty($ModelYear))
        {
        	$conditionArray['i.year'] = $ModelYear;
        }
         if(!empty($Model))
        {
        	$conditionArray['i.model'] = $Model;
        }
        if(!empty($Trim))
        {
            $conditionArray['i.trim_desc'] = $Trim;
        }
        $list_discount->where($conditionArray);
       $list_discount_qry = $list_discount->groupBy('i.vin');
       $result = $list_discount_qry->get()->toArray();
        $vehicles = array();
        if(!empty($result)){ 
            $list_discount_array = $result;
            $vin_deactive_list = $this->getVinDeactiveList($DealerCode);  
            foreach ($list_discount_array as $key => $value) {
                $data = array();
                if(is_object($value)){
                    $data = (array)$value;                
                }
                $data['vin_active'] = 0;
                if(!empty($vin_deactive_list)){
                    if(in_array($data['vin'], $vin_deactive_list)){
                       $data['vin_active'] = 1;
                    }
                }
                /*check for new vin and add the discounts*/
                $vin_info = array(
                    'dealer_code' => $DealerCode,
                    'make' => $data['make'],
                    'year' => $data['year'],
                    'model' => $data['model'],
                    'trim_desc' => $data['trim_desc'],
                    'vin' => $data['vin']
                );
                \Log::info('---------------------Check Automated rules for Vin--------------------');
                \Log::info($vin_info);
                $add_result = $this->SqlFiltergroups($vin_info, $FinanceOption);
                if($add_result){
                    $data['has_discount'] = 1;
                }
                /*check for new vin and add the discounts ends*/
                $data['msrp_format'] = '$' . number_format($data['msrp']);                
                //$data['has_discount'] = $this->existDiscountCount($DealerCode,$data['vin'],$FinanceOption);
                $data['total_amount'] = 0;
                if($data['has_discount']){
					$data['total_amount'] = $this->dealerDiscountCalculation($data['vin'], $data['msrp'], $FinanceOption);
				}
				$data['total_amount_format'] = '$' . number_format($data['total_amount']);
                array_push($vehicles,$data);
            }
         }
        //print_r((array)$list_discount);die();
        $response = $request->all(); 
        $response['StatusCode'] = 1000;
        $response['total_vehicles'] =count($vehicles);
        $response['vehicles'] =$vehicles;
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The vehicles list for DealerCode using Stored Procedure
     *
     * @return \Illuminate\Http\Response
     */
    public function getVehicles(RuleDiscountRequest $request) {
        $this->ExecuteSettings();
        \Log::info($request->all());
        //Validate Request Params
        //$validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');
        $FinanceOption     = $request->get('FinanceOption');
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $Make           = $this->getMakeAbbrevationName($MakeCode);
        $conditionArray = [$DealerCode,$Make,$FinanceOption]; 
        $conditionArray[] = !empty($ModelYear) ? $ModelYear : '';
        $conditionArray[] = !empty($Model) ? $Model : '';
        if(!empty($Trim)){
            $Trim = str_replace('"', "&quot;", $Trim);
            $Trim = str_replace("'", '&#39;', $Trim);
        }
        $conditionArray[] = !empty($Trim) ? $Trim : '';
        //#CALL sp_get_rule_vehicle('42885','JEEP','1','Cherokee','2020','Latitude');
        \Log::info('---------------------sp_get_rule_vehicle--------------------');
        \Log::info($conditionArray);
        $result = \DB::select('CALL sp_get_rule_vehicle(?,?,?,?,?,?)', $conditionArray);
        $vehicles = array();
        if(!empty($result)){ 
            $list_discount_array = $result;
            $vin_deactive_list = $this->getVinDeactiveList($DealerCode);  
            foreach ($list_discount_array as $key => $value) {
                $data = array();
                if(is_object($value)){
                    $data = (array)$value;                
                }
                $data['vin_active'] = 0;
                if(!empty($vin_deactive_list)){
                    if(in_array($data['vin'], $vin_deactive_list)){
                       $data['vin_active'] = 1;
                    }
                }
                /*check for new vin and add the discounts*/
                $vin_info = array(
                    'dealer_code' => $DealerCode,
                    'make' => $data['make'],
                    'year' => $data['year'],
                    'model' => $data['model'],
                    'trim_desc' => $data['trim_desc'],
                    'vin' => $data['vin']
                );
                \Log::info('---------------------Check Automated rules for Vin--------------------');
                \Log::info($vin_info);
                //$add_result = $this->SqlFiltergroups($vin_info, $FinanceOption);
                /*if($add_result){
                    $data['has_discount'] = 1;
                }*/
                /*check for new vin and add the discounts ends*/
                $data['msrp_format'] = '$' . number_format($data['msrp']);                
                $data['has_discount'] = intval($data['has_discount']);
                //$data['has_discount'] = $this->existDiscountCount($DealerCode,$data['vin'],$FinanceOption);
                //$data['total_amount'] = 0;
               /* if($data['has_discount']){
                    $data['total_amount'] = $this->dealerDiscountCalculation($data['vin'], $data['msrp'], $FinanceOption);
                }*/

                $data['total_amount_format'] = '$' . number_format($data['total_amount']);
                array_push($vehicles,$data);
            }
         }
        //print_r((array)$list_discount);die();
        $response = $request->all(); 
        $response['StatusCode'] = 1000;
        $response['total_vehicles'] =count($vehicles);
        $response['vehicles'] =$vehicles;
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The discount record will delete based on discountID
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteSavedDiscount(RuleDiscountRequest $request,$uuid)
    {
        //Validate the Request Params
        $result = array();     
        $result = $request->all();     
        $result['uuid'] = $uuid;
        $discount_name = $request->get('discount_name');   
        $result['discount_name'] = $discount_name;
        $DealerCode = $request->get('DealerCode');
        $filterGroupId           = $request->get('filterGroupId');
        $result['StatusCode'] = 1000;         
        $discount = \DB::table('discounts as d')->join('filterdiscounts as fl','d.id','=','fl.discount_id')->where('d.discount_name',$discount_name)->where('d.dealer_code',$DealerCode)->where('fl.filtergroup_id',$filterGroupId)->select('d.*')->get();
        //$discount = $this->modelDiscount->where('discount_name',$discount_name)->where('dealer_code',$DealerCode)->get();
        if(!$discount->isEmpty()){
            $discount_array = $discount->toArray();
            $discount_ids_array = array_column($discount_array,'id');
            $result['Message'] = $this->successMessage['delete'];
            //$status = $this->modelDiscount->where('uuid',$uuid)->delete();
            $status = $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_ids_array)->delete();
            if(!$status){
                $result['Message'] = $this->errorMessage['delete'];
                $result['StatusCode'] = 1003;
            } 
            $status = $this->modelDiscountFinance->whereIn('discount_id',$discount_ids_array)->delete();
            $status = $this->modelVinDiscount->whereIn('discount_id',$discount_ids_array)->delete();
            $status = $this->modelFilterDiscount->whereIn('discount_id',$discount_ids_array)->delete();
        }else{
            $result['StatusCode'] = 1004;
            $result['Message'] = "Resource not found";  
        }
        return response()->json($result, $this->successStatus);
    }


    /**
     * The discount record will delete based on discountID
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAddedDiscount(RuleDiscountRequest $request)
    {
        //Validate the Request Params
        $validator = $request->validated();  
        $result = $request->all();  
        $result['StatusCode'] = 1000;   
        $discount_name = $request->get('discount_name');      
        $DealerCode = $request->get('DealerCode'); 
        $filterGroupId           = $request->get('filterGroupId');  
        \Log::info('-------deleteAddedDiscount----------------');
        \Log::info($DealerCode);
        \Log::info($filterGroupId);   
        \Log::info($discount_name);   
        $discount = \DB::table('discounts as d')->join('filterdiscounts as fl','d.id','=','fl.discount_id')->where('d.discount_name',$discount_name)->where('d.dealer_code',$DealerCode)->where('fl.filtergroup_id',$filterGroupId)->select('d.*')->get();
        \Log::info($discount); 
        if(!$discount->isEmpty()){
            $discount_array = $discount->toArray();
            \Log::info($discount_array);
            $discount_ids_array = array_column($discount_array,'id');
            \Log::info($discount_ids_array);
            $result['Message'] = $this->successMessage['delete'];
            if($discount_ids_array){
                $status = $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_ids_array)->delete();
                if(!$status){
                    \Log::info('Not deleteAddedDiscount');
                    $result['Message'] = $this->errorMessage['delete'];
                    $result['StatusCode'] = 1003;
                } 
                $status = $this->modelDiscountFinance->whereIn('discount_id',$discount_ids_array)->delete();
                $status = $this->modelVinDiscount->whereIn('discount_id',$discount_ids_array)->delete();
                $status = $this->modelFilterDiscount->whereIn('discount_id',$discount_ids_array)->delete();                
            }
        }else{
            $result['StatusCode'] = 1004;
            $result['Message'] = "Resource not found";  
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
            $status = $this->modelFilterDiscount->whereIn('discount_id',$discount_ids_array)->delete();
            /*if(!$status){
                $result['Message'] = $this->errorMessage['delete'];
                $result['StatusCode'] = 1003;
            }*/ 
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
    public function getDiscount(RuleDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');    
        $FinanceOption     = $request->get('FinanceOption');
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $discount_array = array();
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['discounts'] = $discount_array;
        $response['saved_discounts'] = 0;
        $response['counts'] = array(
            'vehicle_discount_count' => 0,
            'saved_discount_count' => 0
        );
        /*
        * Get filter group ID
        */

        $filter_group = $this->SqlQueriesgetFiltergroupSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
        if($filter_group->isEmpty()){
            return $response;
        }
        $filter_group_result = $filter_group->toArray();
        $filter_ids = array_column($filter_group_result, 'id');

        $conditionArray = ['v.dealer_code' => $DealerCode,'f.finance_option' => $FinanceOption,'d.rule_flag'=>'1'];  
        $date = \DB::raw('NOW()') ;
        $list_discount  = \DB::table('vindiscounts as v')
        ->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')
        ->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('fl.filtergroup_id','d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
            \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount','f.finance_option')->where([['d.end_date','>=',$date]]);
        //->where([['d.start_date','<=',$date],['d.end_date','>=',$date]]);
        $list_discount = $list_discount->where($conditionArray)->whereIn('fl.filtergroup_id',$filter_ids)->groupBy('d.updated_at','d.discount_name')->orderBy('d.updated_at','DESC')->get();
        $discount_array = array();
        if($list_discount->isEmpty()){
            $list_discount  = \DB::table('discounts as d')
            ->join('filterdiscounts as fl','d.id','=','fl.discount_id')
            ->join('discountfiltergroups as df','df.id','=','fl.filtergroup_id')     
            ->select('fl.filtergroup_id','d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
                \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount','df.payment_mode')->where([['d.end_date','>=',$date],['d.dealer_code','=',$DealerCode],['d.rule_flag','=','1']]);
            //->where([['d.start_date','<=',$date],['d.end_date','>=',$date]]);
            $list_discount = $list_discount->whereIn('fl.filtergroup_id',$filter_ids)->groupBy('d.updated_at','d.discount_name')->orderBy('d.id','ASC')->get();
        }

       if(!$list_discount->isEmpty()){
            $list_discount = $list_discount->toArray();
            foreach ($list_discount as $key => $value) {
                $data = (array) $value;
                if (count($discount_array) > 5){
                    break;
                }
                if(!in_array($data['name_of_discount'], array_column($discount_array, 'name_of_discount'))){
                    array_push($discount_array,$data); 
                }
            }
        }
        /*
        * Get saved discount count
        */
        $response['discounts'] = $discount_array;
        $saved_discount = $this->SqlQueriesSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model,$Trim);
        $response['saved_discounts'] =array_unique(array_column($saved_discount->toArray(), 'name_of_discount'));
        $response['counts'] = array(
            'vehicle_discount_count' => count($discount_array),
            'saved_discount_count' => count($response['saved_discounts']),

        );
        return response()->json($response, $this->successStatus); 
    }

    /**
     * The discount list for DealerCode 
     *
     * @return \Illuminate\Http\Response
     */
    public function getVinDiscount(RuleDiscountRequest $request) {
        //Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');    
        $FinanceOption     = $request->get('FinanceOption');
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $vin               = $request->get('vin');
        $discount_array = array();
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['discounts'] = $discount_array;
        $response['saved_discounts'] = 0;
        $response['counts'] = array(
            'vehicle_discount_count' => 0,
            'saved_discount_count' => 0
        );
        /*
        * Get filter group ID
        */
        $filter_group = $this->SqlQueriesgetFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
        if($filter_group->isEmpty()){
            return $response;
        }
        $filter_group_result = $filter_group->toArray();
        $filter_group = $filter_group_result[0];
        $filter_id = $filter_group->id;
        //,'fl.filtergroup_id'=>$filter_id,

        $conditionArray = ['v.dealer_code' => $DealerCode,'f.finance_option' => $FinanceOption,'d.rule_flag'=>'1','v.vin'=>$vin];  
        $date = \DB::raw('NOW()') ;
        $list_discount  = \DB::table('vindiscounts as v')
        ->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')
        ->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('fl.filtergroup_id','d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
            \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount','f.finance_option')->where([['d.end_date','>=',$date]]);
        //->where([['d.start_date','<=',$date],['d.end_date','>=',$date]])
        $list_discount = $list_discount->where($conditionArray)->groupBy('d.discount_name')->orderBy('d.updated_at','DESC')->get();
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
        /*
        * Get saved discount count
        */
        $response['discounts'] = $discount_array;
        $saved_discount = $this->SqlQueriesSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model,$Trim);
        $response['saved_discounts'] =array_unique(array_column($saved_discount->toArray(), 'name_of_discount'));
        $response['counts'] = array(
            'vehicle_discount_count' => count($discount_array),
            'saved_discount_count' => count($response['saved_discounts']),

        );
        return response()->json($response, $this->successStatus); 
    }

    public function savedDiscount(RuleDiscountRequest $request)
    {
    	//Validate Request Params
        $validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');    
        $FinanceOption     = $request->get('FinanceOption');
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $discount_array = array();
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['discounts'] = $discount_array;
        $response['filterGroup'] = $discount_array;
       $result = $this->SqlQueriesSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model,$Trim);
        if($result->isEmpty()){
            return response()->json($response, $this->successStatus); 
        }
        if(!$result->isEmpty()){
            $list_discount = $result->toArray();
            foreach ($list_discount as $key => $value) {
                $data = (array) $value;
                if (count($discount_array) > 5){
                    break;
                }
                if(!in_array($data['name_of_discount'], array_column($discount_array, 'name_of_discount'))){
                    array_push($discount_array,$data); 
                }
            }
        }
        $response['discounts'] = $discount_array;
        $filter_group = $this->SqlQueriesgetFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model,$Trim);
        if($filter_group->isEmpty()){
            return $response;
        }
        $filter_group_result = $filter_group->toArray();
        $response['filterGroup'] = $filter_group_result[0];
        return response()->json($response, $this->successStatus); 
    }

    public function deleteFilterDiscountOnSameDiscount($DealerCode,$discount_name,$filterGroupId,$excludevins)
    {
        $exclude_discounts_ids = array();
        if($excludevins){
            $exclude_discount_id  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('filterdiscounts as f','f.discount_id','=','d.id')->where(['f.filtergroup_id' => $filterGroupId, 'd.dealer_code' => $DealerCode])->whereIn('v.vin',$excludevins)->select('d.id as discount_id')->get();
            $exclude_discounts_ids = array_column($exclude_discount_id->toArray(), 'discount_id');
        }
        $discount_ids  = \DB::table('discounts as d')->join('filterdiscounts as f','f.discount_id','=','d.id')->where(['f.filtergroup_id' => $filterGroupId, 'd.dealer_code' => $DealerCode,'d.discount_name' => $discount_name])->select('d.id as discount_id');
        if($exclude_discounts_ids){
            $discount_ids = $discount_ids->whereNotIn('d.id',$exclude_discounts_ids);
        }
        $discount_ids  = $discount_ids->get();
        \Log::info($discount_ids);
        $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
        if($discount_id_array){
            $this->modelDiscount->where('dealer_code',$DealerCode)->whereIn('id',$discount_id_array)->delete();
            $this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
            $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->delete();
            $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();            
        }
    }

    public function overwriteVinDiscount($DealerCode,$vin,$financeOption,$exclude_discounts = NULL,$flag =false)
    {
        $rule_flag = $flag ? 1: 1;
    	$exist_discount = $this->existDiscountCount($DealerCode,$vin,$financeOption);
        if(0 < $exist_discount){
            $discount_ids  = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where(['f.finance_option' => $financeOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$vin])->select('d.id as discount_id');
            if(!empty($exclude_discounts)){
                 $discount_ids  = $discount_ids->whereNotIn('d.discount_name',$exclude_discounts);
            }
            $discount_ids  = $discount_ids->get();
            \Log::info($discount_ids);
            $discount_id_array = array_column($discount_ids->toArray(), 'discount_id');
            \Log::info($discount_id_array);
            if($discount_id_array){
                $this->modelVinDiscount->whereIn('discount_id',$discount_id_array)->where('dealer_code',$DealerCode)->where('vin',$vin)->delete();
                $this->modelDiscountFinance->whereIn('discount_id',$discount_id_array)->delete();
                $this->modelDiscount->where('dealer_code',$DealerCode)->where('rule_flag','0')->whereIn('id',$discount_id_array)->delete();
                //$this->modelFilterDiscount->whereIn('discount_id',$discount_id_array)->delete();
                $max = \DB::table('discounts')->max('id') + 1; 
                \DB::statement("ALTER TABLE discounts AUTO_INCREMENT =  $max");
                $max = \DB::table('financediscounts')->max('id') + 1; 
                \DB::statement("ALTER TABLE financediscounts AUTO_INCREMENT =  $max");
                //$max = \DB::table('filterdiscounts')->max('id') + 1; 
                //\DB::statement("ALTER TABLE filterdiscounts AUTO_INCREMENT =  $max");                
            }
        }
    }

    public function checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$discount_name = NULL,$vin=NULL)
    {
    	$date = \DB::raw('NOW()') ; 
       $exist_count  = \DB::table('vindiscounts as v')->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id');
        $conditionArray = ['f.finance_option' => $financeOption, 'v.dealer_code' => $DealerCode,'d.rule_flag' => 1,'fl.filtergroup_id'=>$filterGroupId];
        
        if(!empty($vin)){
            $conditionArray['v.vin'] = $vin;
        }
        if(!empty($discount_name)){
            if(!is_array($discount_name)){
            	$exist_count = $exist_count->where(['d.discount_name'=>$discount_name]);        	
            }else{
            	$exist_count = $exist_count->whereIn('d.discount_name',$discount_name); 	
            }            
        }
        $exist_count = $exist_count->where($conditionArray)->get();
        //->where(['f.finance_option' => $financeOption, 'v.dealer_code' => $DealerCode, 'v.vin'=>$vin])->where([['end_date','>=',$date],['start_date','<=',$date]])->get();
       return $exist_count;
    }


    public function SqlQueriesSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim)
    {
        /*
        * Get filter group ID
        */
        /*$filter_group = $this->SqlQueriesgetFiltergroupSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
        if($filter_group->isEmpty()){
            return $filter_group;
        }
        $filter_group_result = $filter_group->toArray();
        $filter_ids = array_column($filter_group_result, 'id');*/
        $filter_group = $this->SqlQueriesgetFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
        if($filter_group->isEmpty()){
            return $filter_group;
        }
        $filter_group_result = $filter_group->toArray();
        $filter_ids = array_column($filter_group_result, 'id');
       $conditionArray = ['v.dealer_code' => $DealerCode,'f.finance_option' => $FinanceOption,'d.rule_flag'=>'1']; 
       $list_discount  = \DB::table('vindiscounts as v')
        ->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')
        ->join('discounts as d','v.discount_id','=','d.id')
        ->join('financediscounts as f','v.discount_id','=','f.discount_id')        
        ->select('fl.filtergroup_id','d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
            \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount','f.finance_option')->where([['d.discount_saved','=','1']]);
        //->where([['d.start_date','<=',$date],['d.end_date','>=',$date]]);
        $result = $list_discount->where($conditionArray)->whereIn('fl.filtergroup_id',$filter_ids)->groupBy('d.updated_at','d.discount_name')->orderBy('d.updated_at','DESC')->get();
       if($result->isEmpty()){
            $list_discount_qry  = \DB::table('discounts as d')
            ->join('filterdiscounts as fl','d.id','=','fl.discount_id')
            ->join('discountfiltergroups as df','df.id','=','fl.filtergroup_id')     
            ->select('fl.filtergroup_id','d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','percent_offer',
                \DB::raw('DATE_FORMAT(d.start_date,"%m/%d/%Y") AS discount_start_date'),\DB::raw('DATE_FORMAT(d.end_date,"%m/%d/%Y") AS discount_end_date'),'d.discount_saved as saved_discount','df.payment_mode')->where([['d.discount_saved','=','1'],['d.dealer_code','=',$DealerCode],['d.rule_flag','=','1']]);
            //->where([['d.start_date','<=',$date],['d.end_date','>=',$date]]);
            $result = $list_discount_qry->whereIn('fl.filtergroup_id',$filter_ids)->groupBy('d.discount_name')->orderBy('d.id','ASC')->get();
        }
       return $result;
    }

    public function addFilter(Request $request)
    {
        //Validate Request Params
        //$validator = $request->validated();
        $NameOfAPI      = $request->get('NameOfAPI');
        $DealerCode     = $request->get('DealerCode');    
        $FinanceOption     = $request->get('FinanceOption');
        $MakeCode           = $request->get('MakeCode');
        $ModelYear          = $request->get('ModelYear');
        $Model              = $request->get('Model');
        $Trim               = $request->get('Trim');
        $result = array();
        $result = $this->SqlQueriesFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model,$Trim);
        $response = $request->all();
        $response['StatusCode'] = 1000;
        $response['Message'] = $this->successMessage['add'];
        $response['filterGroup'] = $result;
        return response()->json($response, $this->successStatus); 
    }

    public function SqlQueriesFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim)
    {
        $paramCount = 1;
        $Make           = $this->getMakeAbbrevationName($MakeCode);
        $conditionArray = ['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption];   
        if(!empty($Make) && empty($ModelYear) && empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->whereNull('model_year')->whereNull('model')->whereNull('trim')->get();
            if($filter_group_result->isEmpty()){
               // $this->deleteFilterDiscountsToptoBottom($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
                $result = \DB::table('discountfiltergroups')->insert($conditionArray);
            }
        }
        if(!empty($Make) && !empty($ModelYear) && empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $conditionArray['model_year'] = $ModelYear;
            $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->whereNull('model')->whereNull('trim')->get();
            if($filter_group_result->isEmpty()){
               // $this->deleteFilterDiscountsToptoBottom($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);            
                $result = \DB::table('discountfiltergroups')->insert($conditionArray);
            }
        }
        if(!empty($Make) && !empty($ModelYear) && !empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $conditionArray['model_year'] = $ModelYear;
            $conditionArray['model'] = $Model;
            $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->whereNull('trim')->get();
            if($filter_group_result->isEmpty()){
                //$this->deleteFilterDiscountsToptoBottom($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim);
                $result = \DB::table('discountfiltergroups')->insert($conditionArray);
            }
        }
        if(!empty($Make) && !empty($ModelYear) && !empty($Model) && !empty($Trim)){
             $conditionArray['make'] = $Make;
            $conditionArray['model_year'] = $ModelYear;
            $conditionArray['model'] = $Model;
            $conditionArray['trim'] = $Trim;
             $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->get();
             if(!$filter_group_result->isEmpty()){
                $filter_group_array = $filter_group_result->toArray();
                $result = \DB::table('discountfiltergroups')->where('id',$filter_group_array[0]->id)->update($conditionArray);                
            }else{
                $result = \DB::table('discountfiltergroups')->insert($conditionArray);
            }
        }

        $filter_group  = \DB::table('discountfiltergroups');
        if(!empty($ModelYear))
        {
             $filter_group->where('model_year', $ModelYear);
        }else{
            $filter_group->whereNull('model_year');
        }
         if(!empty($Model))
        {
            $filter_group->where('model', $Model);
        }else{
            $filter_group->whereNull('model');
        }
        if(!empty($Trim))
        {
           $filter_group->where('trim', $Trim);
        }else{
            $filter_group->whereNull('trim');
        }
        //$selectRawCondition = '(IF(make = '.$Make.',1,0) + IF(model_year = '.$ModelYear.',1,0) + IF(model = '.$Model.',1,0) + IF(trim = '.$Trim.',1,0)) AS matches';
        $filter_group_qry  = $filter_group->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make]);
        $filter_group_result  = $filter_group_qry->select('*')->get();
        //print_r($filter_group_result);
        if($filter_group_result->isEmpty()){
            return $filter_group_result;
        }
        $result =  $filter_group_result->toArray();
        return $result[0];
    }

    public function SqlQueriesgetFiltergroupSavedDiscount($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim)
    {
       $Make           = $this->getMakeAbbrevationName($MakeCode);
        $filter_group  = \DB::table('discountfiltergroups')->where('dealer_code' , $DealerCode)->where('payment_mode' , $FinanceOption);
        if(!empty($Make) && empty($ModelYear) && empty($Model) && empty($Trim)){
            $filter_group_qry_level1  = $filter_group->where('make', $Make)->whereNull('model_year')->whereNull('model')->whereNull('trim');
            $count_level1 = $filter_group_qry_level1->count();
            \Log::info('L1: '.$count_level1);
            $filter_group_result  = $filter_group_qry_level1->get();
            return $filter_group_result;
        }
        if(!empty($Make) && !empty($ModelYear) && empty($Model) && empty($Trim)){
            /*$filter_group_qry  = $filter_group->where(function($q) use ($Make,$ModelYear) {
                 $q->where(['make' => $Make,'model_year' => NULL,'model' => NULL,'trim' => NULL])
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->where('model' , NULL)->where('trim' , NULL);
             });
            $filter_group_result  = $filter_group_qry->get();
            return $filter_group_result;*/

            $filter_group_qry_level2  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => NULL,'df.trim' => NULL]);
            $count_level2 = $filter_group_qry_level2->count();
            \Log::info('L2: '.$count_level2);
            if(0 < $count_level2){
                $filter_group_result = $filter_group_qry_level2->select('df.*')->get(); 
                return $filter_group_result;
            }                          
            $filter_group_qry_level1 = \App\Discountfiltergroup::where(['payment_mode' => $FinanceOption,'dealer_code' =>$DealerCode,'make' => $Make, 'model_year' => NULL,'model' => NULL,'trim' => NULL]);
            $count_level1 = $filter_group_qry_level1->count();
            \Log::info('L1: '.$count_level1);
            $filter_group_result = $filter_group_qry_level1->get(); 
            return $filter_group_result;
        }
        if(!empty($Make) && !empty($ModelYear) && !empty($Model) && empty($Trim)){
/*            $filter_group_qry  = $filter_group->where(function($q) use ($Make,$ModelYear,$Model){
                 $q->where(['make' => $Make,'model_year' => NULL,'model' => NULL,'trim' => NULL])
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->whereNull('model')->whereNull('trim')
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->where('model' , $Model)->whereNull('trim');
             });
            $filter_group_result  = $filter_group_qry->get();
            return $filter_group_result;*/

            $filter_group_qry_level3  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => $Model,'df.trim' => NULL]);
            $count_level3 = $filter_group_qry_level3->count();
            \Log::info('L3: '.$count_level3);
            if(0 < $count_level3){
                $filter_group_result = $filter_group_qry_level3->select('df.*')->get(); 
                return $filter_group_result;
             }            
            $filter_group_qry_level2  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => NULL,'df.trim' => NULL]);
            $count_level2 = $filter_group_qry_level2->count();
            \Log::info('L2: '.$count_level2);
            if(0 < $count_level2){
                $filter_group_result = $filter_group_qry_level2->select('df.*')->get(); 
                return $filter_group_result;
            }                           
            $filter_group_qry_level1 = \App\Discountfiltergroup::where(['payment_mode' => $FinanceOption,'dealer_code' =>$DealerCode,'make' => $Make, 'model_year' => NULL,'model' => NULL,'trim' => NULL]);
            $count_level1 = $filter_group_qry_level1->count();
            \Log::info('L1: '.$count_level1);
            $filter_group_result = $filter_group_qry_level1->get(); 
            return $filter_group_result;
        }
        if(!empty($Make) && !empty($ModelYear) && !empty($Model) && !empty($Trim)){
            /*$filter_group_qry  = $filter_group->where(function($q) use ($Make,$ModelYear,$Model,$Trim){
                 $q->where(['make' => $Make,'model_year' => NULL,'model' => NULL,'trim' => NULL])
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->whereNull('model')->whereNull('trim')
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->where('model' , $Model)->whereNull('trim')
                   ->orWhere('make' , $Make)->where('model_year',$ModelYear)->where('model' , $Model)->where('trim' , $Trim);
             });
            $filter_group_result  = $filter_group_qry->get();
            return $filter_group_result;*/
            $filter_group_qry_level4  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => $Model,'df.trim' => $Trim]);
            $count_level4 = $filter_group_qry_level4->count();
            \Log::info('L4: '.$count_level4);
            if(0 < $count_level4){
                $filter_group_result = $filter_group_qry_level4->select('df.*')->get(); 
                return $filter_group_result;
             }
            $filter_group_qry_level3  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => $Model,'df.trim' => NULL]);
            $count_level3 = $filter_group_qry_level3->count();
            \Log::info('L3: '.$count_level3);
            if(0 < $count_level3){
                $filter_group_result = $filter_group_qry_level3->select('df.*')->get(); 
                return $filter_group_result;
             }            
            $filter_group_qry_level2  = \DB::table('discountfiltergroups as df')->join('filterdiscounts as fl','df.id','=','fl.filtergroup_id')->where(['df.payment_mode' => $FinanceOption,'df.dealer_code' =>$DealerCode,'df.make' => $Make, 'df.model_year' => $ModelYear,'df.model' => NULL,'df.trim' => NULL]);
            $count_level2 = $filter_group_qry_level2->count();
            \Log::info('L2: '.$count_level2);
            if(0 < $count_level2){
                $filter_group_result = $filter_group_qry_level2->select('df.*')->get(); 
                return $filter_group_result;
            }                           
            $filter_group_qry_level1 = \App\Discountfiltergroup::where(['payment_mode' => $FinanceOption,'dealer_code' =>$DealerCode,'make' => $Make, 'model_year' => NULL,'model' => NULL,'trim' => NULL]);
            $count_level1 = $filter_group_qry_level1->count();
            \Log::info('L1: '.$count_level1);
            $filter_group_result = $filter_group_qry_level1->get(); 
            return $filter_group_result;
        }
    }

    public function SqlQueriesgetFiltergroup($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim)
    {
       $Make           = $this->getMakeAbbrevationName($MakeCode);  
        $filter_group  = \DB::table('discountfiltergroups');
        if(!empty($ModelYear))
        {
             $filter_group->where('model_year', $ModelYear);
        }else{
            $filter_group = $filter_group->whereNull('model_year');
        }
         if(!empty($Model))
        {
             $filter_group->where('model', $Model);
        }else{
            $filter_group = $filter_group->whereNull('model');
        }
        if(!empty($Trim))
        {
            $filter_group->where('trim', $Trim);
        }else{
            $filter_group = $filter_group->whereNull('trim');
        }
        $filter_group_qry  = $filter_group->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make]);
        $filter_group_result  = $filter_group_qry->get();
        return $filter_group_result;
    }


    public function updateExcludeVinforFilterGroup($filterGroupId,$newexcludevins,$column_name = 'excludevins')
    {
        \Log::info('-----------------------updateiIn/ExcludeVinforFilterGroup ----------------');
        \Log::info($column_name);
        \Log::info($newexcludevins);
        if(empty($newexcludevins)){
            \DB::table('discountfiltergroups')->where('id',$filterGroupId)->update(array($column_name => NULL));
            return false;
        }
        $newexcludevin_array = $newexcludevins;
        //$newexcludevin_array = explode(',', $newexcludevins);
        $excludevin_list = \DB::table('discountfiltergroups')->where('id',$filterGroupId)->value($column_name);
        $updateVins = array();
        $updateVins = $newexcludevin_array;
        /*if(!empty($excludevin_list)){
            $excludevins_array = explode(',', $excludevin_list);
            if(!empty($excludevins_array)){
                //$updateVins = array_intersect($excludevins_array, $newexcludevin_array);
                //$updateVins = $excludevins_array;
            }
        }*/
        \Log::info('-----------------------After Process ----------------');
        \Log::info($updateVins);
        $updateVin_list = !empty($updateVins) ? implode(',', $updateVins) : NULL;
        \Log::info($updateVin_list);
        \DB::table('discountfiltergroups')->where('id',$filterGroupId)->update(array($column_name => $updateVin_list));
    }


    /**
     * Store a newly created discount against VIN & Dealer code in Database.
     * Status - 2 "Bulk Discount"
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function AddSavedDiscount(RuleDiscountRequest $request)
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
        $filterGroupId           = $request->get('filterGroupId'); 
        $error_array = array();
        $list_discount = array();
        $update_list_discount = array();
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
            if(config('ore.discounts.maxAmount5000Allowed')){
                $result['Message'] = 'Maximum allowed discount is $5000';
                $result['StatusCode'] = 1001;
                return response()->json($result, $this->successStatus);
            }
        }

        if (count($rs[1]) > 0) {
            $result['Message'] = 'Maximum allowed number of discount is 5 ';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
        }
         /*$discount_unique = array_unique($array_discount);
         foreach ($discount_unique as $key => $value) {
            $discount = $this->checkVinDiscountExists($DealerCode,$financeOption,$value);
            if(!$discount->isEmpty()) {
                //return error response
                array_push($error_array,$value);
                continue;
            }
         }*/

        foreach ($Discount as $key => $value) {
            /*if(!empty($value['discount_filter_id']) && $filterGroupId != $value['discount_filter_id']){
                    continue;
            }*/
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
                'inventory_option' => $value['inventory'],
                'rule_flag' => '1'
            );
            if(!(array_key_exists('uuid', $value) && array_key_exists('discount_id', $value))) {
                $discount = $this->checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$value['name_of_discount']);
                if(!$discount->isEmpty()) {
                    //return error response
                    array_push($error_array,$value['name_of_discount']);
                    continue;
                } 
                $total_discount_amount += $data_array['flat_rate'];
                $data_array['uuid'] = \DB::raw('uuid()');
                array_push($list_discount,$data_array);                
            }else{
                $data_array['discount_id'] = $value['discount_id'];
                array_push($update_list_discount,$data_array);      
                $total_discount_amount += $data_array['flat_rate'];          
            }
        }
        /*if(5000 < $total_discount_amount){
            $result['Message'] = 'Maximum allowed discount is $5000';
            $result['StatusCode'] = 1001;
            return response()->json($result, $this->successStatus);
         }*/

        if(!empty($error_array)){
            $result['Message'] = 'Following discount name already exists for vehicle - '.implode(',',$error_array);
            $result['StatusCode'] = 1003;
            return response()->json($result, $this->successStatus);
         }
         
         $update_array = array();
        if(empty($update_list_discount) && empty($list_discount)){
           $result['Message'] = "Empty values cannot be updated";
            $result['StatusCode'] = 1002;
            return response()->json($result, $this->successStatus); 
        }
        $create_array = array();
        if(!empty($update_list_discount)){
            foreach ($update_list_discount as $key => $value) {
                $update_flag = false;
                $update_rule_discount = $update_list_discount[$key];
                $update_rule_discount['vins'] = array();
                foreach ($VinNumber as $k => $vin) {
                    $update_list_discount[$key]['vin'] = $vin;
                    $ruleExistDiscount = $this->checkVinDiscountExists($filterGroupId,$DealerCode,$financeOption,$value['discount_name'],$vin);
                    if($ruleExistDiscount->isEmpty()){
                        $update_list_discount[$key]['uuid'] = \DB::raw('uuid()');
                        array_push($create_array,$update_list_discount[$key]);
                        continue;
                    }
                    array_push($update_rule_discount['vins'],$vin);
                }                
                array_push($update_array,$update_rule_discount);
            }
        }
        if(!empty($list_discount)){
            foreach ($list_discount as $key => $value) {
                foreach ($VinNumber as $k => $vin) {
                    $list_discount[$key]['vin'] = $vin;
                    //$ruleExistDiscount = $this->checkVinDiscountExists($DealerCode,$vin,$financeOption,$value['discount_name']);
                    array_push($create_array,$list_discount[$key]);
                    $this->overwriteVinDiscount($DealerCode,$vin,$financeOption);
                }                
            }
        }
        \Log::info('-----------------------Create array ----------------');
        \Log::info($create_array);
        \Log::info('-----------------------Upadte array ----------------');
        \Log::info($update_array);
        //\Log::info($list_discount);
         if(!empty($update_array)){
            $status_flag = true;
            foreach ($update_array as $key => $value) {
                if(empty($value['vins'])){
                    continue;
                }
                $update_contents = array(
                    'flat_rate' => $value['flat_rate'],
                    'percent_offer' => $value['percent_offer'],
                    'start_date' => $value['start_date'],
                    'end_date' => $value['end_date'],
                    'discount_saved' => $value['discount_saved'],
                    'inventory_option' => $value['inventory_option']
                );
                $wherecondition = array(
                    'v.dealer_code' => $DealerCode,
                    'd.rule_flag'=>1,
                    'f.finance_option' => $financeOption,
                    'd.discount_name'=>$value['discount_name']
                );
                $status = \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','v.discount_id','=','f.discount_id')->where($wherecondition)->whereIn('v.vin',$value['vins'])->update($update_contents);
                if(!$status){
                    $status_flag = false;
                }
            }
            $result['StatusCode'] = 1000;
            $result['Message'] = $this->successMessage['add'];
         }
        //$result['Message'] = $this->successMessage['add'];
        //return response()->json($result, $this->successStatus);
        $result['Discount'] = array();
        //\Log::info($list_discount);
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            $filtergroup_discount = array();
            foreach ($create_array as $key => $value) {
                $discount_name = $value['discount_name'];
                $discount_insert = $this->modelDiscount->create($value);
                $result['Discount'][$discount_name] = $discount_insert->id;
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
                $filter_discount_array = array(
                    'discount_id' => $last_discount_id,
                    'filtergroup_id' => $filterGroupId
                );
                array_push($filtergroup_discount, $filter_discount_array);
                /*foreach ($VinNumber as $k => $vin) {
                    $this->overwriteVinDiscount($DealerCode,$vin,$financeOption);
                    $vindiscount_array = array(
                        'discount_id' => $last_discount_id,
                        'dealer_code' => $DealerCode,
                        'vin'         => $vin
                    );
                    array_push($vin_discount, $vindiscount_array);
                }*/
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status = $this->modelFilterDiscount->insert($filtergroup_discount);
            $status_flag = ($status) ? true : false;
            $result['StatusCode'] = 1000;
            $result['Message'] = $this->successMessage['add'];
            if(!$status_flag){
                $result['StatusCode'] = 1003;
                $result['Message'] = $this->errorMessage['add'];
            }
        }
        return response()->json($result, $this->successStatus);
    }

    public function deleteFilterDiscounts($filter_ids)
    {
        \Log::info('deleteFilterDiscounts');
        \Log::info($filter_ids);
        if(!empty($filter_ids)){
            $discount_ids = $this->modelFilterDiscount->whereIn('filtergroup_id',$filter_ids)->pluck('discount_id');
            $status = $this->modelDiscountFinance->whereIn('discount_id',$discount_ids)->delete();
            $status = $this->modelVinDiscount->whereIn('discount_id',$discount_ids)->delete();
            $status = $this->modelDiscount->whereIn('id',$discount_ids)->delete();
            $status = $this->modelFilterDiscount->whereIn('discount_id',$discount_ids)->delete();                    
            //$status = $this->modelFilterDiscount->whereIn('filtergroup_id',$filter_ids)->delete();                    
        }
    }

    public function deleteFilterDiscountsToptoBottom($DealerCode,$FinanceOption,$MakeCode,$ModelYear,$Model, $Trim)
    {
        $Make           = $this->getMakeAbbrevationName($MakeCode);
        $conditionArray = ['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption];   
        if(!empty($Make) && empty($ModelYear) && empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->get();
            if(!$filter_group_result->isEmpty()){
                $filter_group_array = $filter_group_result->toArray();
                $filter_ids = array();
                $level1_filter_ids = \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNotNull('model')->whereNotNull('trim')->select('id')->get();
                \Log::info('level1_filter_ids');
                \Log::info($level1_filter_ids);
                if(!empty($level1_filter_ids))
                {
                   $level1_filter_ids = $level1_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level1_filter_ids, 'id')); 
                }
                                \Log::info('level2_filter_ids');
                $level2_filter_ids =\DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNotNull('model')->whereNull('trim')->select('id')->get();
                \Log::info($level2_filter_ids);
                if(!empty($level2_filter_ids)){
                   $level2_filter_ids = $level2_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level2_filter_ids, 'id')); 
                }
                                                \Log::info('level3_filter_ids');
                $level3_filter_ids =\DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNull('model')->whereNull('trim')->select('id')->get();
                \Log::info($level3_filter_ids);
                if(!empty($level3_filter_ids))
                {
                  $level3_filter_ids = $level3_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level3_filter_ids, 'id'));
                }
                \Log::info('filter_ids');
                \Log::info($filter_ids);
                if(!empty($filter_ids)){
                    $this->deleteFilterDiscounts($filter_ids);                   
                }
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNotNull('model')->whereNotNull('trim')->delete();
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNotNull('model')->whereNull('trim')->delete();
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make])->whereNotNull('model_year')->whereNull('model')->whereNull('trim')->delete();
            }
        }
        if(!empty($Make) && !empty($ModelYear) && empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $conditionArray['model_year'] = $ModelYear;
             $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->get();
             if(!$filter_group_result->isEmpty()){
                $filter_group_array = $filter_group_result->toArray();
                $filter_ids = array();
                $level1_filter_ids = \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear])->whereNotNull('model')->whereNotNull('trim')->select('id')->get();
                if(!empty($level1_filter_ids))
                {
                  $level1_filter_ids = $level1_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level1_filter_ids, 'id'));    
                }                
                $level2_filter_ids = \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear])->whereNotNull('model')->whereNull('trim')->select('id')->get();
                if(!empty($level2_filter_ids))
                {
                  $level2_filter_ids = $level2_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level2_filter_ids, 'id'));    
                }
                \Log::info('filter_ids');
                \Log::info($filter_ids);
                if(!empty($filter_ids)){
                    $this->deleteFilterDiscounts($filter_ids);                   
                }
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear])->whereNotNull('model')->whereNotNull('trim')->delete();
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear])->whereNotNull('model')->whereNull('trim')->delete();
            }
        }
        if(!empty($Make) && !empty($ModelYear) && !empty($Model) && empty($Trim)){
            $conditionArray['make'] = $Make;
            $conditionArray['model_year'] = $ModelYear;
            $conditionArray['model'] = $Model;
             $filter_group_result  = \DB::table('discountfiltergroups')->where($conditionArray)->get();
             if(!$filter_group_result->isEmpty()){
                $filter_group_array = $filter_group_result->toArray();
                 $filter_ids = array();
                $level1_filter_ids = \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear,'model'=>$Model])->whereNotNull('trim')->select('id')->get();
                if(!empty($level1_filter_ids))
                {
                  $level1_filter_ids = $level1_filter_ids->toArray();
                  $filter_ids = array_merge($filter_ids,array_column($level1_filter_ids, 'id'));    
                }
                if(!empty($filter_ids)){
                    $this->deleteFilterDiscounts($filter_ids);                  
                }
                \Log::info('filter_ids');
                \Log::info($filter_ids);
                \DB::table('discountfiltergroups')->where(['dealer_code' => $DealerCode,'payment_mode' => $FinanceOption,'make'=>$Make,'model_year'=>$ModelYear,'model'=>$Model])->whereNotNull('trim')->delete();
            }
        }
    }

    public function validateSingleDIscountMaxPrice($request)
    {
        //dd($request->all());
        $exced = [];
        $couponcount = [];
        $FinanceOption = $request->FinanceOption;
        $DealerCode = $request->DealerCode;
        $vin = $request->VinNumber;
       // dd( $vin);
        $list = \DB::table('fca_ore_input')->where('vin', $vin)->pluck('msrp', 'vin');
        $vinCountResult = \DB::table('vindiscounts')
            ->join('discounts', 'vindiscounts.discount_id', '=', 'discounts.id')
            ->join('financediscounts', 'vindiscounts.discount_id', '=', 'financediscounts.discount_id')
            ->where('vindiscounts.vin', $vin)
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

    /*
    **************************
    * Cron Logic function
    ***************************
    */
    public function SqlFiltergroups($vin_info, $financeoption){      
        $level4 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'],'trim' => $vin_info['trim_desc'] ]);
        $count_level4 = $level4->count();
        \Log::info('L4: '.$count_level4);
        if($count_level4 == 0){       
                $level3 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => $vin_info['model'], 'trim' => NULL  ]);
                $count_level3 = $level3->count();
                \Log::info('L3: '.$count_level3);
                if($count_level3 == 0){               
                $level2 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'],'make' => $vin_info['make'], 'model_year' => $vin_info['year'],'model' => NULL,'trim' => NULL]);               
                    $count_level2 = $level2->count();
                    \Log::info('L2: '.$count_level2);
                    if($count_level2 == 0){                           
                            $level1 = \App\Discountfiltergroup::where(['payment_mode' => $financeoption,'dealer_code' =>$vin_info['dealer_code'], 'make' => $vin_info['make'],'model' => NULL,'trim' => NULL,'model_year' => NULL]);
                            $count_level1 = $level1->count();
                            \Log::info('L1: '.$count_level1);
                            if($count_level1 == 0){ $output = []; } else {$output = $level1->get(); }
                           
                    }else $output = $level2->get();
                   
                } else $output = $level3->get();
        }else $output = $level4->get();
       
       $vin = $vin_info['vin'];
       $dealer_code = $vin_info['dealer_code'];
       if(count($output) == 0){
            //$result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin); 
            \Log::info('No rule Exists. GET individualdiscount ------------------------------------------');
            //\Log::info($result);
            return false;
        }
        ####Include Vins
        if(!$output->isEmpty()){
            $filter_group = $output->toArray();
            \Log::info('filterArray ------------------------------------------');
            \Log::info($filter_group);
            $includevins = $filter_group[0]['includevins'];
            $excludevins = $filter_group[0]['excludevins'];
            $vinFoundFlag = $excludeFound = false;
            if(!empty($includevins)){
                 $vinFoundFlag = $this->checkValueExistsinArray($vin,$includevins);
                 if($vinFoundFlag){
                    //$result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
                    \Log::info('includevins ------------------------------------------');
                    //\Log::info($result);
                    return false;                   
                 }
            }else{
                \Log::info('IncludeVins are empty');
            }
            if(!empty($excludevins)){
                if(!$vinFoundFlag){
                    $vinFoundFlag = $this->checkValueExistsinArray($vin,$excludevins);
                    if($vinFoundFlag){
                        \Log::info('excludevins ------------------------------------------');
                        return false;  
                        //$result = $this->sqlRulediscountforvin($dealer_code,$filter_group[0]['id'],$vin,$financeoption);
                        //\Log::info($result);
                    }                    
                }
            }else{
                \Log::info('Excludevins are empty');
            }
            if(!empty($includevins) || !empty($excludevins)){
                if(!$vinFoundFlag){
                    \Log::info('individualdiscount ------------------------------------------');
                    $individual_result = $this->sqlindividualdiscountforvin($dealer_code,$financeoption,$vin);
                    \Log::info($individual_result);
                    if(0 < count($individual_result)){
                        return false;
                    }
                    \Log::info('filtergroupdiscounts ------------------------------------------');
                    $result = $this->sqlFilterRulediscount($dealer_code,$filter_group[0]['id'],$financeoption);             
                    \Log::info($result);
                    if(0 < count($result)){
                        \Log::info('not empty filtergroupdiscounts');
                        $discounts = $result->toArray();
                        $this->addDiscountofnewlyfoundVin($dealer_code,$filter_group[0]['id'],$financeoption,$vin,$discounts);
                        return true;                             
                    }               
                }
            }else
            \Log::info('IncludeVins and ExcludeVins are empty');
            return false;
        }           
    }

    public function sqlindividualdiscountforvin($dealer_code,$finance_option,$VinNumber)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'v.vin' => $VinNumber,'f.finance_option' => $finance_option])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode','d.inventory_option as inventory')->get();
    }

    public function sqlRulediscountforvin($dealer_code,$filterGroupId,$VinNumber,$finance_option)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'v.vin' => $VinNumber,'f.finance_option' => $finance_option,'d.rule_flag' => '1','fl.filtergroup_id' => $filterGroupId])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode','d.inventory_option as inventory')->groupBy('d.discount_name')->get();
    }

    public function sqlFilterRulediscount($dealer_code,$filterGroupId,$finance_option)
    {
       $now = \DB::raw('now()');
         //\App\Model\Dlrmgdiscount::join('')
        return \DB::table('vindiscounts as v')->join('discounts as d','v.discount_id','=','d.id')->join('filterdiscounts as fl','v.discount_id','=','fl.discount_id')->join('financediscounts as f','f.discount_id','=','d.id')->where(['v.dealer_code' => $dealer_code,'fl.filtergroup_id' => $filterGroupId,'d.rule_flag' => '1','f.finance_option' => $finance_option])->where('d.start_date', '<=', $now)->where('d.end_date', '>=', $now)->select('d.id as discount_id','d.uuid','d.discount_name as name_of_discount','d.flat_rate','d.percent_offer','d.start_date as discount_start_date','d.end_date as discount_end_date','d.discount_saved as saved_discount','f.finance_option as payment_mode','d.inventory_option as inventory')->groupBy('d.discount_name')->get();
    }

    public function addDiscountofnewlyfoundVin($DealerCode,$filterGroupId,$financeOption,$vin,$discounts)
    {
        $create_array = array();
        foreach ($discounts as $key => $items) {          
             $value = array();
            if(is_object($items)){
                $value = (array)$items;                
            }
            $data_array = array(
                'dealer_code' => $DealerCode,
                'discount_name' => $value['name_of_discount'],
                'flat_rate' => $value['flat_rate'],
                'percent_offer' => $value['percent_offer'],
                'start_date' => $value['discount_start_date'],
                'end_date' => $value['discount_end_date'],
                'discount_saved' => $value['saved_discount'],
                'inventory_option' => $value['inventory'],
                'rule_flag' => '1',
                'uuid' => \DB::raw('uuid()'),
                'vin' => $vin
            );
            array_push($create_array,$data_array);
        }
         $status_flag = false;
        if(!empty($create_array)){
            $finance_discount = array();
            $vin_discount = array();
            $filtergroup_discount = array();
            foreach ($create_array as $key => $value) {
                $discount_name = $value['discount_name'];
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
                $filter_discount_array = array(
                    'discount_id' => $last_discount_id,
                    'filtergroup_id' => $filterGroupId
                );
                array_push($filtergroup_discount, $filter_discount_array);
            }
            $status = $this->modelDiscountFinance->insert($finance_discount);
            $status = $this->modelVinDiscount->insert($vin_discount);
            $status = $this->modelFilterDiscount->insert($filtergroup_discount);
            $status_flag = ($status) ? true : false;
            if(!$status_flag){
                return false;
            }
        }
        return true;
    }

    public function checkValueExistsinArray($value,$array)
    {
        if(empty($array)){
            return false;
        }
        \Log::info('-----------------------------checkValueExistsinArray---------------------------------');
        \Log::info($array);
        \Log::info($value);
        $str_array = explode(',', $array);
        return in_array($value, $str_array);
    }
}
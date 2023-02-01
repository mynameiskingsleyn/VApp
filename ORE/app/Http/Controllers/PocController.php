<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Facades\OreDSClass;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use File;
use Excel;


class PocController extends Controller
{
    private $XRouteoneAPILeaseResource;
    private $XRouteoneAPIFinanceResource;
    private $XRouteoneAPIRebateResource;

    public function __construct()
    {
        $this->XRouteoneAPILeaseResource             = '/customer-quote/standardLease';
        $this->XRouteoneAPIFinanceResource         = '/customer-quote/finance';
        $this->XRouteoneAPIRebateResource             = '/customer-quote/rebates';
    }
    public function calc()
    {
        return view('poc.payment_calc');
    }
    public function paymentChecksForm(Request $request)
    {

        return view('poc.payment-checks');
    }
    public function paymentChecks(Request $request)
    {
        $request->validate([
            'import_file' => 'required'
        ]);
        $path = $request->file('import_file')->getRealPath();
        $data = Excel::load($path)->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $arr = array();
                $arr = [
                    'method' => $value->method,
                    'make' => $value->make,
                    'cashdown' => (int) $value->cashdown,
                    'vin' => $value->vin,
                    'terms' => $value->terms,
                    'year' => $value->year,
                    'milesPerYear' => (int) $value->milesperyear,
                    'model' => $value->model,
                    'financeSource' => $value->financesource,
                    'msrp' => (int) $value->msrp,
                    'tradeIn' => (int) $value->tradein,
                    'zip' => (int) $value->zip,
                    'dealercode' => (int) $value->dealercode,

                ];
                $result[$key]['input'] = $arr;
                $result[$key]['output'] = $this->swagger_bulk_request($arr);
            }
            //dd($result);
            /** download csv report */


            $consolidated = [];
            foreach ($result as $key => $value) {

                $vin = $value['input']['vin'];
                if (count($value['output'])) {
                    $status = 'TRUE';
                } else {
                    $status = 'FALSE';
                }

                $consolidated[] = array($vin, $status);
            }
            Excel::create('paymentcalculator', function ($excel) use ($consolidated, $result) {
                for ($i = 0; $i < count($result); $i++) {
                    if ($i == 0) {
                        $excel->sheet("consolidated", function ($sheet) use ($consolidated, $result) {
                            $sheet->fromArray($consolidated, null, 'A1', false, false);
                        });
                    } else {

                        foreach ($result as $key => $value) {
                            $vin = $value['input']['vin'];
                            $output = $value['output'];
                            $data = array(json_encode($output));
                            $excel->sheet("$vin", function ($sheet) use ($data, $result) {
                                $sheet->fromArray($data, null, 'A1', false, false);
                            });
                        }
                    }
                }
            })->export('xlsx');

            /** download csv report */
        }
    }

    public function swagger_bulk_request_sample($request)
    {
        $result2 = array( //This represents sheet itself.
            array('data1', 'data2'), //This represents a row of rows, in which the data is the cell of each row that is distributed horizontally.
            array('data3', 'data4') //Here's the second row
        );
        Excel::create('testexcel', function ($excel) use ($result2) {
            for ($i = 0; $i < 4; $i++) {
                $excel->sheet("Sheetname" . $i, function ($sheet) use ($result2) {

                    // Sheet manipulation
                    //Notes 1
                    $sheet->fromArray($result2, null, 'A1', false, false);
                    //Notes 2                 
                    foreach ($result2 as $index => $item) {
                        if ($index == 0) { //Exclude Title row
                            continue;
                        }
                    }
                });
            }
        })->export('xlsx');
    }



    public function swagger_bulk_request($request)
    {

        $transactionType         =  $request['method'] ?? 'lease';
        $make                  =  $request['make']  ?? "ALFA ROMEO";
        $vehicle_model         =  $request['model']  ?? 'STELVIO';
        $tradeIn               =  $request['tradeIn']  ?? 0;
        $vehicle_vin           =  $request['vin']  ?? 0;
        $vehicle_year          =  $request['year']  ?? '2019';
        $milesPerYear          =  $request['milesPerYear']  ?? '10000';

        $vehicle_sellingPrice  =  $request['msrp']  ?? 41480;
        $customer_zipcode      =  $request['zip']  ?? 41480;
        $dealercode            =  $request['dealercode']  ?? 68008;
        $financeSource         =  $request['financeSource']  ?? 'F000BA';

        $cashDown             =  $request['cashdown'] ?? 0;


        //Terms
        $terms_array =  $request['terms']  ?? '24,36,39,48,60,72';
        $terms = explode(',', $terms_array);
        //Finance Soruce
        $financeSource         = ($financeSource == 'ccap') ? 'F00CHC'  : 'F000BA';
        if ($financeSource == 'F000BA') $tier = 'S';
        else $tier = 1;
        //Endpoint
        $XRouteoneAPIResource = ($transactionType == 'lease') ? $this->XRouteoneAPILeaseResource : $this->XRouteoneAPIFinanceResource;

        $XRouteoneAPIResource_rebateids         = $this->XRouteoneAPIRebateResource;


        /****
         * RebateID API
         *****/
        $post_rebateids = array(
            "vehicle" =>
            array(
                'vin' => $vehicle_vin,
                'year' => $vehicle_year,
                'make' => $make,
                'model' => $vehicle_model,
                'salesClass' => 'NEW',
                'sellingPrice' => $vehicle_sellingPrice,
                'msrp' => $vehicle_sellingPrice
            ),
            'transactionType' => $transactionType
        );

        $response_rebateids = $this->payment_calc($post_rebateids, $XRouteoneAPIResource_rebateids, $dealercode);


        $pre_owner = 'No Previous Ownership Requirement';
        $grp_aff = 'No Specific Group Affiliation';
        $today =  date("Y-m-d");

        $man_incentives_id = $return_lesse_id = $military_id = $automobility_id = array();
        $incentive_amount = $incentive_returning_essee =  $incentive_military = $incentive_automobility = 0;
        foreach ($response_rebateids as $key => $val) {
            if ($val['expirationDate'] >= $today && $val['groupAffiliation'] == $grp_aff && $val['previousOwnership'] == $pre_owner) {
                array_push($man_incentives_id, $val['incentiveId']);
                $incentive_amount += $val['amount'];
            }
        }
        if ($transactionType == 'finance') {
            $post = array(
                'tradeInValue' => $tradeIn,
                'vehicle' =>
                array(
                    'vin' => $vehicle_vin,
                    'year' => $vehicle_year,
                    'make' => $make,
                    'model' => $vehicle_model,
                    'salesClass' => 'NEW',
                    'sellingPrice' => $vehicle_sellingPrice,
                    'msrp' => $vehicle_sellingPrice,
                ),
                'customer' =>
                array(
                    'address' =>
                    array(
                        'zipCode' => $customer_zipcode,
                    ),
                ),
                'markupIndicator' => false,
                'cashDown' => $cashDown,
                'financeSource' => $financeSource, //'F00CHC',
                'tier' => $tier, //'S',//'1',
                'rebateIds' => $man_incentives_id,
                'terms' =>  $terms,

            );
        } else {
            $post = array(
                'tradeInValue' => $tradeIn,
                'vehicle' =>
                array(
                    'vin' => $vehicle_vin,
                    'year' => $vehicle_year,
                    'make' => $make,
                    'model' => $vehicle_model,
                    'salesClass' => 'NEW',
                    'sellingPrice' => $vehicle_sellingPrice,
                    'msrp' => $vehicle_sellingPrice,
                ),
                'customer' =>
                array(
                    'address' =>
                    array(
                        'zipCode' => $customer_zipcode,
                    ),
                ),
                'markupIndicator' => false,
                'cashDown' => $cashDown,
                'financeSource' => $financeSource, //'F00CHC',
                'tier' => $tier,
                'rebateIds' => $man_incentives_id,
                'terms' =>   $terms,
                'milesPerYear' => $milesPerYear,
                'cashDownAppliedToInceptionFees' => false,
            );
        }

        $payment_return['rebate_details'] = $response_rebateids;
        $payment_return['payments'] = $this->payment_calc($post, $XRouteoneAPIResource, $dealercode);


        return     $payment_return;
    }


    function payment_calc($post, $XRouteoneAPIResource, $dealercode)
    {
        date_default_timezone_set('Etc/GMT');
        $fullURLString                 = 'https://www.routeone.net' . $XRouteoneAPIResource;
        $XRouteOneActAsDealership     = $dealercode; //'UQ4RH';
        $CanonicalizedHeaders_Name  = 'x-routeone-act-as-dealership-partner-id';
        $accessKeyId                 = 'F0AFCA';

        $routeoneSecret             = 'R7UXkrghWvPvjDjtRh7IHKkp92gH4IXbd2tY2rA11';
        $XRouteDate                 = date('D, d M Y H:i:s \g\m\t');
        $XRouteContentType             = 'application/json';

        $json = json_encode($post);

        $ContentMD5_body = base64_encode(md5($json, true));

        #StringToSign Variable assignment 
        $HTTP_VERB                   = "POST" . "\n";
        $ContentMD5                  = strtolower($ContentMD5_body) . "\n";
        $ContentType                 = $XRouteContentType . "\n";
        $Date                        = strtolower($XRouteDate) . "\n";
        $CanonicalizedResource      = $XRouteoneAPIResource . "\n";
        $CanonicalizedHeaders         = $CanonicalizedHeaders_Name . ':' . $XRouteOneActAsDealership . "\n";

        # signature=>base64(hmac-sha256({accessKeySecret}, UTF8({stringToSign}))) 				
        #StringToSign Created
        $stringToSign = $HTTP_VERB . $ContentMD5 . $ContentType . $Date . $CanonicalizedHeaders . $CanonicalizedResource;
        $byteArrayStringToSign = utf8_encode($stringToSign);

        #Signature Created	
        $signature_hash             = hash_hmac('sha256', $byteArrayStringToSign, $routeoneSecret, true);
        $signature                     = base64_encode($signature_hash);
        # Authorization Prepared  


        # Authorization: RouteOne {accessKeyId}:{signature}

        $Authorization = "RouteOne $accessKeyId:$signature";
        # cURL 
        $ch = curl_init($fullURLString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'accept: ' . $XRouteContentType,
            'content-type: ' . $XRouteContentType,
            "date: " . $XRouteDate,
            $CanonicalizedHeaders_Name . ": " . $XRouteOneActAsDealership,
            "content-md5: " . $ContentMD5_body,
            "authorization: " . $Authorization,
        ));


        #	debugging	
        try {
            $response = curl_exec($ch);
            $response_arr = json_decode($response, true);
            curl_close($ch);
            return $response_arr;
        } catch (\Exception $e) {
            curl_close($ch);
            return 'Exception Message: ' . $e->getMessage();
        }
    }
}

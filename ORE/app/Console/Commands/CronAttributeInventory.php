<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\DealerInventoryMail;
use App\Mail\AttributeInventoryMail;

use Fcaore\Databucket\Facade\Databucket;
use DB;

class CronAttributeInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:AttributeInventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitoring Attribute Inventory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('----------------------------------------------------------');
        \Log::info('Attribute Inventory Starts');
        try {
            $AuditDetails = \Databucket::IBFileAuditMonitoring();
            $attribute = $this->AttributeInventory();
            Mail::to('thangavel@v2soft.com')
                ->cc(['thangavel@v2soft.com'])
                ->queue(new AttributeInventoryMail($attribute,$AuditDetails));
            \Log::info('Attribute Inventory Monitoring E-Mail Send Successfully.');
            return ['status' => "true", 'message' => 'Attribute Inventory Monitoring E-Mail Send Successfully.'];
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            \Log::info($e->getLine());
            return ['status' => "failure", 'message' => $e->getMessage()];
            exit;
        }
        \Log::info('Attribute Inventory Ends');
        \Log::info('----------------------------------------------------------');
    }


    public function AttributeInventory()
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', -1);
        $model_year = 2017;
        // drive_type
        // eng_desc,
        // transmission_desc
        /// drive type
        $drive_type = \DB::table('fca_ore_filters')->select('tier1_actuals')->distinct('tier1_actuals')->where('filter_types', 'drive_type')->get();

        $drive_type_attribute = \DB::table('fca_ore_filter_lookup')->select('attribute')->distinct('attribute')->where('attribute_type', 'drive_type')->get();

        $drive_type_attribute_array=[];
        
        foreach ($drive_type_attribute as $key => $value) {
            $drive_type_attribute_array[] = $value->attribute;
        }
        $drive_type_array = [];
        foreach ($drive_type as $key => $value) {
            $drive_type_array[] = $value->tier1_actuals;
        }
        $drive_type_array=array_merge($drive_type_array,$drive_type_attribute_array);
        //// eng desc
        $eng_desc = \DB::table('fca_ore_filters')->select('tier1_actuals')->distinct('tier1_actuals')->where('filter_types', 'eng_desc')->get();

        $eng_type_attribute = \DB::table('fca_ore_filter_lookup')->select('attribute')->distinct('attribute')->where('attribute_type', 'eng_desc')->get();

        $eng_attribute_array=[];
        
        foreach ($eng_type_attribute as $key => $value) {
            $eng_attribute_array[] = $value->attribute;
        }

        $eng_desc_array = [];
        foreach ($eng_desc as $key => $value) {
            $eng_desc_array[] = $value->tier1_actuals;
        }
        $eng_desc_array=array_merge($eng_desc_array,$eng_attribute_array);
        // transmission desc
        $transmission_desc = \DB::table('fca_ore_filters')->select('tier1_actuals')->distinct('tier1_actuals')->where('filter_types', 'transmission_desc')->get();
        
        $transmission_type_attribute = \DB::table('fca_ore_filter_lookup')->select('attribute')->distinct('attribute')->where('attribute_type', 'transmission_desc')->get();

        $transmision_attribute_array=[];
        
        foreach ($transmission_type_attribute as $key => $value) {
            $transmision_attribute_array[] = $value->attribute;
        }
        
        $transmission_desc_array = [];
        foreach ($transmission_desc as $key => $value) {
            $transmission_desc_array[] = $value->tier1_actuals;
        }

        $transmission_desc_array=array_merge($transmission_desc_array,$transmision_attribute_array);

        //  dd($drive_type_array, $eng_desc_array, $transmission_desc_array);
        $inputs = \DB::table('fca_ore_input')->select('drive_type', 'eng_desc', 'transmission_desc')->distinct('drive_type', 'eng_desc', 'transmission_desc')->where('vehicle_type', 'New')->where('year','>',$model_year)->get();
        $inputs_drive_type = [];
        $inputs_eng_desc = [];
        $inputs_transmission_desc = [];
        foreach ($inputs as $key => $value) {
            if ($value->drive_type != '') {
                $inputs_drive_type[] = $value->drive_type;
            }
            if ($value->eng_desc != '') {
                $inputs_eng_desc[] = $value->eng_desc;
            }
            if ($value->transmission_desc != '') {
                $inputs_transmission_desc[] = $value->transmission_desc;
            }
        }
        $inputs_drive_type = array_unique($inputs_drive_type);
        $inputs_eng_desc = array_unique($inputs_eng_desc);
        $inputs_transmission_desc = array_unique($inputs_transmission_desc);
        //dd($inputs_drive_type, $inputs_eng_desc, $inputs_transmission_desc);
        //// drive type
        $drive_type_data = array_diff($inputs_drive_type, $drive_type_array);
        $eng_desc_data = array_diff($inputs_eng_desc, $eng_desc_array);
        $eng_transmission_data = array_diff($inputs_transmission_desc, $transmission_desc_array);

        $output = array('drive_type' => $drive_type_data, 'eng_desc' => $eng_desc_data, 'transmission_desc' => $eng_transmission_data);
        ///for audit log
        $drivestr = '';
        if (!empty($drive_type_data)) {
            foreach ($drive_type_data as $key => $value) {
                $drivestr .= $value . '|';
            }
            $drivestr = rtrim($drivestr, '|');
            \DB::table('fca_ore_filters_audit')->insert(
                ['filter_types' => 'drive_type', 'Attribute' =>  $drivestr]
            );
        }
        $engstr = '';
        if (!empty($eng_desc_data)) {
            foreach ($eng_desc_data as $key => $value) {
                $engstr .= $value . '|';
            }
            $engstr = rtrim($engstr, '|');
            \DB::table('fca_ore_filters_audit')->insert(
                ['filter_types' => 'eng_desc', 'Attribute' => $engstr]
            );
        }
        $transmissionstr = '';
        if (!empty($eng_transmission_data)) {
            foreach ($eng_transmission_data as $key => $value) {
                $transmissionstr .= $value;
            }
            $transmissionstr = rtrim($transmissionstr, '|');
            \DB::table('fca_ore_filters_audit')->insert(
                ['filter_types' => 'transmission_desc', 'Attribute' => $transmissionstr]
            );
        }        
        return $output;
    }
}

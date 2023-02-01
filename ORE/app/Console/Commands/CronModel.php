<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Fcaore\Databucket\Facade\Databucket;
use App\Dealer;
use App\Vehicle;
use App\Catvehicle; 
use Carbon\Carbon; 
use DB;
use Cache;
use App\Zipcode;   
use App\Vmodel;   
use Illuminate\Support\Arr;
use Illuminate\Support\Str; 

class CronModel extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:Model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Model Table Loading';

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
		if(env('APP_ENV') != 'local' ){	
			// Model Table Loading
			$this->vehicle(); 
		 } 
	}
	
	public function vehicle(){
		$divisionCode = ['C','D','J','T','X']; 
		$this->logAppend('Start',' Start Model table'); 
		$vehicle = [];	
		try{
				 
			foreach($divisionCode as $key_divisionCode => $value_divisionCode){
				$GetVehicleDetails = 'https://www.chrysler.com/hostd/getvehicles.xml?divisionCode='.$value_divisionCode;
			
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $GetVehicleDetails); 
				$xml_string = $res->getBody();
				$xml = simplexml_load_string($xml_string);
				$json = json_encode($xml);
				$array = json_decode($json, true); 
				
					if(Arr::has($array['data'],'vehicles')) { 
						$bulk = $array['data']['vehicles']['vehicle'];
						$special_character_array = ['Â','Ã',"Â","Ã"];
								foreach($bulk as $bulk_key=>$bulk_value){ 
									 
									//  $new_trim = $bulk_value['@attributes']['trimDesc'];
									 // $new_desc = $bulk_value['@attributes']['description'];
									
									$new_trim = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['trimDesc']));
									//$modelDesc = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['modelDesc']));		
									$new_desc = trim(Str::replaceLast('®', ' ', $bulk_value['@attributes']['description'])); 
									
									$new_trim = trim(str_replace('®', ' ', $new_trim));
									//$modelDesc = trim(str_replace('®', ' ', $modelDesc)); $new_desc = trim(str_replace('®', ' ', $new_desc)); 
									
									 
									$modelDesc = trim($bulk_value['@attributes']['modelDesc']);
									
									$new_year =  $bulk_value['@attributes']['modelYearDescription'];
									
									if($new_year <= date('Y') && $new_year > date('Y')-3){
									
											$vehicle[] = [	'description' => str_replace($special_character_array , '', $new_desc),
												'franchiseDescription' => str_replace($special_character_array , '', $bulk_value['@attributes']['franchiseDescription']),
												'modelDesc' => str_replace($special_character_array , '', $modelDesc) ,
												'trimDesc' => str_replace($special_character_array , '', $new_trim),
												'modelYearDescription' =>$new_year,	
												'franchiseCode' => $bulk_value['@attributes']['franchiseCode'],
												'baseVehicleMsrp' => $bulk_value['@attributes']['baseVehicleMsrp']]; 
									}
								}
					}
				} 
				//	\Log::info($vehicle);
				   Vmodel::truncate(); 
				   Vmodel::insert($vehicle);
				$this->logAppend('End',' End Model table'); 
				
		}catch (\Exception $e) {
			\Log::info( $e->getMessage());
			return $e->getMessage();
		}  
	}

	function logAppend($time, $cronName){
		$dots = '-----------------------------------------------------------------------------';
		$timings = Carbon::now('America/New_York').': *** '.$time.':  '.$cronName.' ****';
		
		if($time == 'Start'){
			$this->info($dots);
			Log::info($dots);
		}
		
		$this->info($timings);
		Log::info($timings); 
		
		if($time == 'End'){
			$this->info($dots);
			Log::info($dots); 
		}
	} 
}
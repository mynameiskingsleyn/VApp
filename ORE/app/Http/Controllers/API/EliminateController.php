<?php

namespace App\Http\Controllers\API;

use App\cr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Dealereliminate;
use App\Api;
use Artisan;
class EliminateController extends Controller
{
	
	private $Api;
	
	private $Dealereliminate;
    public function __construct(Api $Api, Dealereliminate $Dealereliminate)
    {
         $this->api = $Api;
		 $this->Dealereliminate = $Dealereliminate;
    }
     

	 public function add(Request $request)
    {
		
	//	Artisan::call('Cron:CronMasterSummary');
	//	exit;
		$bodyContent = "";
		$m_Result = "Failed :(";
		try{
        $bodyContent = $request->getContent();
		$my_array_data = json_decode($bodyContent, TRUE);
		
		foreach($my_array_data as $key=>$value)
		{
		
		
		$username =	$value['username'];
		$password =	$value['password']; 
		$dealer_code = $value['dealer_code'] ;
		
		
			if(($username == config('ore.dealereliminate.username'))&& ($password == config('ore.dealereliminate.password')) && $dealer_code!='')
					{ 				
							if($value['status'] == -1){
								$this->Dealereliminate->where(['dlr_code' => $dealer_code])->delete();
							}else{
								 
								$this->Dealereliminate->updateOrCreate(
								['dlr_code' => $dealer_code],
								['status' => $value['status'], 'dlr_dba_name' => $value['dealer_name'] ]);
							}
				 
					}
					else{
					return false;
					}
			}
			
			 //Artisan::call('Cron:CronMasterSummary');
			 //Artisan::call('Cron:CronVin');
			 return response("Successfully Dealer Delete from Database.", 200)->header('Content-Type', 'application/json'); 
			 
			/* $pre_dir = '/var/www/html/releases/';
			$post_dir = '/artisan';
			$LOGFILE="/var/www/html/OreShellCron.logs";

			$current = scandir($pre_dir);
			$cpath = end($current);
			$cron_path = $pre_dir.$cpath.$post_dir;

			\Log::info('/usr/bin/php  '.$cron_path.'  schedule:run >> /var/www/html/OreShellCron.logs 2>&1');
			exec('/usr/bin/php  '.$cron_path.'  php artisan Cron:CronMasterSummary >> /var/www/html/OreShellCron.logs 2>&1');
			 */
			
		}
		catch(\Exception $e){
			\Log::info("Error in JSON EliminateController Request: ".$e->getMessage()); 
		} 
		return response("Received", 200)->header('Content-Type', 'application/json'); 
    }
}

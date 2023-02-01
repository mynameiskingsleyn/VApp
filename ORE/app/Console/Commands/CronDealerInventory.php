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
use Fcaore\Databucket\Facade\Databucket;
use DB;

class CronDealerInventory extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:dealerInvMonitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitoring Dealer Inventory';
	
	 


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
		$DealerInventory = \Databucket::DealerInventoryMonitoring(); 
		  
		try{
			if(env('APP_ENV') == 'staging'){
				Mail::to(config('ore.uptime.to_email')) 
							->cc(config('ore.uptime.cc_email'))
							->queue(new DealerInventoryMail($DealerInventory)); 
					return ['status' =>"true", 'message' => 'Dealer Inventory Monitoring E-Mail Send Successfully.'];
					
			}else{
					return ['status' =>"true", 'message' => 'Dealer Inventory Monitoring E-Mail Send Successfully.']; 
			}
			
		}catch(\Exception $e){
			\Log::info($e->getMessage());
			return ['status' =>"failure", 'message' => $e->getMessage()];
			exit;
		} 
    }
	
	
	 
	
}
<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Fcaore\Databucket\Facade\Databucket;
use Carbon\Carbon; 
use App\Vmodel; 
use DB; 
use Cache;

 

class CronTesting extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:Testing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Developer Testing Purpose';
	
	 


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
			$this->developerTest();
	}
	
		public function developerTest(){
				 
				try{
					$this->logAppend('Start','DEVELOPER PING'); 
					echo "DEVELOPER TEST: ".Carbon::now('America/New_York')."\n";
					Log::info("DEVELOPER TEST: ".Carbon::now('America/New_York'));
					$this->logAppend('End','DEVELOPER PING'); 
						
				}catch (\Exception $e) {
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
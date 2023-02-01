<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [ 
			//'\App\Console\Commands\CronTesting',
			'\App\Console\Commands\CronUptime',
			'\App\Console\Commands\CronModel',
			'\App\Console\Commands\CronMasterSummary',
			'\App\Console\Commands\CronDealerInventory',
			'\App\Console\Commands\CronLeadAuto',
			'\App\Console\Commands\CronAttributeInventory',
			//'\App\Console\Commands\CronVin', 
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    { 

			$types = 'runInBackground';
        
			if($types == 'times'){  
			}else if($types == 'runInBackground'){		 
				
				
				if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training'){
					$schedule->command('Cron:CronMasterSummary')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('09:00');
					$schedule->command('Cron:AttributeInventory')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('09:05');
				}else{
					$schedule->command('Cron:CronMasterSummary')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('07:50');
					$schedule->command('Cron:AttributeInventory')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('07:55');
				 
					//Every week wednesday run at 07:45 EST 
					$schedule->command('Cron:Model')->runInBackground()->timezone('America/New_York')->weeklyOn(3, '07:45');
				 
				
					//$schedule->command('Cron:Uptime')->runInBackground()->timezone('America/New_York')->everyThirtyMinutes(); 
					//$schedule->command('Cron:dealerInvMonitor')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('08:15');
					
				}
				
				$schedule->command('Cron:LeadAuto')->runInBackground()->timezone('America/New_York')->everyFiveMinutes();
				
				 /* 	$schedule->command('Cron:Uptime')->runInBackground()->timezone('America/New_York')->everyFifteenMinutes();
				$schedule->command('Cron:CronVin')->daily()->runInBackground()->timezone('America/New_York')->dailyAt('04:00');*/ 
				 
			}
	}

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

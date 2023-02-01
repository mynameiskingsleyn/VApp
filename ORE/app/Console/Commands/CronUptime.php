<?php
namespace App\Console\Commands;

use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Mail;
use App\Mail\uptimeMailable; 
use Fcaore\Databucket\Facade\Databucket;
use DB;

class CronUptime extends Command
{
     
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cron:Uptime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vendor Services Up or Down';
	
	/*
	*	List of API Vendor Serivces
	*/
    protected $api_lists;
	
	/*
	*	List of Failed Serivces
	*   @request array
	*/
	protected $api_lists_failure;
	
	/*
	*	List of Failed Serivces
	*   @request array
	*/
	protected $api_lists_success;
	
	
	/*
	*  Count of failure Serivces
	*  @request $int
	*/
	protected $api_failure_count;
	
	/*
	* success and Failure
	*  @request $array
	*/ 
	protected $api_upstatus;



    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
		 $this->api_lists_failure 	= [];
		 $this->api_lists_success 	= [];
		 $this->api_failure_count 	= 0;
		 $this->api_upstatus 		= [];		
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
	
			$client = new \GuzzleHttp\Client();
		
		$singleVehicle = \Databucket::singleVehicle();
		 
		
		$this->api_lists['routeone_credit_apply'] = config('ore.routeone.endpoint');		
		$this->api_lists['routeone_payment_calcualtor'] = config('ore.calc.endpoint');
		//$this->api_lists['ishowroom'] = config('ore.ishowroom.endpoint');
		
		//$this->api_lists['carnow'] = config('ore.carnow.plugin');
		$this->api_lists['blackbook'] = config('ore.blackbook.endpoint');
		$this->api_lists['crazy_egg'] = config('ore.crazyegg.endpoint');
		
		$this->api_lists['dealer_api'] = config('ore.dealerapi.endpoint').'='.$singleVehicle[0]['dealer_code'];
		
		$this->api_lists['vinnumber_api'] = str_replace('VINNUMER',$singleVehicle[0]['vin'], config('ore.vindecoder.endpoint'));		
		$this->api_lists['merkle_api'] = config('ore.merkle.endpoint');	
		
		
		
		foreach($this->api_lists as $key => $val){
			try { 
				$isvalid = $this->is_valid_url($val); 
			if ($isvalid || $isvalid==''){	
				$request  = $client->request('GET', $val);
				$response = $request->getBody();
				$stausCode = $request->getStatusCode();	
				 if($stausCode != 200){ 
					 $this->api_failure_count++;
					 $this->api_lists_failure[$key] = $request->getReasonPhrase();
				 }else{
					$this->api_lists_success[$key] = $request->getReasonPhrase();
				 }
			}else{
				$this->api_failure_count++;
					 $this->api_lists_failure[$key] = 'File Not Found';
			}
				 
			} catch (RequestException $e) {   
				if ($e->hasResponse()) {  
					 $this->api_failure_count++;
					 $this->api_lists_failure[$key] = 'Server Down / File Not Found';
				}
			} 		
		}
		
		if($this->api_failure_count > 0){
			$this->api_upstatus['success'] = $this->api_lists_success;
			$this->api_upstatus['failure'] = $this->api_lists_failure;
			 
			if(env('APP_ENV') == 'staging'){
				Mail::to(config('ore.uptime.to_email')) 
					->cc(config('ore.uptime.cc_email'))
					->queue(new uptimeMailable($this->api_upstatus));
			}else{
				\Log::info('Mail serivces has been restriced in test environment');
			}
		}
		
    	 
    }
	
	
	public function is_valid_url($url)
{
    $url = @parse_url($url);
    if (!$url)
    {
        return false;
    }
    $url = array_map('trim', $url);
    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';
    if ($path == '')
    {
        $path = '/';
    }
    $path .= (isset($url['query'])) ?  "?$url[query] " : '';
    if (isset($url['host']) AND $url['host'] != gethostbyname($url['host']))
    {
        if (PHP_VERSION  >= 5)
        {
            $headers = @get_headers( "$url[scheme]://$url[host]:$url[port]$path ");
        }
        else
        {
            $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
            if (!$fp)
            {
                return false;
            }
            fputs($fp,  "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n ");
            $headers = fread($fp, 4096);
            fclose($fp);
        }
        $headers = (is_array($headers)) ? implode( "\n ", $headers) : $headers;
        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }
    return false;
}

	
}
<?php

namespace App\Http\Middleware;

use Closure;

class FrameHeadersMiddleware
{
    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server'  
    ];
	
	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		 $this->removeUnwantedHeaders($this->unwantedHeaderList);
		
		$response = $next($request);
	 	$response->header('Referrer-Policy', 'no-referrer-when-downgrade');
	 	$response->header('X-Content-Type-Options' , 'nosniff');
	 	$response->header('X-XSS-Protection' , '1; mode=block');
	 	$response->header('X-Frame-Options', 'sameorigin');
		$response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains'); 
	 
		return $response;
    }
	
	private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }
}

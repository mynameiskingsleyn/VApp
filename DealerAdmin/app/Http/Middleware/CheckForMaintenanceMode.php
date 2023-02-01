<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Closure;
class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ]; 

    public function handle($request, Closure $next) {
	    if ($this->app->isDownForMaintenance() && ! $this->shouldPassThrough($request)) {
	        //throw new HttpException(503);
	        $response = array();
	        $response['Message'] = 'Service Unavailable';
	        $response['StatusCode'] = 503;

	        return response()->json($response,200);
	    }
	    return $next($request);
	}
}
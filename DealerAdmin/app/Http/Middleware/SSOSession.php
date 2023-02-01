<?php

namespace App\Http\Middleware;

use Closure;

use Log;

class SSOSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //$dbstatus = config('dealeradmin.dc.login');
        //if ($dbstatus) {
            \Log::info('Session All Value::');
            \Log::info(\Session::all());   
            \Log::info('Middleware Session checking');
            if (!\Session::exists('DealerAdmin')) {
                // user value cannot be found in session
                \Log::info('Session:: DealerAdmin Key is not exists.Response unauthorized');
                return response()->view('error.unauthorized');
            }
            \Log::info('Session:: DealerAdmin Key is exists.Checking DealerCode key..');
            $DealerAdmin = \Session::get('DealerAdmin');
            if (!array_key_exists('DealerCode', $DealerAdmin)){
                // user value cannot be found in session
                $this->ClearSessionKeys();
                 \Log::info('Session:: DealerAdmin-DealerCode Key is not exists. Response unauthorized');
                return response()->view('error.unauthorized');
            }
            \Log::info('Session:: DealerAdmin-DealerCode Key is exists.Checking dealer exists in tables');
            $DealerCode = $DealerAdmin['DealerCode'];
            $dealer_exists = \DB::table('fca_ore_dealer_info')->where('dlr_code', $DealerCode)->get();
            if ($dealer_exists->isEmpty()) {
                \Log::info('Session:: Invalid DealerCode in fca_ore_dealer_info.Response unauthorized');
                $this->ClearSessionKeys();
                return response()->view('error.unauthorized');
            }
            \Log::info('Session:: DealerCode exists in fca_ore_dealer_info');
            if ($DealerCode != '' && is_numeric($DealerCode)) {
                //$mdoca_exists = \DB::table('fca_ore_mdoca')->where('dlr_code', $DealerCode)->first();
                /*$mdoca_exists = \DB::table('dealer_access')->where('dealer_code', $DealerCode)->first();
                if (is_null($mdoca_exists)) {
                    \Log::info('Session:: Invalid DealerCode in dealer_access.Response unauthorized');
                    $this->ClearSessionKeys();
                    return response()->view('error.unauthorized');
                }
                \Log::info('Session:: DealerCode exists in dealer_access');*/
                $eliminate_exists = \DB::table('fca_ore_dealer_eliminate')->where('dlr_code', $DealerCode)->first();
                if (!is_null($eliminate_exists)) {
                    \Log::info('Session:: DealerCode exists in fca_ore_dealer_eliminate.Response unauthorized');
                    $this->ClearSessionKeys();
                    return response()->view('error.unauthorized');
                }
                \Log::info('Session:: DealerCode not exists in fca_ore_dealer_eliminate');
            } else {
                \Log::info('Session:: Invalid DealerCode.Response unauthorized');
                return response()->view('error.unauthorized');
            }
            \Log::info('Middleware Session checking successfully');
        //}
        return $next($request);
    }

    public function ClearSessionKeys()
    {
        \Session::forget('DealerCode');
        \Session::forget('DealerAdmin');
        \Session::forget('DealerName');
        \Session::forget('ZipCode');
    }
}

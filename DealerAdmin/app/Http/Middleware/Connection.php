<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Auth;
use Log;

class Connection
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
        $DealerAdmin = \Session::get('DealerAdmin');
        $DealerCode = $DealerAdmin['DealerCode'];
        \Log::info('Connection Middleware:: DealerCode:: '.$DealerCode.' : Starts');
        if (!\Session::has('dealertype')) {
            $db = \Session::get('dealertype');
            $input = \DB::table('fca_ore_input')->select('make')->where('dealer_code', $DealerCode)->where('make','alfa romeo')->get();
            \Log::info('ORE-Alfa DB dealer vehicle count:'.count($input));
            if (count($input) > 0) {
                \config()->set('database.default', 'mysql');
                \Log::info('ORE-Alfa DB Session Set: dealertype:'.$db);
                \Session::put('dealertype', 'mysql');
            } else {
                \DB::disconnect('mysql');
                \DB::reconnect('mysql2');
                \config()->set('database.default', 'mysql2');
                $input = \DB::table('fca_ore_input')->select('make')->where('dealer_code', $DealerCode)->get();
                \Log::info('ORE-CDFJR DB dealer vehicle count:'.count($input));
                if (count($input) > 0) {
                    \DB::disconnect('mysql');
                    \DB::reconnect('mysql2');
                    \config()->set('database.default', 'mysql2');
                    \Session::put('dealertype', 'mysql2');
                    \Log::info('ORE-CDFJR DB Session Set: dealertype:'.$db);
                } else {
                    \DB::disconnect('mysql2');
                    \DB::reconnect('mysql');
                    return redirect('/');
                }
            }
        } else {
            $db = \Session::get('dealertype');
            \Log::info('SessionExist : dealertype:'.$db);
            config()->set('database.default', $db);
        }
        \Log::info('Connection Middleware:: DealerCode:: '.$DealerCode.' : Ends');
        return $next($request);
    }
}

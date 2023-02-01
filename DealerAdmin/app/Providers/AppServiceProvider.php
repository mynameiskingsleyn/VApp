<?php namespace App\Providers;

use DB;
use URL;
use Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
                DB::listen(function($query) {
                    Log::info(
                        $query->sql,
                        $query->bindings,
                        $query->time
                    );
                });
				//URL::forceSchema('https');
 				
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}

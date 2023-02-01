<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DatacompServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        \App::bind('oredsclass', function() {
            return new \App\Helpers\OreDSClass;
        });
    }
}

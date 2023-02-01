<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/  
/**
*Inventory Controller
*/ 

Route::post('/getLocator', 'InventoryController@getLocator')->name('getLocator');
Route::post('/filterByZipcode', 'InventoryController@filterByZipcode')->name('filterByZipcode');
Route::get('/filterByDealer', 'InventoryController@filterByDealer')->name('filterByDealer'); 
Route::get('/inventory_vehicle', 'InventoryController@vehicle')->name('vehicle');

if(env('APP_ENV') == 'test'){
	Route::get('/alfa-video-download', function () {
		$main_url = "https://d1jougtdqdwy1v.cloudfront.net/images/alfa.mp4";
		$file = basename($main_url);
		header("Content-disposition:attachment; filename=$file");
		readfile($main_url);
	 
	});
}

/**
Carnow
**/
Route::post('/baseCarNow', 'API\CarnowController@post_api')->name('post_carnow_base');
/**
Maintenance
**/
Route::get('/maintenance', 'HomeController@maintenance')->name('maintenance');
/**
Payment Calcualtor
***/
Route::post('/payment-calcultor', 'CalculatorController@PaymentCalculator')->name('calc');

/**
*VehicleController
*/

### USER EXPERIENCES
Route::get('/vehicle', 'UserexperienceController@index')->name('vehicle_index'); 
Route::post('/userinfo_vehicle', 'UserexperienceController@vehicle')->name('vehicle'); 
Route::post('/userinfo_tradein', 'UserexperienceController@tradein')->name('tradein');
Route::post('/userinfo_service_protection', 'UserexperienceController@service_protection')->name('service_protection');
Route::get('/update_pageinfo/{curPage}', 'UserexperienceController@update_pageinfo')->name('update_pageinfo');
Route::post('/loadPrevSession','UserexperienceController@load_prev_session')->name('load_prev_session');


##Tiers  
Route::get('/{brand}/t1/', 'VehicleController@vehicle_params')->name('vehicle_params_t1');
Route::get('/{brand}/t3/', 'VehicleController@vehicle_params')->name('vehicle_params_t3');
Route::get('/{brand}/ore/', 'VehicleController@vehicle_params')->name('vehicle_params_ore'); 


Route::get('/review', 'VehicleController@review')->name('review');
Route::post('/mylead', 'VehicleController@mylead')->name('mylead'); 
Route::post('/initial_lead', 'VehicleController@initial_lead')->name('initial_lead');
Route::post('/autolead', 'VehicleController@autolead')->name('autolead');

/**
*HomeController
*/
Route::post('/leadinfo', 'HomeController@leadInfo')->name('lead_info');
Route::get('/testlead','HomeController@testlead')->name('testlead'); 

Route::get('/routeone', 'VehicleController@routeone')->name('routeone');
Route::get('/routeone_iframe', 'VehicleController@routeone_iframe')->name('routeone_iframe');  

Route::get('/saveOreSession', 'VehicleController@saveOreSession')->name('saveOreSession');

Route::get('/image_color/{type}', 'VehicleController@image_color')->name('image_color');
 

/**
*Session Timeout Lead
*/
Route::get('/sessionout/mylead', 'VehicleController@sesionout')->name('sesionout');

/**
* Uptime
*/
//Route::get('/getUptimeRequest', 'UptimeController@getUptimeRequest')->name('getUptimeRequest');


/**
* POC
*/
//if(env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'local'){ 
		Route::get('/poc/payment-calc', 'PocController@calc')->name('poc_calc');
//}


 
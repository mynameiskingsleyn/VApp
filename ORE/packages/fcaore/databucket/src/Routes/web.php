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
Route::group(['middleware' => 'web'], function(){
	//Param make will accept three case sensitive like fiat,Fiat,FIAT.
	$params_make_expression = '^(jeep|chrysler|dodge|ram|fiat|Jeep|Chrysler|Dodge|Ram|Fiat|JEEP|CHRYSLER|DODGE|RAM|FIAT)$';


// Landing Page
Route::get('/', 'Fcaore\Databucket\Http\LandingController@on_load')->name('landing.on_load');
// BrandLanding Page
Route::get('/{params_make}/{tiers?}', 'Fcaore\Databucket\Http\LandingController@on_brand_load')
							->name('landing.on_brand_load')
							->where('params_make', $params_make_expression);

/* Route::get('/landing_new_vehicle/{params_make}/{params_vechType}/{zipcode}', 'Fcaore\Databucket\Http\LandingController@on_new_vehicle')
										->name('landing.new_vehicle')  ; */	
										
Route::post('/landing_cpo_vehicle', 'Fcaore\Databucket\Http\LandingController@on_cpo_vehicle')->name('landing.cpo_vehicle');	
Route::post('/find_cpo_dealers', 'Fcaore\Databucket\Http\LandingController@on_cpo_dealers')->name('find.cpo_dealers');
Route::post('/find_zip_by_cord', 'Fcaore\Databucket\Http\LandingController@on_zip_by_cord')->name('find.find_zip_by_cord');
Route::post('/find_cord_by_zip', 'Fcaore\Databucket\Http\LandingController@on_cord_by_zip')->name('find.find_cord_by_zip');


Route::get('/ore/{params_make}/{zipcode?}', 'Fcaore\Databucket\Http\LandingController@on_new_vehicle')->name('landing.new_vehicle')->where('params_make', $params_make_expression);

// TIER - SNI
Route::get('/{make}/{tier}/{year}/{model}', 'Fcaore\Databucket\Http\SNIController@sni_list')
										->name('sni_list')
										->where('tier', '^(ore|t1|t3)$')
										->where('make', $params_make_expression);	


										// CPO / USED SNIPPET
/* Route::get('/c/{make}/{year}/{model}/{vehicle_type}', 'Fcaore\Databucket\Http\SNIController@sni_list_cpo')
										->name('sni_list_cpo')
										->where('year', '^(20)(18|19|20|21)$')
										->where('tier', '^(ore|t1|t3)$')
										->where('make', '^(alfaromeo)$');
										
Route::get('/u/{make}/{year}/{model}/{vehicle_type}', 'Fcaore\Databucket\Http\SNIController@sni_list_cpo')
										->name('sni_list_used')
										->where('year', '^(20)(18|19|20|21)$')
										->where('tier', '^(ore|t1|t3)$')
										->where('make', '^(alfaromeo)$');
  */

// SNI Inventory

Route::get('/getInventoryResults', 'Fcaore\Databucket\Http\SNIController@getInventoryResults')->name('getInventoryResults'); 
Route::get('/getInventoryFilters', 'Fcaore\Databucket\Http\SNIController@getInventoryFilters')->name('getInventoryFilters'); 
Route::get('/review_static_sni', 'Fcaore\Databucket\Http\SNIController@review_sni')->name('review_sni');
Route::get('/inventory', 'Fcaore\Databucket\Http\SNIController@inventory')->name('inventory');
Route::post('/find_dealers_by_zipcode_radius', 'Fcaore\Databucket\Http\SNIController@on_dealers_by_zipcode_radius')->name('find_dealers_by_zipcode_radius'); 

Route::post('/sniLeftFilter', 'Fcaore\Databucket\Http\SNIController@sniLeftFilter')->name('sniLeftFilter');
Route::post('/sniRightSide', 'Fcaore\Databucket\Http\SNIController@sniRightSide')->name('sniRightSide');

Route::get('/feeddealers', 'Fcaore\Databucket\Http\cronController@feeddealers')->name('feeddealers');

Route::post('/validate_zipcode', 'Fcaore\Databucket\Http\SNIController@zipValidation')->name('validate_zipcode');

Route::post('/cpo_catid_replacer', 'Fcaore\Databucket\Http\SNIController@cpo_catid_replacer')->name('cpo.cpo_catid_replacer'); 

 }); 
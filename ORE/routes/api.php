<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
 
Route::post('v1/routeone', 'API\RouteoneController@store');
//Route::post('v1/carnow', 'API\CarnowController@store'); 

//Route::post('v1/carnow/users', 'API\CarnowController@post_api');
Route::post('v1/dealereliminate', 'API\EliminateController@add');

/*
if(env('APP_ENV') == 'dev' || env('APP_ENV') == 'local' || env('APP_ENV') == 'test'){ 
	Route::prefix('v1')->group(function(){ 
					Route::get('filtermakes', 'Api\AdmintoolController@FilterMakes');
					Route::get('getmodelyear', 'Api\AdmintoolController@getModelYear');
					Route::get('getvehicleselection', 'Api\AdmintoolController@getVehicleSelection');
					Route::get('filtertrimselection', 'Api\AdmintoolController@FilterTrimSelection');
					Route::get('filtersecondaryselection', 'Api\AdmintoolController@FilterSecondarySelection');
					Route::get('sarchbysttributes', 'Api\AdmintoolController@SearchByAttributes');
					Route::get('searchbyvin', 'Api\AdmintoolController@SearchByVIN');	 
	});
}
*/


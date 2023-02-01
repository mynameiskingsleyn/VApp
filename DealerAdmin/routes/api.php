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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});


Route::prefix('v1')->group(function(){
	 //Route::post('login', 'Api\AuthController@login');
	// Route::post('login', 'Api\AuthController@login_demo');
	 Route::post('register', 'Api\AuthController@register');
	// Route::group(['middleware' => 'auth:api'], function(){
		Route::post('getUser', 'Api\AuthController@getUser');
	Route::group(['middleware' => 'connection'], function () {
	
		/* Api\FilterController */
		Route::post('getAllMakes', 'Api\FilterController@getAllMakes');
		Route::post('getModelYear', 'Api\FilterController@getModelYear');
		Route::post('getVehicleSelection', 'Api\FilterController@getVehicleSelection');
		Route::post('FilterTrimSelection', 'Api\FilterController@FilterTrimSelection');
		Route::post('FilterMsrpSelection', 'Api\FilterController@FilterMsrpSelection');
		Route::post('FilterSecondarySelection', 'Api\FilterController@FilterSecondarySelection');
		
		/* Api\ListController */
		Route::post('SearchByAttributes', 'Api\ListController@SearchByAttributes');
		Route::post('SearchByVIN', 'Api\ListController@SearchByVIN');

		/* Api\DiscountController */
		Route::post('discount/add', 'Api\DiscountController@AddDiscount');
		Route::put('discount/edit/{DealerCode}', 'Api\DiscountController@EditDiscount');
		Route::delete('discount/delete/{DiscountID}', 'Api\DiscountController@deleteDiscount');
		Route::post('discount/list', 'Api\DiscountController@getDiscount');		
		Route::post('discount/vehicles', 'Api\DiscountController@getVehicles');		
		Route::delete('vin-discount/delete/{DealerCode}/{VinNumber}/{FinanceOption}', 'Api\DiscountController@deleteVinDiscount');		
		Route::post('vin-activation', 'Api\DiscountController@vinActivation');		
		//Route::post('discount/get-list', 'Api\DiscountController@getDiscountList');

		/* Api\BulkDiscountController */
		Route::post('bulk-discount/add', 'Api\BulkDiscountController@AddDiscount');
		Route::post('bulk-discount/vehicles', 'Api\BulkDiscountController@getVehicles');	
		Route::post('bulk-discount/list', 'Api\BulkDiscountController@getDiscount');		
		Route::post('bulk-discount/remove', 'Api\BulkDiscountController@removeBulkDiscount');		
		Route::post('clear-all-discount', 'Api\BulkDiscountController@clearAllDiscounts');		
		/*Route::put('bulk-discount/edit/{DealerCode}', 'Api\BulkDiscountController@EditDiscount');*/

		/* Api\BulkDiscountController */
		Route::post('rule-discount/add', 'Api\RuleDiscountController@AddDiscount');
		Route::post('rule-discount/saved-add', 'Api\RuleDiscountController@AddSavedDiscount');

		Route::post('rule-single-discount/add', 'Api\RuleDiscountController@AddSingleDiscount');
		Route::post('rule-discount/vehicles', 'Api\RuleDiscountController@getVehicles');	
		Route::post('rule-discount/vehicles_api', 'Api\RuleDiscountController@getVehicles_api');	
		Route::post('rule-discount/list', 'Api\RuleDiscountController@getDiscount');		
		Route::post('rule-discount/vin-list', 'Api\RuleDiscountController@getVinDiscount');		
		Route::post('rule-discount/saved-list', 'Api\RuleDiscountController@savedDiscount');	
		Route::post('rule-discount/remove', 'Api\RuleDiscountController@removeBulkDiscount');		
		Route::delete('rule-discount/delete/{DiscountID}', 'Api\RuleDiscountController@deleteSavedDiscount');		
		Route::delete('rule-discount/vin-delete/{DiscountID}', 'Api\RuleDiscountController@deleteDiscount');		
		Route::post('rule-discount/delete', 'Api\RuleDiscountController@deleteAddedDiscount');		
		Route::post('filter-group/add', 'Api\RuleDiscountController@addFilter');	
		/*Route::put('bulk-discount/edit/{DealerCode}', 'Api\BulkDiscountController@EditDiscount');*/
	  // });
	 });
		/* CalculatorController */
		Route::post('paymentcalc','CalculatorController@paymentcalc');
		Route::post('feedback', 'HomeController@Feedback');
});
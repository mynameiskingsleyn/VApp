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

Route::get('/init', 'Api\FilterController@init');
Route::get('/primaryMake', 'Api\FilterController@primaryMake');
Route::get('/primaryYear/{make}', 'Api\FilterController@primaryYear');
Route::get('/primaryModelYear/{make}/{year}', 'Api\FilterController@primaryModelYear');
Route::get('/primaryTrimSelection/{make}/{year}/{model}', 'Api\FilterController@primaryTrimSelection');
Route::get('/primaryMsrp/{make}/{year}/{model}/{trim}', 'Api\FilterController@primaryMsrp');

Route::get('/login', 'HomeController@index');
Route::post('/login', 'HomeController@processLogin');

Route::get('/', 'SearchController@dealerDiscounts');

Route::group(['middleware' => 'connection'], function () {
	Route::get('/inventory', 'SearchController@dealerDiscounts');
	Route::get('/automated-inventory', 'SearchController@dealerAutomatedDiscounts');
});
Route::get('/switch-connection', 'SearchController@SwitchConnection');
/*Route::group(['middleware' => 'ssosession'], function () {
	Route::get('/', 'SearchController@dealerDiscounts');
	Route::get('/inventory', 'SearchController@dealerDiscounts');
	Route::get('/automated-inventory', 'SearchController@dealerAutomatedDiscounts');
});*/
Route::get('ssologout', 'SearchController@ssologout');

Route::post('/sso/login', 'SsoController@sso');
Route::get('/help_and_faq', 'HomeController@helpandFaq');
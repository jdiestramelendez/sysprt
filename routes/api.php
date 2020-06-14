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
//APP
Route::group(['middleware' => ['jwt.auth','api-header']], function () {
  
    Route::get('user', 'UserController@show');
    Route::post('user_rifavo', 'UserController@getRifavo');
    
});
Route::post('rifavbydriver', 'UserController@getTotalRifavByDriverAndRangeDate')->middleware('web');
Route::post('rifavbydriver_2', 'UserController@getTotalRifavByDriverAndRangeDateNew')->middleware('web');
Route::post('rifavbyasset_2', 'UserController@getTotalRifavByAssetAndRangeDateNew')->middleware('web');
Route::post('rifavwordbydriver_2', 'UserController@getWordRifavByDriverAndRangeDate')->middleware('web');
Route::post('rifavwordbyasset_2', 'UserController@getWordRifavByAssetAndRangeDate')->middleware('web');
Route::post('rifavbydriverall_2', 'UserController@getTotalRifavByDriverAndRangeDateGraph')->middleware('web');
Route::post('getviagensbyasset', 'UserController@getDetalhesViagemByAsset')->middleware('web');

Route::group(['middleware' => 'api-header'], function () {
    // The registration and login requests doesn't come with tokens 
    // as users at that point have not been authenticated yet
    // Therefore the jwtMiddleware will be exclusive of them
    Route::post('user/login', 'UserController@loginApp');
    Route::post('user/loginsystem', 'UserController@loginSystem');

});
 
Route::resource('dealers', 'DealerAPIController');

Route::resource('groups', 'GroupAPIController');

Route::resource('sub_groups', 'SubGroupAPIController');

Route::resource('sites', 'SitesAPIController');

Route::resource('assets', 'AssetsAPIController');

Route::resource('drivers', 'DriversAPIController');

Route::resource('escalas', 'EscalasAPIController');

Route::resource('quadro_horarios', 'QuadroHorariosAPIController');
<?php

use App\Http\Controllers\RazerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('ms/payment/razer/callback', [
    'uses' => 'CallbackController@index'
]);

Route::get('ms/payment/razer/redirect', [
    'uses' => 'RedirectController@index'
]);

Route::post('ms/payment/razer/query', [
    'uses' => 'QueryController@index'
]);

Route::post('ms/payment/razer/history', [
    'uses' => 'PaymentHistoryController@index'
]);

Route::middleware(['checkMerchant:encrypted_data'])->group(function () {
    // Routes accessible only to users with the specified merchant ID
    Route::get('ms/payment/razer/{encrypted_data}', [
        'uses' => 'RazerController@payment'
    ]);
});

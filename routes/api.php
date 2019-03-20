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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'Api\Version1'], function () {
    Route::put('/users/{user}/companies', 'UserController@attachCompanies');
    Route::resource('users', 'UserController');
    Route::put('/companies/{company}/users', 'CompanyController@attachUsers');
    Route::resource('companies', 'CompanyController');
});

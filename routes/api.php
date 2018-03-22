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
Route::group((['prefix' => 'v1']), function () {

    Route::post('/safebox', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@create');
    Route::get('/safebox/{id}/open', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@open');
    Route::get('/safebox/{id}/', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@content');
    Route::post('/safebox/{id}', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@addItem');

});
//Route::post('/safebox', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@create');
//Route::get('/safebox/{id}/open', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@open');
//Route::get('/safebox/{id}/', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@content');
//Route::post('/safebox/{id}', '\SafeBox\Infrastructure\UI\Http\SafeBox\SafeBoxController@addItem');


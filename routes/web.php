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

Route::get('ajax-test', 'Controller@ajaxTest');
Route::get('/', 'Controller@index');
Route::get('legal', 'Controller@legal');

Route::group(['namespace' => 'Cron', 'prefix' => 'cron'], function () {
});

//ICO
Route::get('ico', 'Ico\IcoController@ico')->name('ico');
Route::get('invest/{affiliate_code?}', 'Ico\IcoController@invest')->name('ico-invest');
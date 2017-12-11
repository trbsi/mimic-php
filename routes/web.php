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
Route::get('appstore', 'Controller@appStore');

Route::group(['namespace' => 'Cron', 'prefix' => 'cron'], function () {
});

//ICO
Route::get('ico', 'Ico\IcoController@ico')->name('ico');
Route::get('whitepaper', 'Ico\IcoController@whitePaper')->name('whitepaper-url');
Route::get('invest/{affiliate_code?}', 'Ico\IcoController@invest')->name('ico-invest');
Route::any('ico/contribute', 'Ico\IcoController@contribute')->name('ico-contribute');
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

Route::get('/', 'Controller@index');
Route::get('legal', 'Controller@legal');
Route::get('appstore', 'Controller@appStore');
Route::get('share/{id}', 'Controller@shareMimic')->name('share.mimic');

//ADMIN
Route::namespace('Admin')->prefix('admin')->group(function () {
	Route::get('push-notifications/send', 'AdminController@sendNotificationToEveryone');
});
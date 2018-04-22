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



Auth::routes();
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/searchType', 'HomeController@searchType');
Route::get('/searchSize', 'HomeController@searchSize');
Route::get('/reserve', 'ReserveController@index');
Route::get('/reserve/confirm', 'ReserveController@submitReserve');
Route::get('/reserve/{id}', 'ReserveController@ReservrRoom');
Route::get('/reserve/{id}/{timeReserve}', 'ReserveController@reserveForm');
// Route::get('/main', function(){
//     echo bcrypt('1234');
// });
Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

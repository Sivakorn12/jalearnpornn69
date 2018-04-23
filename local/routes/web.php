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
Route::get('/checkdate', 'ReserveController@CHECK_DATE_RESERVE');
Route::get('/reserve/{id}/{timeReserve}', 'ReserveController@reserveForm');

Route::get('/history', 'HistoryController@index');
// Route::get('/checkdate', 'HistoryController@CHECK_DATE_RESERVE');
// Route::get('/main', function(){
//     echo bcrypt('1234');
// });
Route::prefix('/control')->group(function () {
  Route::get('/' ,'OfficerController@index');
  Route::get('/reservation','OfficerController@indexReservation');
  Route::get('/reservation/view/{id}','OfficerController@viewReservation');
  Route::any('/reservation/{id}/confirm','OfficerController@confirmReservation');
  Route::any('/reservation/{id}/cancel','OfficerController@cancelReservation');

  Route::get('/room','OfficerController@indexRoom');
});

Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

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
Route::post('/reserve/confirm', 'ReserveController@submitReserve');
Route::get('/reserve/{id}', 'ReserveController@ReservrRoom');
Route::get('/checkdate', 'ReserveController@CHECK_DATE_RESERVE');
Route::get('/reserve/{id}/{timeReserve}/{timeSelect}', 'ReserveController@reserveForm');

Route::get('/history', 'HistoryController@index');
// Route::get('/main', function(){
//     echo bcrypt('1234');
// });
Route::prefix('/control')->group(function () {
  Route::get('/' ,'Officer\OfficerController@index');
  Route::get('/reservation','Officer\OfficerController@indexReservation');
  Route::get('/reservation/fetchTbBooking','Officer\OfficerController@fetchTbBooking');
  Route::get('/reservation/view/{id}','Officer\OfficerController@viewReservation');
  Route::any('/reservation/{id}/confirm','Officer\OfficerController@confirmReservation');
  Route::any('/reservation/{id}/cancel','Officer\OfficerController@cancelReservation');

  Route::get('/room','Officer\RoomController@index');
  Route::get('/resetStatus','Officer\OfficerController@resetStatus');
});

Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

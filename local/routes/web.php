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
// Route::get('/main', function(){
//     echo bcrypt('1234');
// });
Route::prefix('/control')->group(function () {
  Route::get('/' ,'Officer\CheckBookingController@index');
  Route::get('/checkbooking','Officer\CheckBookingController@indexReservation');
  Route::get('/checkbooking/fetchTbBooking','Officer\CheckBookingController@fetchTbBooking');
  Route::get('/checkbooking/view/{id}','Officer\CheckBookingController@viewReservation');
  Route::any('/checkbooking/{id}/confirm','Officer\CheckBookingController@confirmReservation');
  Route::any('/checkbooking/{id}/cancel','Officer\CheckBookingController@cancelReservation');

  Route::get('/room','Officer\RoomController@index');
  Route::get('/room/form','Officer\RoomController@Form');
  Route::post('/room/add','Officer\RoomController@add');
  Route::get('/room/edit/{id}','Officer\RoomController@Form');
  Route::post('/room/update','Officer\RoomController@update');
  Route::get('/room/delete/{id}','Officer\RoomController@delete');

  Route::get('/equipment','Officer\EquipmentController@index');
  Route::get('/equipment/form','Officer\EquipmentController@Form');
  Route::post('/equipment/add','Officer\EquipmentController@add');
  
  Route::get('/resetStatus','Officer\CheckBookingController@resetStatus');
});

Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

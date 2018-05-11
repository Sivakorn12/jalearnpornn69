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
Route::get('/history/{reserveid}', 'HistoryController@DELETE_RESERVE');
// Route::get('/main', function(){
//     echo bcrypt('1234');
// });
Route::any('/getdataReserve/{id}', 'Officer\DashboardController@viewBooking');
Route::any('/getdataCalendar/{id}', 'HomeController@viewBooking');


Route::prefix('/control')->group(function () {
  Route::get('/' ,'Officer\DashboardController@index');
  Route::get('/checkbooking','Officer\CheckBookingController@indexReservation');
  Route::get('/checkbooking/fetchTbBooking','Officer\CheckBookingController@fetchTbBooking');
  Route::get('/checkbooking/view/{id}','Officer\CheckBookingController@viewReservation');
  Route::any('/checkbooking/{id}/confirm','Officer\CheckBookingController@confirmReservation');
  Route::any('/checkbooking/{id}/cancel','Officer\CheckBookingController@cancelReservation');

  Route::get('/reservation','Officer\ReservationController@index');
  Route::post('/reservation/confirm','Officer\ReservationController@confirm');
  Route::get('/reservation/{id}','Officer\ReservationController@Form');
  Route::get('/reservation/{id}/{timeReserve}/{timeSelect}', 'Officer\ReservationController@reserveForm');

  Route::get('/room','Officer\RoomController@index');
  Route::get('/room/form','Officer\RoomController@Form');
  Route::post('/room/add','Officer\RoomController@add');
  Route::get('/room/edit/{id}','Officer\RoomController@Form');
  Route::post('/room/update','Officer\RoomController@update');
  Route::get('/room/delete/{id}','Officer\RoomController@delete');

  Route::get('/equipment','Officer\EquipmentController@index');
  Route::get('/equipment/form','Officer\EquipmentController@Form');
  Route::post('/equipment/add','Officer\EquipmentController@add');
  Route::get('/equipment/edit/{id}','Officer\EquipmentController@Form');
  Route::post('/equipment/update','Officer\EquipmentController@update');
  Route::get('/equipment/delete/{id}','Officer\EquipmentController@delete');

  Route::get('/holiday','Officer\HolidayController@index');
  Route::get('/holiday/form','Officer\HolidayController@Form');
  Route::any('/holiday/add','Officer\HolidayController@add');
  Route::get('/holiday/edit/{id}','Officer\HolidayController@Form');
  Route::post('/holiday/update','Officer\HolidayController@update');
  Route::get('/holiday/delete/{id}','Officer\HolidayController@delete');

  Route::get('/extratime','Officer\ExtraTimeController@index');
  Route::any('/extratime/add','Officer\ExtraTimeController@add');
  Route::any('/extratime/delete/{id}','Officer\ExtraTimeController@delete');

  Route::get('/checkdate', 'Officer\ReservationController@CHECK_DATE_RESERVE');

  Route::get('/resetStatus','Officer\CheckBookingController@resetStatus');
});

Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

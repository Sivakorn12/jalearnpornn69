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
Route::post('/reserve/confirmedit', 'ReserveController@SET_EDIT_DATA_RESERVE');
Route::get('/reserve/{id}', 'ReserveController@ReservrRoom');
Route::get('/checkdate', 'ReserveController@CHECK_DATE_RESERVE');
Route::get('/reserve/form/reserve', 'ReserveController@reserveForm');
Route::get('/reserve/{id}/{timeReserve}/{timeSelect}', 'ReserveController@reserveForm');
Route::get('/history/editdata/{id}/{timeSelect}', 'ReserveController@EDIT_DATA_RESERVE');

Route::get('/history', 'HistoryController@index');
Route::get('/history/deletedata', 'HistoryController@DELETE_RESERVE');
Route::get('/deleteborrow', 'HistoryController@DELETE_BORROW');
Route::get('/getQr', 'HistoryController@GET_QRCODE');


Route::get('/main', function(){
    echo bcrypt('1234');
});
Route::any('/getdataReserve/{id}', 'Officer\DashboardController@viewBooking');
Route::any('/getdataCalendar/{id}', 'HomeController@viewBooking');
Route::any('/getNoti', 'HomeController@getNoti');


Route::prefix('/control')->group(function () {
  Route::get('qr-code','Officer\DashboardController@qr' );
  Route::get('backupdb','Officer\DashboardController@backup2' );
  Route::any('checkdayreserve','Officer\ReservationController@checkDayReserve' );
  
  Route::get('/' ,'Officer\DashboardController@index');
  Route::get('/checkbooking','Officer\CheckBookingController@indexReservation');
  Route::get('/checkbooking/fetchTbBooking','Officer\CheckBookingController@fetchTbBooking');
  Route::get('/checkbooking/view/{id}','Officer\CheckBookingController@viewReservation');
  Route::any('/checkbooking/{id}/confirm','Officer\CheckBookingController@confirmReservation');
  Route::any('/checkbooking/{id}/cancel','Officer\CheckBookingController@cancelReservation');

  Route::get('/reservation','Officer\ReservationController@index');
  Route::post('/reservation/confirm','Officer\ReservationController@confirm');
  Route::get('/reservation/{id}','Officer\ReservationController@Form');
  Route::post('/reservation/form/reserve', 'Officer\ReservationController@reserveForm');

  Route::post('/reserve_adayinweek/confirm','Officer\ReservationController@reserve_adayinweek');
  Route::any('/reserve_adayinweek/form','Officer\ReservationController@Form_adayinweek');
 
  Route::get('/reservation/adayinweek/{id}','Officer\ReservationController@choose_adayinweek');
  

  Route::get('/room','Officer\RoomController@index');
  Route::get('/room/form','Officer\RoomController@Form');
  Route::post('/room/add','Officer\RoomController@add');
  Route::get('/room/edit/{id}','Officer\RoomController@Form');
  Route::post('/room/update','Officer\RoomController@update');
  Route::get('/room/delete/{id}','Officer\RoomController@delete');
  Route::get('/room/test','Officer\RoomController@checkreserv');

  Route::get('/roomtype','Officer\RoomTypeController@index');
  Route::post('/roomtype/save','Officer\RoomTypeController@save');
  Route::any('/roomtype/delete/{id}','Officer\RoomTypeController@delete');

  Route::get('/building','Officer\BuildingController@index');
  Route::post('/building/save','Officer\BuildingController@save');
  Route::any('/building/delete/{id}','Officer\BuildingController@delete');

  Route::get('/section','Officer\SectionController@index');
  Route::post('/section/save','Officer\SectionController@save');
  Route::any('/section/delete/{id}','Officer\SectionController@delete');

  Route::get('/department','Officer\DepartmentController@index');
  Route::post('/department/save','Officer\DepartmentController@save');
  Route::any('/department/delete/{id}','Officer\DepartmentController@delete');
  
  Route::get('/faculty','Officer\FacultyController@index');
  Route::post('/faculty/save','Officer\FacultyController@save');
  Route::any('/faculty/delete/{id}','Officer\FacultyController@delete');

  Route::get('/equipment','Officer\EquipmentController@index');
  Route::get('/equipment/form','Officer\EquipmentController@Form');
  Route::post('/equipment/add','Officer\EquipmentController@add');
  Route::get('/equipment/edit/{id}','Officer\EquipmentController@Form');
  Route::post('/equipment/update','Officer\EquipmentController@update');
  Route::get('/equipment/delete/{id}','Officer\EquipmentController@delete');

  Route::get('/return-eq','Officer\ReturnEquipController@index');
  Route::post('/return-eq/borrow','Officer\ReturnEquipController@borrow');
  Route::get('/return-eq/confirm/{id}','Officer\ReturnEquipController@confirm');
  Route::get('/return-eq/confirm-return/{id}','Officer\ReturnEquipController@confirmReturn');
  Route::get('/return-eq/cancel/{id}','Officer\ReturnEquipController@cancel');
  Route::get('/return-eq/viewdetailBorrow','Officer\ReturnEquipController@viewdetailBorrow');

  Route::get('/holiday','Officer\HolidayController@index');
  Route::get('/holiday/form','Officer\HolidayController@Form');
  Route::any('/holiday/add','Officer\HolidayController@add');
  Route::get('/holiday/edit/{id}','Officer\HolidayController@Form');
  Route::post('/holiday/update','Officer\HolidayController@update');
  Route::get('/holiday/delete/{id}','Officer\HolidayController@delete');

  Route::get('/extratime','Officer\ExtraTimeController@index');
  Route::any('/extratime/add','Officer\ExtraTimeController@add');
  Route::any('/extratime/delete/{id}','Officer\ExtraTimeController@delete');

  Route::get('/room_open','Officer\ExtraTimeController@indexRoomExOpen');
  Route::post('/room_open/save','Officer\ExtraTimeController@saveRoomExOpen');
  Route::any('/room_open/delete/{id}','Officer\ExtraTimeController@deleteRoomExOpen');

  Route::get('/checkdate', 'Officer\ReservationController@CHECK_DATE_RESERVE');

  Route::get('/resetStatus','Officer\CheckBookingController@resetStatus');
});

Route::prefix('/admin')->group(function () {
  Route::get('/', 'Admin\AdminController@index');
  Route::get('/backupdb', 'Admin\AdminController@backup_database');
  Route::get('/manageUser', 'Admin\AdminController@GET_USERS');
  Route::get('/manageUser/editstatus', 'Admin\AdminController@GET_FORM_STATUS');
  Route::post('/setstatusUser', 'Admin\AdminController@SET_STATUS_USER');
});

Route::get('auth/google', 'GoogleController@redirectToProvider');
Route::get('auth/google/callback', 'GoogleController@handleProviderCallback');

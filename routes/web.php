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

Route::get('/', function () {
    return view('adminlte::login');
});

Auth::routes();

// Route::get('/home', function() {
//     return view('home');
// })->name('home')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('/admin','HomeController@admin')->name('admin')->middleware('auth');

//User
Route::get('/create', 'RegistrationController@create')->middleware('auth');
Route::post('create', 'RegistrationController@store')->middleware('auth');
Route::get('/edit/{user}','RegistrationController@edit')->name('user_edit')->middleware('auth');
Route::post('/update/{user}','RegistrationController@edit')->middleware('auth');
Route::get('/user/{user}','RegistrationController@profile')->name('user_view')->middleware('auth');

//Leave Type
Route::get('/leavetype_create', 'LeaveTypeController@create')->middleware('auth');
Route::post('leavetype_create', 'LeaveTypeController@store')->middleware('auth');
Route::get('/delete/leave_type/{leaveType}', 'LeaveTypeController@destroy')->name('leavetype_delete')->middleware('auth');

//Employee Type
Route::get('/emptype_create', 'EmpTypeController@create')->middleware('auth');
Route::post('emptype_create', 'EmpTypeController@store')->middleware('auth');
Route::get('/delete/emp_type/{empType}','EmpTypeController@destroy')->name('emptype_delete')->middleware('auth');

//Leave Entitlement
Route::get('/create_entitlement/{empType}','LeaveEntitlementController@create')->name('leaveent_create')->middleware('auth');
Route::post('create_entitlement/{empType}', 'LeaveEntitlementController@store')->middleware('auth');

//Leave Application
Route::get('/apply/leave','LeaveApplicationController@create')->middleware('auth');
Route::get('/apply/approve','LeaveApplicationController@approve')->middleware('auth');

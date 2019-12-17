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
Route::get('/admin','HomeController@admin')->name('admin')->middleware('can:admin-dashboard');

Route::get('/myprofile','UserController@index')->name('view_profile')->middleware('auth');
Route::get('/myprofile/edit','UserController@edit')->middleware('auth');
Route::post('/myprofile/update','UserController@update')->name('update_profile')->middleware('auth');
//Change Password
Route::get('/change-password', 'ChangePasswordController@index');
Route::post('/change-password', 'ChangePasswordController@store')->name('change.password');


//Create, Edit, Delete User
Route::middleware('can:edit_users')->group(function(){
    Route::get('/create', 'RegistrationController@create')->middleware('auth');
    Route::post('create', 'RegistrationController@store')->middleware('auth');
    Route::get('/edit/{user}','RegistrationController@edit')->name('user_edit')->middleware('auth');
    Route::post('/update/{user}','RegistrationController@update')->name('user_update')->middleware('auth');
    Route::get('/user/{user}','RegistrationController@profile')->name('user_view')->middleware('auth');
});


Route::middleware('can:edit_settings')->group(function() {
    
    //Leave Type
    Route::get('/leavetype/create', 'LeaveTypeController@create')->middleware('auth');
    Route::post('leavetype/create', 'LeaveTypeController@store')->middleware('auth');
    Route::get('/delete/leave_type/{leaveType}', 'LeaveTypeController@destroy')->name('leavetype_delete')->middleware('auth');

    //Employee Type
    Route::get('/emptype/create', 'EmpTypeController@create')->middleware('auth');
    Route::post('emptype/create', 'EmpTypeController@store')->middleware('auth');
    Route::get('/emptype/edit/{empType}','EmpTypeController@edit')->name('emptype_edit')->middleware('auth');
    Route::post('emptype/update/{empType}','EmpTypeController@update')->name('emptype_update')->middleware('auth');
    Route::get('/delete/emp_type/{empType}','EmpTypeController@destroy')->name('emptype_delete')->middleware('auth');

    //Employee Group
    Route::get('/empgroup/create','EmpGroupController@create')->middleware('auth');
    Route::post('empgroup/create','EmpGroupController@store')->middleware('auth');
    Route::get('/empgroup/edit/{empGroup}','EmpGroupController@edit')->name('empgroup_edit')->middleware('auth');
    Route::post('empgroup/update/{empGroup}','EmpGroupController@update')->name('empgroup_update')->middleware('auth');
    Route::get('/delete/emp_group/{empGroup}','EmpGroupController@destroy')->name('empgroup_delete')->middleware('auth');  
    
    //Leave Entitlement
    Route::get('/entitlement/create/{empType}','LeaveEntitlementController@create')->name('leaveent_create')->middleware('auth');
    Route::post('entitlement/create/{empType}', 'LeaveEntitlementController@store')->middleware('auth');

    //Approval authority
    Route::post('/create/approval_authority/{user}','ApprovalAuthorityController@store')->name('approval_auth_create')->middleware('auth');
    Route::post('update/approval_authority/{approvalAuthority}','ApprovalAuthorityController@update')->name('approval_auth_update')->middleware('auth');
});



//Leave Application
Route::get('/apply/leave','LeaveApplicationController@create')->middleware('auth');
Route::get('/apply/approve','LeaveApplicationController@approve')->middleware('auth');

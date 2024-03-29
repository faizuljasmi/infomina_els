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

Route::get('/login', function () {
    return redirect("https://videspace.com/app/external/app/leave-dashboard");
});

Route::get('/admin-login', function () {
    return view('adminlte::login');
});

Route::get('/', function () {
    if (Auth::user() != null && (Auth::user()->user_type == 'Admin' || Auth::user()->user_type == 'Management')) {
        return redirect('/admin');
    }
    else if (Auth::user() != null && Auth::user()->user_type == 'Employee'){
        return redirect('/home');
    }
    else{
        //https://videspace.com/
        //https://videspace.com/app
        return redirect("https://videspace.com/");
    }
});

Route::get('/sso-login/{token}', 'AdminController@sso_login');
Route::post('/sso-logout/{token}', 'AdminController@sso_logout');
//Route::get('/mobile/notification', 'LeaveApplicationController@mobile_notification');

//Auth::routes();
  Route::post('login', [
    'as' => '',
    'uses' => 'Auth\LoginController@login'
  ]);
  Route::post('logout', [
    'as' => 'logout',
    'uses' => 'Auth\LoginController@logout'
  ]);
  
  // Password Reset Routes...
  Route::post('password/email', [
    'as' => 'password.email',
    'uses' => 'Auth\ForgotPasswordController@sendResetLinkEmail'
  ]);
  Route::get('password/reset', [
    'as' => 'password.request',
    'uses' => 'Auth\ForgotPasswordController@showLinkRequestForm'
  ]);
  Route::post('password/reset', [
    'as' => 'password.update',
    'uses' => 'Auth\ResetPasswordController@reset'
  ]);
  Route::get('password/reset/{token}', [
    'as' => 'password.reset',
    'uses' => 'Auth\ResetPasswordController@showResetForm'
  ]);
  
  // Registration Routes...
//   Route::get('register', [
//     'as' => 'register',
//     'uses' => 'Auth\RegisterController@showRegistrationForm'
//   ]);
//   Route::post('register', [
//     'as' => '',
//     'uses' => 'Auth\RegisterController@register'
//   ]);

// Route::get('/home', function() {
//     return view('home');
// })->name('home')->middleware('auth');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('/admin','HomeController@admin')->name('admin')->middleware('can:admin-dashboard');
Route::get('/admin/search', 'HomeController@search')->name('admin__leave_search')->middleware('auth');
Route::get('/admin/add-remark', 'HomeController@store_remarks')->name('add_remark')->middleware('auth'); // Added
Route::get('/delete-remarks', 'HomeController@delete_remarks')->middleware('auth'); // Added
Route::get('/load-remarks', 'HomeController@load_remarks')->middleware('auth'); // Added

Route::get('/myprofile','UserController@index')->name('view_profile')->middleware('auth');
Route::get('/myprofile/edit','UserController@edit')->name('edit_profile')->middleware('auth');
Route::post('/myprofile/update','UserController@update')->name('update_profile')->middleware('auth');
//Change Password
Route::get('/change-password', 'ChangePasswordController@index')->name('change_password');
Route::post('/change-password', 'ChangePasswordController@store')->name('change.password');


//Create, Edit, Delete User
Route::middleware('can:employee-data')->group(function(){
    Route::get('/create', 'RegistrationController@create')->name('user_create')->middleware('auth');
    Route::post('create', 'RegistrationController@store')->name('user_store')->middleware('auth');
    Route::get('/edit/{user}','RegistrationController@edit')->name('user_edit')->middleware('auth');
    Route::post('/update/{user}','RegistrationController@update')->name('user_update')->middleware('auth');
    Route::get('/user/{user}','RegistrationController@profile')->name('user_view')->middleware('auth');
    Route::get('/user/delete/{user}','RegistrationController@destroy')->name('user_delete')->middleware('auth');
    Route::get('/user/deactivate/{user}','RegistrationController@deactivate')->name('user_deactivate')->middleware('auth');
    Route::get('/search', 'RegistrationController@search')->name('user_search')->middleware('auth');
    Route::get('/apply/for/{user}','LeaveApplicationController@applyFor')->name('apply_for')->middleware('auth');
    Route::post('apply/for/{user}','LeaveApplicationController@submitApplyFor')->name('submit_apply_for')->middleware('auth');

    //Admin Control
    Route::post('load-history','AdminController@view_history')->middleware('auth');
    Route::post('change-leave-status', 'AdminController@change_leave_status');
    Route::get('reports', 'AdminController@index')->name('index')->middleware('auth');
    Route::post('reports/import', 'AdminController@import')->name('excel_import')->middleware('auth');
    Route::get('reports/export', 'AdminController@export')->name('excel_export')->middleware('auth');
    Route::get('reports/export-balance', 'AdminController@export_leave_balance')->name('excel_export_bal')->middleware('auth');
    Route::get('reports/autocomplete', 'AdminController@autocomplete');

    //HM
    Route::get('/healthmetrics','HealthMetricsController@checkin_index')->name('healthmetric_index')->middleware('auth');
    Route::get('/healthmetrics/search','HealthMetricsController@search_checkin')->name('healthmetric_search')->middleware('auth');
    Route::get('/healthmetrics/medical-certs','HealthMetricsController@mc_index')->name('healthmetric_mc_index')->middleware('auth');
    Route::get('/healthmetrics/medical-certs/search','HealthMetricsController@search_mc')->name('healthmetric_mc_search')->middleware('auth');
    Route::post('/fetch-healthmetrics','HealthMetricsController@fetch')->middleware('auth');
    Route::post('/revert-healthmetrics','HealthMetricsController@revert')->middleware('auth');
    Route::post('/fetch-checkins','HealthMetricsController@fetch_checkins')->middleware('auth');

    // Route::get('deduct/burnt', 'AdminController@deduct_burnt')->name('deduct-burnt');
});
//Route::get('deduct/burnt', 'AdminController@deduct_burnt')->name('deduct-burnt');

Route::middleware('can:edit_settings')->group(function() {

    //Leave Type
    Route::get('/leavetype/create', 'LeaveTypeController@create')->name('leavetype_create')->middleware('auth');
    Route::post('leavetype/create', 'LeaveTypeController@store')->name('leavetype_store')->middleware('auth');
    Route::get('/delete/leave_type/{leaveType}', 'LeaveTypeController@destroy')->name('leavetype_delete')->middleware('auth');

    //Employee Type
    Route::get('/emptype/create', 'EmpTypeController@create')->middleware('auth');
    Route::post('emptype/create', 'EmpTypeController@store')->name('emptype_store')->middleware('auth');
    Route::get('/emptype/edit/{empType}','EmpTypeController@edit')->name('emptype_edit')->middleware('auth');
    Route::post('emptype/update/{empType}','EmpTypeController@update')->name('emptype_update')->middleware('auth');
    Route::get('/delete/emp_type/{empType}','EmpTypeController@destroy')->name('emptype_delete')->middleware('auth');

    //Employee Group
    Route::get('/empgroup/create','EmpGroupController@create')->middleware('auth');
    Route::post('empgroup/create','EmpGroupController@store')->name('empgroup_store')->middleware('auth');
    Route::get('/empgroup/edit/{empGroup}','EmpGroupController@edit')->name('empgroup_edit')->middleware('auth');
    Route::post('empgroup/update/{empGroup}','EmpGroupController@update')->name('empgroup_update')->middleware('auth');
    Route::get('/delete/emp_group/{empGroup}','EmpGroupController@destroy')->name('empgroup_delete')->middleware('auth');

    //Leave Entitlement
    Route::get('/entitlement/create/{empType}','LeaveEntitlementController@create')->name('leaveent_create')->middleware('auth');
    Route::post('entitlement/create/{empType}', 'LeaveEntitlementController@store')->name('leaveent_store')->middleware('auth');

    //Set Leave Earnings amount settings
    Route::post('/leave/earnings/set/{user}','LeaveController@setEarnings')->name('earnings_set')->middleware('auth');
    //Set Brough Forward Leave amount settings
    Route::post('/leave/broughtforward/set/{user}','LeaveController@setBroughtForward')->name('brought_fwd_set')->middleware('auth');

    //Approval authority
    Route::post('/create/approval_authority/{user}','ApprovalAuthorityController@store')->name('approval_auth_create')->middleware('auth');
    Route::post('update/approval_authority/{approvalAuthority}','ApprovalAuthorityController@update')->name('approval_auth_update')->middleware('auth');

    //Holiday
    Route::get('/holiday/view','HolidayController@index')->middleware('auth');
    Route::post('/holiday/create','HolidayController@store')->name('holiday_create')->middleware('auth');
    Route::get('/holiday/edit/{holiday}','HolidayController@edit')->name('holiday_edit')->middleware('auth');
    Route::post('/holiday/update/{holiday}','HolidayController@update')->name('holiday_update')->middleware('auth');
    Route::get('/holiday/delete/{holiday}','HolidayController@delete')->name('holiday_delete')->middleware('auth');

    //Country
    Route::get('/countries/index','CountryController@index')->middleware('auth');
    Route::get('/countries/create','CountryController@create')->name('countries_create')->middleware('auth');
    Route::post('/countries/create','CountryController@store')->name('country_store')->middleware('auth');
    Route::get('/countries/edit/{country}','CountryController@edit')->name('country_edit')->middleware('auth');
    Route::post('/countries/update/{country}','CountryController@update')->name('country_update')->middleware('auth');
    Route::get('/countries/delete/{country}','CountryController@delete')->name('country_delete')->middleware('auth');

    //State
    Route::get('/states/index','StateController@index')->middleware('auth');
    Route::get('/states/create','StateController@create')->name('state_create')->middleware('auth');
    Route::post('/states/create','StateController@store')->name('state_store')->middleware('auth');
    Route::get('/states/create/for/{country}','StateController@add_specific')->name('state_store_specific')->middleware('auth');
    Route::get('/states/edit/{state}','StateController@edit')->name('state_edit')->middleware('auth');
    Route::post('/states/update/{state}','StateController@update')->name('state_update')->middleware('auth');
    Route::get('/states/delete/{state}','StateController@delete')->name('state_delete')->middleware('auth');
    Route::get('/states/filter','StateController@filter')->middleware('auth');

    //Branch
    Route::get('/branches/index','BranchController@index')->middleware('auth');
    Route::get('/branches/create','BranchController@create')->name('branch_create')->middleware('auth');
    Route::post('/branches/create','BranchController@store')->name('branch_store')->middleware('auth');
    //Route::get('/branches/create/for/{state}','BranchController@add_specific')->name('branch_store_specific')->middleware('auth');
    Route::get('/branches/edit/{branch}','BranchController@edit')->name('branch_edit')->middleware('auth');
    Route::post('/branches/update/{branch}','BranchController@update')->name('branch_update')->middleware('auth');
    Route::get('/branches/delete/{branch}','BranchController@delete')->name('branch_delete')->middleware('auth');
});

//Leave Application
// Route::get('/leave/apply','LeaveApplicationController@create')->middleware('auth');
Route::post('leave/apply','LeaveApplicationController@store')->name('leaveapp_store')->middleware('auth');
Route::get('/leave/apply/view/{leaveApplication}','LeaveApplicationController@view')->name('view_application')->middleware('auth');
Route::get('/leave/apply/edit/{leaveApplication}','LeaveApplicationController@edit')->name('edit_application')->middleware('auth');
Route::post('/leave/apply/update/{leaveApplication}','LeaveApplicationController@update')->name('update_application')->middleware('auth');
Route::get('/leave/apply/approve/{leaveApplication}','LeaveApplicationController@approve')->name('approve_application')->middleware('auth');
Route::get('/leave/apply/deny/{leaveApplication}','LeaveApplicationController@deny')->name('deny_application')->middleware('auth');
Route::post('/leave/apply/cancel/{leaveApplication}','LeaveApplicationController@cancel')->name('cancel_application')->middleware('auth');

//Replacement leave
// Route::get('/leave/replacement/apply','ReplacementLeaveController@create')->middleware('auth');

//Excel Import & Export
//Route::get('import-excel', 'ExcelController@index');
//Route::post('import-excel', 'ExcelController@import');





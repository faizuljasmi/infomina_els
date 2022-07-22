<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Mobile
Route::post('/leave/list/mobile','LeaveApplicationController@list');
Route::post('/leave/list/my-apl/mobile','LeaveApplicationController@list_my_pending');
Route::post('/leave/action/mobile','LeaveApplicationController@mobile_action');
Route::post('/leave/total-pending/mobile','LeaveApplicationController@pending_count');

//Workspace API
//user_email
Route::post('/wspace/get-my-leaves','WorkspaceController@getMyLeave')->middleware('cors');
//user_email
Route::post('/wspace/get-to-approve','WorkspaceController@getToApproveLeaves')->middleware('cors');
//leave_app_id
Route::post('/wspace/get-leave-details','WorkspaceController@getLeaveAppDetails')->middleware('cors');
//leave_app_id, approver_email
Route::post('/wspace/approve-leave','WorkspaceController@approveLeave')->middleware('cors');
//leave_app_id, approver_email
Route::post('/wspace/approve-replacement-leave','WorkspaceController@approveReplacementLeave')->middleware('cors');
////leave_app_id, approver_email
Route::post('/wspace/deny-leave','WorkspaceController@denyLeave')->middleware('cors');
////leave_app_ids, approver_email
Route::post('/wspace/is-pending','WorkspaceController@is_pending')->middleware('cors');
///user_email
Route::post('/wspace/get-leave-status','WorkspaceController@getLeaveStatus')->middleware('cors');
///get holidays
Route::post('/wspace/get-holidays','WorkspaceController@getHolidays')->middleware('cors');


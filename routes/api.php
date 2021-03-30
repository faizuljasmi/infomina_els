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
Route::post('/wspace/get-my-pending','WorkspaceController@getMyPendingLeave');
//user_email
Route::post('/wspace/get-to-approve','WorkspaceController@getToApproveLeaves');
//leave_app_id
Route::post('/wspace/get-leave-details','WorkspaceController@getLeaveAppDetails');
//leave_app_id, approver_email
Route::post('/wspace/approve-leave','WorkspaceController@approveLeave');
//leave_app_id, approver_email
Route::post('/wspace/approve-replacement-leave','WorkspaceController@approveReplacementLeave');
////leave_app_id, approver_email
Route::post('/wspace/deny-leave','WorkspaceController@denyLeave');


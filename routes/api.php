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
Route::post('/wspace/get-my-pending','WorkspaceController@getMyPendingLeave');
Route::post('/wspace/get-to-approve','WorkspaceController@getToApproveLeaves');
Route::post('/wspace/get-leave-details','WorkspaceController@getLeaveAppDetails');
Route::post('/wspace/approve-leave','WorkspaceController@approveLeave');
Route::post('/wspace/approve-replacement-leave','WorkspaceController@approveReplacementLeave');
Route::post('/wspace/deny-leave','WorkspaceController@denyLeave');


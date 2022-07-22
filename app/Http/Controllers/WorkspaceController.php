<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveApplication;
use App\Services\LeaveService;
use App\User;
use App\LeaveType;
use App\ApprovalAuthority;
use App\Holiday;
use App\Jobs\NotifyAuthorityEmail;
use Carbon\Carbon;
use Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Jobs\NotifyWspace;
use App\Jobs\NotifyUserEmail;



class WorkspaceController extends Controller
{
    protected $leaveService;
    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }
    /**
     * Get the user's pending leaves
     */
    public function getMyLeave(Request $request)
    {
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            try {

                $user_email = $request->user_email;
                $offset = $request->offset;
                $user = User::where('email', $user_email)->firstOrFail();
                $user_id = $user->id;
                $pending_leaves = LeaveApplication::orderBy('created_at', 'DESC')->where(function ($query) use ($user_id) {
                    $query->where('status', 'PENDING_1')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'PENDING_2')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'PENDING_3')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'DENIED_1')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'DENIED_2')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'DENIED_3')
                        ->where('user_id', $user_id);
                })->orWhere(function ($query) use ($user_id) {
                    $query->where('status', 'APPROVED')
                        ->where('user_id', $user_id);
                })->whereYear('created_at', date('Y'))->paginate($offset);

                $count = $pending_leaves->total();

                $trimmed = $pending_leaves->map(function ($item, $key) {

                    $status = $this->leaveService->getStatusDesc($item);

                    return [
                        'leave_id' => $item->id,
                        'leave_type' => $item->leaveType->name,
                        'name' => $item->user->name,
                        'email' => $item->user->email,
                        'status' => $item->status,
                        'status_desc' => $status,
                        'approver_email' => [
                            'approver_one' => $item->approver_one ? $item->approver_one->email : ' ',
                            'approver_two' => $item->approver_two ? $item->approver_two->email : ' ',
                            'approver_three' => $item->approver_three ? $item->approver_three->email : ' ',
                        ],
                        'apply_for' => $item->apply_for,
                        'date_from' => $item->date_from,
                        'date_to' => $item->date_to,
                        'date_submitted' => $item->created_at,
                        'total_days' => $item->total_days,
                    ];
                });

                $data = [
                    'total' => $count,
                    'data' => $trimmed
                ];
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
            return response()->json($data);
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }


    /**
     * Get the user's to approve leaves (if the user is an approval authority)
     */
    public function getToApproveLeaves(Request $request)
    {
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            try {
                $offset = $request->offset;
                $user_email = $request->user_email;
                $user = User::where('email', $user_email)->firstOrFail();
                $trimmed = [];
                $data = [];
                if ($this->leaveService->isAuthority($user)) {

                    $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                        $query->where('status', 'PENDING_1')
                            ->where('approver_id_1', $user->id);
                    })->orWhere(function ($query) use ($user) {
                        $query->where('status', 'PENDING_2')
                            ->where('approver_id_2', $user->id);
                    })->orWhere(function ($query) use ($user) {
                        $query->where('status', 'PENDING_3')
                            ->where('approver_id_3', $user->id);
                    })->whereYear('created_at', date('Y'))->paginate($offset);

                    $count = $leaveApps->total();

                    $trimmed = $leaveApps->map(function ($item, $key) {

                        $status = $this->leaveService->getStatusDesc($item);

                        return [
                            'leave_id' => $item->id,
                            'leave_type' => $item->leaveType->name,
                            'name' => $item->user->name,
                            'email' => $item->user->email,
                            'status' => $item->status,
                            'status_desc' => $status,
                            'apply_for' => $item->apply_for,
                            'date_from' => $item->date_from,
                            'date_to' => $item->date_to,
                            'date_submitted' => $item->created_at,
                            'total_days' => $item->total_days,
                        ];
                    });

                    $data = [
                        'total' => $count,
                        'data' => $trimmed
                    ];
                }
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
            return response()->json($data);
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    public function getLeaveAppDetails(Request $request)
    {

        //Get user id
        //Get leave app details
        //Generate link to view, but according to role (Emp/Authority)
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            try {
                $leave_app_id = $request->leave_app_id;
                $leave_app = LeaveApplication::findOrFail($leave_app_id);
                $status = $this->leaveService->getStatusDesc($leave_app);

                $trimmed = [
                    'leave_id' => $leave_app->id,
                    'leave_type' => $leave_app->leaveType->name,
                    'status' => $leave_app->status,
                    'status_desc' => $status,
                    'applicant_name' => $leave_app->user->name,
                    'applicant_email' => $leave_app->user->email,
                    'date_from' => $leave_app->date_from,
                    'date_to' => $leave_app->date_to,
                    'apply_for' => $leave_app->apply_for,
                    'date_submitted' => $leave_app->created_at,
                    'total_days' => $leave_app->total_days,
                    'resume_date' => $leave_app->date_resume,
                    'reason' => $leave_app->reason,
                    'relief_personnel' => $leave_app->relief_personnel ? $leave_app->relief_personnel->name : ' ',
                    'emergency_contact_name' => $leave_app->emergency_contact_name,
                    'emergency_contact_no' => $leave_app->emergency_contact_no,
                    'remarks' => $leave_app->remarks,
                    'remarker' => $leave_app->remarker ? $leave_app->remarker->name : ' ',
                    'approver_one' => $leave_app->approver_one ? $leave_app->approver_one->email : ' ',
                    'approver_two' => $leave_app->approver_two ? $leave_app->approver_two->email : ' ',
                    'approver_three' => $leave_app->approver_three ? $leave_app->approver_three->email : ' ',
                    'attachment_url' => $leave_app->attachment_url
                ];
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
            return response()->json($trimmed);
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    public function approveLeave(Request $request)
    {
        //ni yg maleh ni
        //tgk leave type apa
        $transaction_status = [];
        $status = "";
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            try {
                $leave_app_id = $request->leave_app_id;
                $approver_email = $request->approver_email;
                $leave_app = LeaveApplication::findOrFail($leave_app_id);
                $approver = User::where('email', $approver_email)->firstOrFail();
                $prev_status = $leave_app->status;
                //Check balance
                if ($this->leaveService->isBalanceEnough($leave_app->user->id, $leave_app->leaveType->id, $leave_app->total_days) == false) {
                    return response()->json(['error' => "User does not have enough balance"]);
                }

                //Approve or Deny
                $leave_app = $this->leaveService->approveOrDeny($leave_app_id, $approver->id, "APPROVE", "wspace");

                //If somehow the approval is done when the leave is not pending on them
                if ($leave_app->status == $prev_status) {
                    $data = [
                        'error' => "Action is not executed: the leave application is not pending on your level."
                    ];
                    return response()->json($data);
                } else {

                    $status = $this->leaveService->getStatusDesc($leave_app);
                    $transaction_status = [
                        'old_status' => $prev_status,
                        'new_status' => $status
                    ];
                    if ($leave_app->status == "APPROVED") {

                        //Add amount of days to taken leave
                        $this->leaveService->setLeaveTaken($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'ADD');
                        //Subtract amount of days from leave balance
                        $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'SUBTRACT');

                        if ($leave_app->leaveType->name == 'Sick') {
                            //Subtract amount of days from Hospitalization balance as well. [Hosp]
                            $this->leaveService->setLeaveBalance($leave_app->user->id, 4, $leave_app->total_days, 'SUBTRACT');
                        } else if ($leave_app->leaveType->name == 'Emergency') {
                            //Subtract amount of days from leave balance [Annual]
                            $this->leaveService->setLeaveBalance($leave_app->user->id, 1, $leave_app->total_days, 'SUBTRACT');
                        }
                    }
                    NotifyWspace::dispatch($leave_app, $this->leaveService)->delay(now()->addMinutes(1));
                    NotifyUserEmail::dispatch($leave_app)->delay(now()->addMinutes(1));
                    NotifyAuthorityEmail::dispatch($leave_app, $this->leaveService)->delay(now()->addMinutes(1));
                }
                $data = [
                    'success' => [
                        'desc' => $transaction_status,
                        'leave_application' => $leave_app
                    ]
                ];
                //$data = $this->leaveService->setLeaveTaken(5,1,2,"Add");
                return response()->json($data);
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }

        //do operation tolak tambah
        //hantar email
        //return message
    }

    public function approveReplacementLeave(Request $request)
    {
        //Init transaction status
        $transaction_status = [];
        //Init status
        $status = "";
        //Get bearer token from request
        $secret_key = $request->bearerToken();
        //Check if secret key mathces
        if ($secret_key == config('wspace.secret')) {
            try {
                //Get leave app id from request
                $leave_app_id = $request->leave_app_id;
                //Get approver_email from request
                $approver_email = $request->approver_email;
                //Get leave app from DB
                $leave_app = LeaveApplication::findOrFail($leave_app_id);
                //Get user who approves
                $approver = User::where('email', $approver_email)->firstOrFail();
                //Stor current status as prev status
                $prev_status = $leave_app->status;

                //Exceute Approve or Deny
                $leave_app = $this->leaveService->approveOrDeny($leave_app_id, $approver->id, "APPROVE", "wspace");

                //If somehow the approval is done when the leave is not pending on them
                if ($leave_app->status == $prev_status) {
                    $data = [
                        'error' => "Action is not executed: the leave application is not pending on your level."
                    ];
                    return response()->json($data);
                    //If there is change is status
                } else {
                    //Compose transaction status
                    $status = $this->leaveService->getStatusDesc($leave_app);
                    $transaction_status = [
                        'old_status' => $prev_status,
                        'new_status' => $status
                    ];
                    //If replacement leave is approved
                    if ($leave_app->status == "APPROVED") {
                        //If the replacement leave is an apply leave
                        if ($this->leaveService->isApply($leave_app)) {

                            //Check if the balance is enough
                            if ($this->leaveService->isBalanceEnough($leave_app->user->id, $leave_app->leaveType->id, $leave_app->total_days) == false) {
                                //If not enough, change the leave status back to previous and return error
                                $leave_app->status = $prev_status;
                                $leave_app->save();
                                return response()->json(['error' => "User does not have enough Replacement Claim balance"]);
                            }

                            //Get the claim application related to this app
                            $claimApp = $this->leaveService->getReplacementClaim($leave_app);
                            //Get all apply replacement applications related to this claim app
                            $applyApps = $this->leaveService->getReplacementApplications($claimApp);
                            //Get total days of all those applications
                            $total_days = $this->leaveService->getTotalDays($applyApps);

                            //If the total days is fully used including this application, set the claim application status to TAKEN
                            if ($total_days == $claimApp->total_days) {
                                $this->leaveService->setTaken($claimApp);
                            } else if ($total_days > $claimApp->total_days) {
                                $leave_app->status = $prev_status;
                                $leave_app->save();
                                return response()->json(['error' => "User does not have enough Replacement Claim balance."]);
                            }

                            //Add amount of days to taken leave
                            $this->leaveService->setLeaveTaken($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'ADD');
                            //Subtract amount of days from leave balance
                            $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'SUBTRACT');
                        } else if ($this->leaveService->isClaim($leave_app)) {
                            //ADD days to replacement leave earning
                            $this->leaveService->setLeaveEarning($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'ADD');
                            //ADD days to replacement leave balance
                            $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'ADD');
                        }
                    }

                    //Notify users
                    NotifyWspace::dispatch($leave_app, $this->leaveService)->delay(now()->addMinutes(1));
                    NotifyUserEmail::dispatch($leave_app)->delay(now()->addMinutes(1));
                    NotifyAuthorityEmail::dispatch($leave_app, $this->leaveService)->delay(now()->addMinutes(1));
                }
                $data = [
                    'success' => [
                        'desc' => $transaction_status,
                        'leave_application' => $leave_app
                    ]
                ];
                return response()->json($data);
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    public function denyLeave(Request $request)
    {
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            try {
                $leave_app_id = $request->leave_app_id;
                $approver_email = $request->approver_email;
                $approver = User::where('email', $approver_email)->firstOrFail();
                $leave_app = LeaveApplication::findOrFail($leave_app_id);
                $prev_status = $leave_app->status;
                $leave_app = $this->leaveService->approveOrDeny($leave_app->id, $approver->id, "DENY", "wspace");
                //If somehow the approval is done when the leave is not pending on them
                if ($leave_app->status == $prev_status) {
                    $data = [
                        'error' => "Action is not executed: the leave application is not pending on your level."
                    ];
                    return response()->json($data);
                }
                NotifyUserEmail::dispatch($leave_app)->delay(now()->addMinutes(1));
                return  response()->json($leave_app);
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    //isauthority

    //ispending
    /**
     * leave app id in array
     * approver_email
     */
    public function is_pending(Request $request)
    {
        $secret_key = $request->bearerToken();
        if ($secret_key == config('wspace.secret')) {
            $res = [];
            try {
                $leave_apps = $request->leave_app_id;
                $approver_email = $request->approver_email;
                for ($i = 0; $i < count($leave_apps); $i++) {
                    $is_pending = $this->leaveService->is_pending_at_user($leave_apps[$i], $approver_email);
                    //assign value is_pending to key leave_id and populate in array res as an object
                    $res[$leave_apps[$i]] = $is_pending;
                }
                return $res;
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        } else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    public function getLeaveStatus(Request $request){
        $secret_key = $request->bearerToken();

        if ($secret_key == config('wspace.secret')) {
            
            try {
                $user_email = $request->user_email;
                $user = User::where('email', $user_email)->firstOrFail();
                $toret = [];
                $leave = LeaveApplication::where('user_id', $user->id)->where('status','APPROVED')->where('date_from','>=',Carbon::now()->subDays(1))->where('date_from','<=',Carbon::now()->addDays(14))->orderBy('date_from', 'DESC')->with(['leaveType'])->first();                                       
                //$leave = LeaveApplication::where('user_id',$user->id)->where('status','APPROVED')->where('date_from','<=',Carbon::now()->addDays(14))->orderBy('date_from', 'DESC')->with(['leaveType'])->first();
                //$leave = LeaveApplication::where('user_id',$user->id)->where('status','APPROVED')->where('date_from','>=',Carbon::now()->addDays(14))->orderBy('date_from', 'ASC')->with(['leaveType'])->first();
                $wordings = "";
                if($leave){
                    if(Carbon::today()->toDateString() == $leave->date_from){
                        if($leave->total_days > 1){
                            $date_to = Carbon::parse($leave->date_to)->format('M d Y');
                            $wordings = "On Leave Today Until ".$date_to;
                        }
                        else{
                            $wordings = "On Leave Today";
                        }
                    }
                    else{
                        $leave_date = Carbon::parse($leave->date_from)->format('M d Y');
                        $wordings = "Upcoming Leave: ".$leave_date;
                    }
                    $toret = [
                        "leave_type" => $leave->leaveType->name,
                        "date_from" => $leave->date_from,
                        "date_to" => $leave->date_to,
                        "total_days" => $leave->total_days,
                        "status" => $wordings
                    ];
                    return response()->json($toret);
                }
                else{
                    $toret = [
                        "leave_type" => null,
                        "date_from" => null,
                        "date_to" => null,
                        "total_days" => null,
                        "status" => "No Upcoming Leave"
                    ];
                    return response()->json($toret);
                }
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        }
        else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }

    public function getHolidays(Request $request){
        $secret_key = $request->bearerToken();

        if ($secret_key == config('wspace.secret')) {
            try {
                
                $year = $request->year;
                $time = $year."-01-01 00:00:00";
                $date = new Carbon( $time );   

                $holidays = Holiday::with(['state' => function ($query) {
                    $query->select('id', 'name');
                }, 'country' => function ($query) {
                    $query->select('id', 'name');
                }])->whereYear('date_from',$date->year)->select('id','name','date_from','date_to','total_days','state_id','country_id')->get();

                return response()->json($holidays);
            } catch (ModelNotFoundException $exception) {
                return response()->json($exception->getMessage());
            }
        }
        else {
            return response()->json(['error' => "Authentication failed. Your connection are not authorized to make a request"]);
        }
    }
}

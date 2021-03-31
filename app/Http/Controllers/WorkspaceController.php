<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveApplication;
use App\Services\LeaveService;
use App\User;
use App\LeaveType;
use App\ApprovalAuthority;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

define("PENDING_MSG", "Pending approval by ");


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
    public function getMyPendingLeave(Request $request)
    {
        try {
            $user_email = $request->user_email;
            $date = Carbon::now()->subDays(14);
            $user = User::where('email', $user_email)->firstOrFail();
            $user_id = $user->id;
            $pending_leaves = LeaveApplication::where('created_at', '>=', $date)->where(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_1')
                    ->where('user_id', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_2')
                    ->where('user_id', $user_id);
            })->orWhere(function ($query) use ($user_id) {
                $query->where('status', 'PENDING_3')
                    ->where('user_id', $user_id);
            })->get();

            $trimmed = $pending_leaves->map(function ($item, $key) {

                if ($item->status == "PENDING_1") {
                    $status = PENDING_MSG . $item->approver_one->name;
                } else if ($item->status == "PENDING_2") {
                    $status = PENDING_MSG . $item->approver_two->name;
                } else {
                    $status = PENDING_MSG . $item->approver_three->name;
                }

                return [
                    'leave_id' => $item->id,
                    'leave_type' => $item->leaveType->name,
                    'name' => $item->user->name,
                    'date_from' => $item->date_from,
                    'date_to' => $item->date_to,
                    'date_submitted' => $item->created_at,
                    'total_days' => $item->total_days,
                    'status' => $status
                ];
            });
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }
        return response()->json($trimmed);
    }


    /**
     * Get the user's to approve leaves (if the user is an approval authority)
     */
    public function getToApproveLeaves(Request $request)
    {
        try {
            $user_email = $request->user_email;
            $user = User::where('email', $user_email)->firstOrFail();
            $trimmed = [];
            if ($user->user_type == "Authority" || $user->user_type == "Management" || $user->user_type == "Admin") {

                $leaveApps = LeaveApplication::where(function ($query) use ($user) {
                    $query->where('status', 'PENDING_1')
                        ->where('approver_id_1', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'PENDING_2')
                        ->where('approver_id_2', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('status', 'PENDING_3')
                        ->where('approver_id_3', $user->id);
                })->get();

                $trimmed = $leaveApps->map(function ($item, $key) {

                    if ($item->status == "PENDING_1") {
                        $status = PENDING_MSG . $item->approver_one->name;
                    } else if ($item->status == "PENDING_2") {
                        $status = PENDING_MSG . $item->approver_two->name;
                    } else {
                        $status = PENDING_MSG . $item->approver_three->name;
                    }

                    return [
                        'leave_id' => $item->id,
                        'leave_type' => $item->leaveType->name,
                        'name' => $item->user->name,
                        'date_from' => $item->date_from,
                        'date_to' => $item->date_to,
                        'date_submitted' => $item->created_at,
                        'total_days' => $item->total_days,
                        'status' => $status
                    ];
                });
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }
        return response()->json($trimmed);
    }

    public function getLeaveAppDetails(Request $request)
    {

        //Get user id
        //Get leave app details
        //Generate link to view, but according to role (Emp/Authority)
        try {
            $leave_app_id = $request->leave_app_id;
            $leave_app = LeaveApplication::findOrFail($leave_app_id);
            $status = "";
            if ($leave_app->status == "PENDING_1") {
                $status = PENDING_MSG . $leave_app->approver_one->name;
            } else if ($leave_app->status == "PENDING_2") {
                $status = PENDING_MSG . $leave_app->approver_two->name;
            } else {
                $status = PENDING_MSG . $leave_app->approver_three->name;
            }

            $trimmed = [
                'leave_id' => $leave_app->id,
                'leave_type' => $leave_app->leaveType->name,
                'applicant_name' => $leave_app->user->name,
                'date_from' => $leave_app->date_from,
                'date_to' => $leave_app->date_to,
                'date_submitted' => $leave_app->created_at,
                'total_days' => $leave_app->total_days,
                'resume_date' => $leave_app->date_resume,
                'reason' => $leave_app->reason,
                'relief_personnel' => $leave_app->relief_personnel->name,
                'emergency_contact_name' => $leave_app->emergency_contact_name,
                'emergency_contact_no' => $leave_app->emergency_contact_no,
                'remarks' => $leave_app->remarks,
                'attachment_url' => $leave_app->attachment_url,
                'status' => $status
            ];
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }

        return response()->json($trimmed);
    }

    public function approveLeave(Request $request)
    {
        //ni yg maleh ni
        //tgk leave type apa

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
            $leave_app = $this->leaveService->approveOrDeny($leave_app_id, $approver->id, "APPROVE");

            if ($leave_app->status == "APPROVED") {

                //Add amount of days to taken leave
                $this->leaveService->setLeaveTaken($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Add');
                //Subtract amount of days from leave balance
                $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Subtract');

                if ($leave_app->leaveType->name == 'Sick') {
                    //Subtract amount of days from Hospitalization balance as well. [Hosp]
                    $this->leaveService->setLeaveBalance($leave_app->user->id, 4, $leave_app->total_days, 'Subtract');
                } else if ($leave_app->leaveType->name == 'Emergency') {
                    //Subtract amount of days from leave balance [Annual]
                    $this->leaveService->setLeaveBalance($leave_app->user->id, 1, $leave_app->total_days, 'Subtract');
                }
            }
            $data = [
                'leave_application' => $leave_app
            ];
            //$data = $this->leaveService->setLeaveTaken(5,1,2,"Add");
            return response()->json($data);
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }

        //do operation tolak tambah
        //hantar email
        //return message
    }

    public function approveReplacementLeave(Request $request)
    {
        try {
            $leave_app_id = $request->leave_app_id;
            $approver_email = $request->approver_email;
            $leave_app = LeaveApplication::findOrFail($leave_app_id);
            $approver = User::where('email', $approver_email)->firstOrFail();
            $prev_status = $leave_app->status;

            if ($this->leaveService->isApply($leave_app)) {

                if ($this->leaveService->isBalanceEnough($leave_app->user->id, $leave_app->leaveType->id, $leave_app->total_days) == false) {
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
                    return response()->json(['error' => "User does not have enough Replacement Leave balance."]);
                }

                //Add amount of days to taken leave
                $this->leaveService->setLeaveTaken($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Add');
                //Subtract amount of days from leave balance
                $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Subtract');
            } else if ($this->leaveService->isClaim($leave_app)) {
                //Add days to replacement leave earning
                $this->leaveService->setLeaveEarning($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Add');
                //Add days to replacement leave balance
                $this->leaveService->setLeaveBalance($leave_app->user->id, $leave_app->leave_type_id, $leave_app->total_days, 'Add');
            }

            //Approve or Deny
            $leave_app = $this->leaveService->approveOrDeny($leave_app_id, $approver->id, "APPROVE");
            return response()->json($leave_app);
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }
    }

    public function denyLeave(Request $request)
    {
        try {
            $leave_app_id = $request->leave_app_id;
            $approver_email = $request->approver_email;
            $approver = User::where('email', $approver_email)->firstOrFail();
            $leave_app = LeaveApplication::findOrFail($leave_app_id);
            $leave_app = $this->leaveService->approveOrDeny($leave_app->id, $approver->id, "DENY");
            return  response()->json($leave_app);
        } catch (ModelNotFoundException $exception) {
            return response()->json($exception->getMessage());
        }
    }
}
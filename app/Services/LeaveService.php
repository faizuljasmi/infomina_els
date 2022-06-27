<?php

namespace App\Services;

use App\LeaveBalance;
use App\TakenLeave;
use App\LeaveApplication;
use App\LeaveEarning;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\History;
use App\ReplacementRelation;
use App\Notifications\NewApplication;
use App\Notifications\StatusUpdate;
use App\Notifications\CancelApplication;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Carbon\Carbon;
use App\User;

define("PENDING_MSG", "Pending approval by ");
define("DENIED_MSG", "Denied by ");

class LeaveService
{

    /**
     * To get leave balance for certain user
     */
    public function getLeaveBalance($user_id, $leave_type_id)
    {
        $leaveBal = LeaveBalance::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        return $leaveBal;
    }

    public function getLeaveTaken($user_id, $leave_type_id)
    {
        $leaveTaken = TakenLeave::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        return $leaveTaken;
    }

    public function getLeaveEarning($user_id, $leave_type_id)
    {
        $leaveEarning = LeaveEarning::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        return $leaveEarning;
    }

    /**
     * To set leave balance for certain user
     */
    public function setLeaveBalance($user_id, $leave_type_id, $no_of_days, $operation)
    {

        $leaveBal = LeaveBalance::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        if ($operation == "ADD") {
            $leaveBal->no_of_days += $no_of_days;
        } else {
            $leaveBal->no_of_days -= $no_of_days;
        }
        $leaveBal->update();

        return $leaveBal;
    }

    /**
     * To set taken leave for certain user
     */
    public function setLeaveTaken($user_id, $leave_type_id, $no_of_days, $operation)
    {

        $leaveTaken = TakenLeave::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        if ($operation == "ADD") {
            $leaveTaken->no_of_days += $no_of_days;
        } else {
            $leaveTaken->no_of_days -= $no_of_days;
        }
        $leaveTaken->update();

        return $leaveTaken;
    }

    public function setLeaveEarning($user_id, $leave_type_id, $no_of_days, $operation)
    {

        $leaveEarning = LeaveEarning::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        if ($operation == "ADD") {
            $leaveEarning->no_of_days += $no_of_days;
        } else {
            $leaveEarning->no_of_days -= $no_of_days;
        }
        $leaveEarning->update();

        return $leaveEarning;
    }

    /**
     * To change leave application status based on approve or deny action
     */
    public function approveOrDeny($leave_app_id, $approver_id, $operation, $from)
    {

        $leave_app = LeaveApplication::findOrFail($leave_app_id);

        //Get leave application authorities ID
        $la_1 = $leave_app->approver_id_1;
        $la_2 = $leave_app->approver_id_2;
        $la_3 = $leave_app->approver_id_3;
        $approver_name = "";

        if ($operation == "APPROVE") {
            if ($approver_id == $la_1 && $leave_app->status == 'PENDING_1') {
                $approver_name = $leave_app->approver_one->name;
                if ($la_2 == null) {
                    $leave_app->status = 'APPROVED';
                }
                //else update status to pending 2,
                else {
                    $leave_app->status = 'PENDING_2';
                }
            } else if ($approver_id == $la_2 && $leave_app->status == 'PENDING_2') {
                $approver_name = $leave_app->approver_two->name;
                //if no authority 3, terus change to approved
                if ($la_3 == null) {
                    $leave_app->status = 'APPROVED';
                }
                //else update status to pending 3
                else {
                    $leave_app->status = 'PENDING_3';
                }
            } else if ($approver_id == $la_3 && $leave_app->status == 'PENDING_3') {
                $approver_name = $leave_app->approver_three->name;
                $leave_app->status = 'APPROVED';
            } else {
                return $leave_app;
            }

            $action = "Approved by " . $approver_name . " from " . $from;
            $this->recordHistory($leave_app, $approver_id, $action);
        } else if ($operation == "DENY") {
            if ($approver_id == $la_1 && $leave_app->status == 'PENDING_1') {
                $approver_name = $leave_app->approver_one->name;
                $leave_app->status = 'DENIED_1';
            } else if ($approver_id == $la_2 && $leave_app->status == 'PENDING_2') {
                $approver_name = $leave_app->approver_two->name;
                $leave_app->status = 'DENIED_2';
            } else if ($approver_id == $la_3 && $leave_app->status == 'PENDING_3') {
                $approver_name = $leave_app->approver_three->name;
                $leave_app->status = 'DENIED_3';
            } else {
                return $leave_app;
            }

            $action = "Denied by " . $approver_name . " from " . $from;
            $this->recordHistory($leave_app, $approver_id, $action);
        } else if ($operation == "CANCEL") {
            $this->recordHistory($leave_app, $approver_id, "Cancelled");
            $leave_app->status = 'CANCELLED';
        }
        $leave_app->update();
        // $this->notifyUser($leave_app, $leave_app->user);
        // $this->notifyWspace($leave_app);
        return $leave_app;
    }

    /**
     * To notify approver authority
     */
    public function notifyAuthority($leave_app)
    {
        try {
            if ($leave_app->status == "PENDING_1") {
                $leave_app->approver_one->notify(new NewApplication($leave_app));
            } else if ($leave_app->status == "PENDING_2") {
                $leave_app->approver_two->notify(new NewApplication($leave_app));
            } else if ($leave_app->status == "PENDING_3") {
                $leave_app->approver_three->notify(new NewApplication($leave_app));
            } else {
                return "Mail not sent";
            }
            return "Mail sent";
        } catch (\Exception $e) { // Using a generic exception
            return 'Mail not sent';
        }
    }

    /**
     * To notify user about any status update
     */
    public function notifyUser($leave_app, $user)
    {

        try {
            if ($leave_app->status != "CANCELLED") {
                $leave_app->user->notify(new StatusUpdate($leave_app));
            } else {
                $leave_app->user->notify(new CancelApplication($leave_app));
            }
            return "Mail sent";
        } catch (\Exception $e) { // Using a generic exception
            return 'Mail not sent';
        }
    }

    public function recordHistory($leave_app, $user_id, $action)
    {

        //Record in activity history
        $hist = new History;
        $hist->leave_application_id = $leave_app->id;
        $hist->user_id = $user_id;
        $hist->action = $action;
        $hist->save();

        return $hist;
    }

    public function getTotalDays($leave_apps)
    {
        $total_days = 0;
        foreach ($leave_apps as $la) {
            $total_days += $la->total_days;
        }
        return $total_days;
    }

    /////////////////////////Function for Replacement Leave/////////////////////

    /**
     * Pass Apply Replacement leave to get Claim replacement leave
     */
    public function getReplacementClaim($apply_leave_app)
    {
        //Get the claim application related to this use replacement application
        $claimApplyRelation = $apply_leave_app->replacement_claim;
        $claimApp = $claimApplyRelation->claim;
        return $claimApp;
    }

    /**
     * Pass Claim Replacement leave to get all Apply Replacement leave
     */
    public function getReplacementApplications($claim_leave_app)
    {
        $claimApplyRelations = $claim_leave_app->replacement_applications;
        $applyApps = collect();
        foreach ($claimApplyRelations as $aca) {
            $leaveApp = $aca->application;
            if ($leaveApp->status == 'PENDING_1' || $leaveApp->status == 'PENDING_2' || $leaveApp->status == 'PENDING_3' || $leaveApp->status == 'APPROVED') {
                $applyApps->push($leaveApp);
            }
        }
        return $applyApps;
    }

    public function getStatusDesc($leave_application)
    {

        if ($leave_application->status == "PENDING_1") {
            $status = PENDING_MSG . $leave_application->approver_one->name;
        } else if ($leave_application->status == "PENDING_2") {
            $status = PENDING_MSG . $leave_application->approver_two->name;
        } else if ($leave_application->status == "PENDING_3") {
            $status = PENDING_MSG . $leave_application->approver_three->name;
        } else if ($leave_application->status == "DENIED_1") {
            $status = DENIED_MSG . $leave_application->approver_one->name;
        } else if ($leave_application->status == "DENIED_2") {
            $status = DENIED_MSG . $leave_application->approver_two->name;
        } else if ($leave_application->status == "DENIED_3") {
            $status = DENIED_MSG . $leave_application->approver_three->name;
        } else if ($leave_application->status == "CANCELLED") {
            $status = "Leave Application Has Been Cancelled";
        } else if ($leave_application->status == "TAKEN") {
            $status = "This Replacement Leave Claim Has Been Fully Used";
        } else if ($leave_application->status == "EXPIRED") {
            $status = "This Replacement Leave Claim Has Expired";
        } else {
            $status = "Leave Application Has Been Approved";
        }
        return $status;
    }

    public function setTaken($leave_app)
    {
        $leave_app->status = "TAKEN";
        $leave_app->save();
        return $leave_app;
    }

    public function isClaim($leave_app)
    {
        if ($leave_app->remarks == "Claim") {
            return true;
        }
        return false;
    }

    public function isApply($leave_app)
    {
        if ($leave_app->remarks == "Apply") {
            return true;
        }
        return false;
    }

    public function isAuthority($user)
    {
        if ($user->user_type == "Authority" || $user->user_type == "Management" || $user->user_type == "Admin") {
            return true;
        }
        return false;
    }

    /**
     * To check if user has enough leave balance for certain leave type
     */
    public function isBalanceEnough($user_id, $leave_type_id, $no_of_days)
    {
        $leaveBal = LeaveBalance::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        if ($leaveBal->no_of_days < $no_of_days) {
            return false;
        }
        return true;
    }

    public function notifyWspace($leave_app)
    {
        $to_email = "";
        $title = $leave_app->leaveType->name . " Leave Application from " . $leave_app->user->name;
        $dt = new Carbon($leave_app->created_at);
        $dt = $dt->format('l, j F Y, h:i A');
        $subtitle = 'submitted on ' . $dt;
        if ($leave_app->status == 'PENDING_1') {
            $to_email = $leave_app->approver_one->email;
        } else if ($leave_app->status == 'PENDING_2') {
            $to_email = $leave_app->approver_two->email;
        } else if ($leave_app->status == 'PENDING_3') {
            $to_email = $leave_app->approver_three->email;
        }
        // } else if ($leave_app->status == 'APPROVED') {
        //     $to_email = $leave_app->user->email;
        //     $title = $leave_app->leaveType->name . " Leave Application Approved.";
        //     $dt = new Carbon($leave_app->updated_at);
        //     $dt = $dt->format('l, j F Y, h:i A');
        //     $subtitle = "approved on " . $dt;
        // }
        else {
            return [];
        }

        $data = [
            "leave_id" => $leave_app->id,
            "leave_type" => $leave_app->leaveType->name,
            "name" => $leave_app->user->name,
            "email" => $leave_app->user->email,
            "apply_for" => $leave_app->apply_for,
            "date_from" => $leave_app->date_from,
            "date_to" => $leave_app->date_to,
            "total_days" => $leave_app->total_days
        ];
        // return response()->json($data);
        $client = new Client();
        //http://128.199.123.181
        //http://128.199.123.181
        $response = $client->request('POST', 'http://128.199.123.181/api/v1/send-message', [
            'headers' =>
            [
                'Authorization' => "Bearer ".config('wspace.els_secret')
            ],
            'form_params' => [
                'to_email' => $to_email,
                'title' => $title,
                'subtitle' => $subtitle,
                'body' => json_encode($data)
            ]
        ]);
        return $response;
    }

    public function is_pending_at_user($leave_app_id, $approver_email)
    {
        //$user = User::where('email',$approver_email)->firstOrFail();
        try {
            $leave_app = LeaveApplication::where('id', $leave_app_id)->firstOrFail();
            if ($leave_app->status == "PENDING_1" && $leave_app->approver_one->email == $approver_email) {
                return true;
            } else if ($leave_app->status == "PENDING_2" && $leave_app->approver_two->email == $approver_email) {
                return true;
            } else if ($leave_app->status == "PENDING_3" && $leave_app->approver_three->email == $approver_email) {
                return true;
            }
            return false;
        } catch (\Exception $e) { // Using a generic exception
            return 'Leave app not found';
        }
    }
}

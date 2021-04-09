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

define("PENDING_MSG", "Pending approval by ");

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

    public function getLeaveTaken($user_id, $leave_type_id){
        $leaveTaken = TakenLeave::where('user_id', $user_id)->where('leave_type_id',$leave_type_id)->first();
        return $leaveTaken;
    }

    public function getLeaveEarning($user_id, $leave_type_id){
        $leaveEarning = LeaveEarning::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        return $leaveEarning;
    }

    /**
     * To set leave balance for certain user
     */
    public function setLeaveBalance($user_id, $leave_type_id, $no_of_days, $operation)
    {

        $leaveBal = LeaveBalance::where('user_id', $user_id)->where('leave_type_id', $leave_type_id)->first();
        if ($operation == "Add") {
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
        if ($operation == "Add") {
            $leaveTaken->no_of_days += $no_of_days;
        } else {
            $leaveTaken->no_of_days -= $no_of_days;
        }
        $leaveTaken->update();

        return $leaveTaken;
    }

    public function setLeaveEarning($user_id, $leave_type_id, $no_of_days, $operation)
    {

        $leaveEarning = LeaveEarning::where('uesr_id', $user_id)->where('leave_type_id',$leave_type_id)->first();
        if($operation == "Add"){
            $leaveEarning->no_of_days += $no_of_days;
        } else{
            $leaveEarning->no_of_days -= $no_of_days;
        }
        $leaveEarning->update();

        return $leaveEarning;

    }

    /**
     * To change leave application status based on approve or deny action
     */
    public function approveOrDeny($leave_app_id, $approver_id, $operation)
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
                    $this->notifyAuthority($leave_app, 'APPROVER_TWO');
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
                    $this->notifyAuthority($leave_app, 'APPROVER_THREE');
                }
            } else if ($approver_id == $la_3 && $leave_app->status == 'PENDING_3'){
                $approver_name = $leave_app->approver_three->name;
                $leave_app->status = 'APPROVED';
            }
            else{
                return $leave_app;
            }

            $action = "Approved by ".$approver_name;
            $this->recordHistory($leave_app,$approver_id,$action);

        } else if ($operation == "DENY") {
            if ($approver_id == $la_1) {
                $approver_name = $leave_app->approver_one->name;
                $leave_app->status = 'DENIED_1';
            } else if ($approver_id == $la_2) {
                $approver_name = $leave_app->approver_two->name;
                $leave_app->status = 'DENIED_2';
            } else {
                $approver_name = $leave_app->approver_three->name;
                $leave_app->status = 'DENIED_3';
            }

            $action = "Denied by ".$approver_name;
            $this->recordHistory($leave_app,$approver_id,$action);

        } else if ($operation == "CANCEL") {
            $this->recordHistory($leave_app,$approver_id,"Cancelled");
            $leave_app->status = 'CANCELLED';
        }
        $leave_app->update();
        $this->notifyUser($leave_app, $leave_app->user);

        return $leave_app;
    }

    /**
     * To notify approver authority
     */
    public function notifyAuthority($leave_app, $to_notify)
    {
        try {
            if ($to_notify == "APPROVER_ONE") {
                $leave_app->approver_one->notify(new NewApplication($leave_app));
            } else if ($to_notify == "APPROVER_TWO") {
                $leave_app->approver_two->notify(new NewApplication($leave_app));
            } else {
                $leave_app->approver_three->notify(new NewApplication($leave_app));
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

    public function getPendingAt($leave_application){

        if ($leave_application->status == "PENDING_1") {
            $status = PENDING_MSG . $leave_application->approver_one->name;
        } else if ($leave_application->status == "PENDING_2") {
            $status = PENDING_MSG . $leave_application->approver_two->name;
        } else if($leave_application->status == "PENDING_3") {
            $status = PENDING_MSG . $leave_application->approver_three->name;
        }
        else{
            $status = 'Approved';
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

    public function isAuthority($user){
        if($user->user_type == "Authority" || $user->user_type == "Management" || $user->user_type == "Admin"){
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
}

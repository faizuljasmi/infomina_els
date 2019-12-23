<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BroughtForwardLeave;
use App\BurntLeave;
use App\EmpType;
use App\LeaveApplication;
use App\LeaveBalance;
use App\LeaveEarning;
use App\LeaveEntitlement;
use App\LeaveType;
use App\TakenLeave;
use App\User;

class LeaveController extends Controller
{
    public function setEarnings(Request $request, User $user){

        //dd($request->all());
        $input = $request->all();

         //Loop thru each of it
         foreach ($input as $key=>$val) {
            
            //To eliminate first entry which is token__
            if(strpos($key,'leave_') === false){
                continue;
            }
            //Trim, only in get the id
            $key = trim($key,"leave_");

            //Check for duplicate leave earning
            // $dupcheck = LeaveEarning::where('leave_type_id', '=', (int)$key, 'AND', 'user_id', '=', $user->id)->first();
            $dupcheck = LeaveEarning::orderBy('leave_type_id','ASC')->where(function ($query) use ($user , $key) {
                $query->where('leave_type_id', (int)$key)
                    ->where('user_id', $user->id);
            })->first();
            //Check for duplicate leave balance
            $lbCheck = LeaveBalance::orderBy('leave_type_id','ASC')->where(function ($query) use ($user , $key) {
                $query->where('leave_type_id', (int)$key)
                    ->where('user_id', $user->id);
            })->first();
        

            //If there is no duplicate,save as new one
            if($dupcheck == null){
                $le = new LeaveEarning;
                $le->user_id = $user->id;
                $le->leave_type_id = (int)$key;
                $le->no_of_days = (int)$val;
                $le->save();

                $lt = new TakenLeave;
                $lt->user_id = $user->id;
                $lt->leave_type_id = (int)$key;
                $lt->no_of_days = 0;
                $lt->save();

                $bf = new BroughtForwardLeave;
                $bf->user_id = $user->id;
                $bf->leave_type_id = (int)$key;
                $bf->no_of_days = 0;
                $bf->save();

                //Add earning to balance
                if($lbCheck == null){
                    $lb = new LeaveBalance;
                    $lb->user_id = $user->id;
                    $lb->leave_type_id = (int)$key;
                    $lb->no_of_days = (int)$val;
                    $lb->save();
                }
                //If got existing balance, just update.
                else{
                    $lbCheck->no_of_days += (int)$val;
                    $lbCheck->save();
                }
            }
            //If not, update.
            else{
                $dupcheck->no_of_days = (int)$val;
                $dupcheck->save();
            }

        }

        //update balance
        
        return back()->with('message','Nice, earnings updated');
    }

    public function setBroughtForward(Request $request, User $user){

         //dd($request->all());
         $input = $request->all();

         //Loop thru each of it
         foreach ($input as $key=>$val) {
            
            //To eliminate first entry which is token__
            if(strpos($key,'leave_') === false){
                continue;
            }
            //Trim, only in get the id
            $key = trim($key,"leave_");

            //Check for duplicate
            $dupcheck = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where(function ($query) use ($user , $key) {
                $query->where('leave_type_id', (int)$key)
                    ->where('user_id', $user->id);
            })->first();
            //Check leave earning for similar leave type
            $leCheck = LeaveEarning::orderBy('leave_type_id','ASC')->where(function ($query) use ($user , $key) {
                $query->where('leave_type_id', (int)$key)
                    ->where('user_id', $user->id);
            })->first();
            //Check leave balance of similar leave type
            $lbCheck = LeaveBalance::orderBy('leave_type_id','ASC')->where(function ($query) use ($user , $key) {
                $query->where('leave_type_id', (int)$key)
                    ->where('user_id', $user->id);
            })->first();

            //If there is no duplicate,save as new one
            if($dupcheck == null){
                $bf = new BroughtForwardLeave;
                $bf->user_id = $user->id;
                $bf->leave_type_id = (int)$key;
                $bf->no_of_days = (int)$val;
                $bf->save();

                //Add brought forward to leave earning
                if($leCheck == null){
                    $le = new LeaveEarning;
                    $le->user_id = $user->id;
                    $le->leave_type_id = (int)$key;
                    $le->no_of_days = (int)$val;
                    $le->save();

                    $lb = new LeaveBalance;
                    $lb->user_id = $user->id;
                    $lb->leave_type_id = (int)$key;
                    $lb->no_of_days = (int)$val;
                    $lb->save();
                }
                //If got existing earning, just update.
                else{
                    $leCheck->no_of_days += (int)$val;
                    $lbCheck->no_of_days = $leCheck->no_of_days;
                    $leCheck->save();
                    $lbCheck->save();
                }
            }
            //If got existing broughtforward, update existing
            else{
                
                //Add brought forward to leave earning
                if($leCheck == null){
                    $le = new LeaveEarning;
                    $le->user_id = $user->id;
                    $le->leave_type_id = (int)$key;
                    $le->no_of_days = (int)$val;
                    $le->save();

                    $lb = new LeaveBalance;
                    $lb->user_id = $user->id;
                    $lb->leave_type_id = (int)$key;
                    $lb->no_of_days = (int)$val;
                    $lb->save();
                }
                //If got existing earning, just update.
                else{
                    //If the new value is less than old value
                    if($dupcheck->no_of_days > (int)$val){
                        //Minus the diff from the earning
                        $diff = $dupcheck->no_of_days - (int)$val;
                        $leCheck->no_of_days -= $diff;
                        $lbCheck->no_of_days = $leCheck->no_of_days;
                    }
                    else{
                        $diff = (int)$val - $dupcheck->no_of_days;
                        $leCheck->no_of_days += $diff;
                        $lbCheck->no_of_days = $leCheck->no_of_days;
                    }
                    $leCheck->save();
                    $lbCheck->save();
                }
                $dupcheck->no_of_days = (int)$val;
                $dupcheck->save();
            }

        }
        return back()->with('message','Nice, brought forward updated');
    }
}

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

            //Check for duplicate
            $dupcheck = LeaveEarning::where('leave_type_id', '=', (int)$key, 'AND', 'user_id', '=', $user->id)->first();

            //If there is no duplicate,save as new one
            if($dupcheck == null){
                $le = new LeaveEarning;
                $le->user_id = $user->id;
                $le->leave_type_id = (int)$key;
                $le->no_of_days = (int)$val;
                $le->save();
            }
            //If not, update.
            else{
                $dupcheck->no_of_days = (int)$val;
                $dupcheck->save();
            }

        }
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
            $dupcheck = BroughtForwardLeave::where('leave_type_id', '=', (int)$key, 'AND', 'user_id', '=', $user->id)->first();

            //If there is no duplicate,save as new one
            if($dupcheck == null){
                $bf = new BroughtForwardLeave;
                $bf->user_id = $user->id;
                $bf->leave_type_id = (int)$key;
                $bf->no_of_days = (int)$val;
                $bf->save();
            }
            //If not, update.
            else{
                $dupcheck->no_of_days = (int)$val;
                $dupcheck->save();
            }

        }
        return back()->with('message','Nice, brought forward updated');
    }
}

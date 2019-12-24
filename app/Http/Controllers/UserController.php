<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\EmpType;
use App\EmpGroup;
use App\LeaveType;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\BroughtForwardLeave;
use App\TakenLeave;


class UserController extends Controller
{
    
    public function index(){

        //Get current logged in user
        $user = auth()->user();
        //Get his employee type
        $empType = $user->emp_types;
        //Get his employee group
        $empGroup = $user->emp_group;
        //Get his approval authority
        $empAuth = $user->approval_authority;
        //dd($empAuth->getAuthorityOneAttribute);

        //Get his leave entitlements
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveEarn = LeaveEarning::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //Get all leave types, for display
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.employee.profile')->with(compact('user','empType','empGroup','empAuth','leaveEnt','leaveTypes','leaveEnt','leaveEarn','broughtFwd','leaveBal','leaveTak'));
    }

    public function edit(){
        
        $user = auth()->user();
        $empType = $user->emp_types;
        $empGroup = $user->emp_group;
        $empAuth = $user->approval_authority;
        //dd($empAuth);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveEarn = LeaveEarning::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.employee.edit')->with(compact('user','empType','empGroup','empAuth','leaveTypes','leaveEnt','leaveEarn','broughtFwd','leaveBal','leaveTak'));
    }

    public function update(Request $request){
        //dd($request->emp_group_id);
        $user = auth()->user();
        $user->update($request->only('name','email','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title','emergency_contact_name','emergency_contact_no'));
        return redirect()->route('view_profile')->with('message', 'Your profile has been updated succesfully');

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmpType;
use App\EmpGroup;
use App\LeaveType;
use App\LeaveEntitlement;


class UserController extends Controller
{
    //
    public function index(){
        $user = auth()->user();
        $empType = $user->emp_types;
        $empGroup = $user->emp_group;
        $empAuth = $user->approval_authority;
        //dd($empAuth->getAuthorityOneAttribute);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.employee.profile')->with(compact('user','empType','empGroup','empAuth','leaveEnt','leaveTypes'));
    }

    public function edit(){
        
        $user = auth()->user();
        $empType = $user->emp_types;
        
        $empGroup = $user->emp_group;

        $empAuth = $user->approval_authority;
        //dd($empAuth);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.employee.edit')->with(compact('user','empType','empGroup','empAuth','leaveTypes','leaveEnt'));
    }

    public function update(Request $request){
        //dd($request->emp_group_id);
        $user = auth()->user();
        $user->update($request->only('name','email','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title','emergency_contact_name','emergency_contact_no'));
        return redirect()->route('view_profile')->with('message', 'Your profile updated succesfully');

    }
}

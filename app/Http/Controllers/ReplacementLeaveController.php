<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveType;
use App\User;
use App\LeaveBalance;

class ReplacementLeaveController extends Controller
{
    public function create(){

        //Get all leave type =  replacement
        $leaveType = LeaveType::where('name','Replacement')->get();

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities for this user
        $leaveAuth = User::orderBy('id','ASC')->where('user_type', '=', 'Admin')->get()->except($user->id);

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();

        return view('leaveapp.replacement')->with(compact('user','leaveType', 'groupMates','leaveAuth','leaveBal'));
    }

    public function store(){

    }

    public function edit(){

    }

    public function update(){

    }

    public function approve(){

    }

    public function deny(){

    }
}

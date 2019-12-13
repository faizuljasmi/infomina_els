<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveType;
use App\User;

class LeaveApplicationController extends Controller
{
    //Create New Application
    public function create(){

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id','ASC')->get();

        //Get THIS user id
        $user = auth()->user();

        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities of THIS user
        //ONLY CAN GET ID, HOW TO GET NAME?

        //TODO: Get leave balance of THIS employee

        return view('leaveapp.create')->with(compact('leaveType', 'groupMates'));
    }


    //Store Application
    public function store(Request $request){

    }

    public function edit(LeaveApplication $leaveApplication){

    }

    public function update(Request $request, LeaveApplication $leaveApplication){

    }

    public function approve(){
        $leaveType = LeaveType::orderBy('id','ASC')->get();
        $employees = User::orderBy('id','ASC')->get();

        return view('leaveapp.approve')->with(compact('leaveType', 'employees'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeaveType;
use App\User;

class LeaveApplicationController extends Controller
{
    public function create(){
        $leaveType = LeaveType::orderBy('id','ASC')->get();
        $employees = User::orderBy('id','ASC')->get();
        //dd($leaveType);
        return view('leaveapp.create')->with(compact('leaveType', 'employees'));
    }

    public function approve(){
        $leaveType = LeaveType::orderBy('id','ASC')->get();
        $employees = User::orderBy('id','ASC')->get();

        return view('leaveapp.approve')->with(compact('leaveType', 'employees'));
    }
}

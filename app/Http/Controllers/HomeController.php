<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmpType;
use App\LeaveType;
use App\LeaveApplication;

class HomeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Show application dashboard
     */

    public function index(){
        //Get current user who is logged in
        $user = auth()->user();
        $app = LeaveApplication::find(1);
        dd($app->approver_one->toArray());
        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        //dd($emptype->name);
        return view('home')->with(compact('user','emptype','empTypes','leaveTypes'));
    }

    /**
     * Show admin dashboard
     */

    public function admin(){
        //Get current user who is logged in
        $user = auth()->user();
        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        //dd($emptype->name);
        return view('admin')->with(compact('user','emptype','empTypes','leaveTypes'));
    }
}

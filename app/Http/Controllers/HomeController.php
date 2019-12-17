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
        $emptype = $user->emp_types;
        $empTypes = EmpType::orderBy('id', 'ASC')->get();
        $leaveTypes = LeaveType::orderBy('id', 'ASC')->get();
        //$leaveApps = $user->leave_applications;
        $leaveApps = LeaveApplication::orderBy('created_at','DESC')->where('user_id', '=', $user->id)->paginate(3);
        //dd($leaveApps);
        return view('home')->with(compact('user','emptype','empTypes','leaveTypes','leaveApps'));
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

    public function getProfile() {
        $user = Auth::user();
        return view("admin_panel.info_views.users.user_profile")->with("user", $user);
      }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\NewApplication;
use App\User;
use App\EmpType;
use App\LeaveType;
use App\LeaveApplication;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\BroughtForwardLeave;
use App\LeaveBalance;
use App\TakenLeave;
use App\Holiday;
use Carbon\Carbon;

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
        $leaveEnts = LeaveEntitlement::orderBy('leave_type_id','ASC')->where('emp_type_id','=',$user->emp_type_id)->get();
        //dd($leaveEnts);
        $leaveEarns = LeaveEarning::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //$leaveApps = $user->leave_applications;
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //dd($user->taken_leaves()->where('leave_type_id',12)->get('no_of_days'));
        $leaveTak = TakenLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveApps = LeaveApplication::orderBy('created_at','DESC')->where('user_id', '=', $user->id)->paginate(3);

        //Get all holidays dates
        $holidays = Holiday::all();
        $all_dates = array();
        foreach($holidays as $hols){
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            $all_dates = [];
            while ($startDate->lte($endDate)){
                $dates = str_replace("-","",$startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }

        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        foreach($leaveApps as $la){
            $startDate = new Carbon($la->date_from);
            $endDate = new Carbon ($la->date_to);
            $applied_dates = [];
            $approved_dates = [];
            if($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3'){
                while ($startDate->lte($endDate)){
                    $dates = str_replace("-","",$startDate->toDateString());
                    $applied_dates[] = $dates;
                    $startDate->addDay();
                }
            }
            if($la->status == 'APPROVED'){
                while ($startDate->lte($endDate)){
                    $dates = str_replace("-","",$startDate->toDateString());
                    $approved_dates[] = $dates;
                    $startDate->addDay();
                }
            }
        }
        //dd($approved_dates);

        return view('home')->with(compact('user','emptype','empTypes','leaveTypes','leaveApps', 'leaveEnts','leaveEarns','broughtFwd','leaveBal','leaveTak','all_dates','applied_dates','approved_dates'));
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
        //Mantop ni. Only get leave applications that are currently waiting for THIS authority to approve, yang lain tak tarik.
        $leaveApps = LeaveApplication::orderBy('created_at','DESC')->where(function ($query) use ($user) {
            $query->where('status', 'PENDING_1')
                ->where('approver_id_1', $user->id);
        })->orWhere(function($query) use ($user){
            $query->where('status', 'PENDING_2')
                ->where('approver_id_2', $user->id);	
        })->orWhere(function($query)use ($user) {
            $query->where('status', 'PENDING_3')
                ->where('approver_id_3', $user->id);
        })->simplePaginate(3);

        $leaveHist = LeaveApplication::orderBy('created_at','DESC')->where(function ($query) use ($user) {
            $query->where('status', 'APPROVED')
                ->where('approver_id_1', $user->id);
        })->orWhere(function($query) use ($user){
            $query->where('status', 'APPROVED')
                ->where('approver_id_2', $user->id);	
        })->orWhere(function($query)use ($user) {
            $query->where('status', 'APPROVED')
                ->where('approver_id_3', $user->id);
        })->simplePaginate(5);

        $holidays = Holiday::all();
        $all_dates = array();
        foreach($holidays as $hols){
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            $all_dates = [];
            while ($startDate->lte($endDate)){
                $dates = str_replace("-","",$startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }
        
        
        //dd($leaveApps);
        return view('admin')->with(compact('user','emptype','empTypes','leaveTypes','leaveApps','leaveHist','all_dates'));
    }

    public function getProfile() {
        $user = Auth::user();
        return view("admin_panel.info_views.users.user_profile")->with("user", $user);
      }
}

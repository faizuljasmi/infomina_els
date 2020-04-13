<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:02:58
 * @modify date 2020-01-07 09:02:58
 * @desc [description]
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\NewApplication;
use App\Notifications\StatusUpdate;
use App\Notifications\CancelApplication;
use App\LeaveType;
use App\User;
use App\ApprovalAuthority;
use App\LeaveApplication;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;
use App\Holiday;
use App\EmpGroup;
use App\History;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    //Create New Application
    public function create()
    {

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get()->except('leave_type_id', '=', '12');

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();
	//dd($groupMates->unique()->values()->all());

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        if ($leaveAuth == null) {
            return redirect('home')->with('error', 'Your approval authorities have not been set yet by the HR Admin. Please contact the HR Admin.');
        }

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        //Get all leave applications
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get();


        //Get leave applications of same group
        $groupLeaveApps = collect([]);
        foreach ($leaveApps as $la) {
            $groupIndex = ["_", "_two_", "_three_", "_four_", "_five_"];

            $isUserLaGroupSameUserGroup = false;
            foreach ($groupIndex as $gI_1) {
                foreach ($groupIndex as $gI_2) {
                    $gLa = $la->user["emp_group" . $gI_1 . "id"];
                    $gUser = $user["emp_group" . $gI_2 . "id"];

                    if ($gUser != "" && $gUser != null && $gLa != "" && $gLa != null) {
                        if ($gLa == $gUser) {
                            $isUserLaGroupSameUserGroup = true;
                            break;
                        }
                    }
                }
            }
            if ($isUserLaGroupSameUserGroup && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')
            && ($la->user_id != $user->id)) {
                $groupLeaveApps->add($la);
            }
        }
        //Get my applications
        $myApps = collect([]);
        foreach ($leaveApps as $la) {
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $myApps->add($la);
            }
        }

        //Group user's applications by months. Starting from the start of current month until end of year
        $myApps = $myApps->whereBetween('date_from',array(now()->startOfMonth()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });

        //Group user's group applications by months. Starting from the start of the week until end of year.
        $groupLeaveApps = $groupLeaveApps->whereBetween('date_from',array(now()->startOfWeek()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });


        $holidays = Holiday::all();
        $holsPaginated = Holiday::orderBy('date_from', 'ASC')->get()->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });

      //dd($holsPaginated);
        $all_dates = array();
        foreach ($holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }

        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        $myApplication = array();
        foreach ($leaveApps as $la) {
            //Get the user applied and approved application
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $stardDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);

                while ($stardDate->lte($endDate)) {
                    $dates = str_replace("-", "", $stardDate->toDateString());
                    $myApplication[] = $dates;
                    $stardDate->addDay();
                }
            }
            if ($la->user->emp_group_id == $user->emp_group_id) {
                $startDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);
                if ($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3') {
                    while ($startDate->lte($endDate)) {
                        $dates = str_replace("-", "", $startDate->toDateString());
                        $applied_dates[] = $dates;
                        $startDate->addDay();
                    }
                }
                if ($la->status == 'APPROVED') {
                    while ($startDate->lte($endDate)) {
                        $dates = str_replace("-", "", $startDate->toDateString());
                        $approved_dates[] = $dates;
                        $startDate->addDay();
                    }
                }
            }
        }

        return view('leaveapp.create')->with(compact('user', 'leaveType', 'groupMates', 'leaveAuth', 'leaveBal', 'all_dates', 'applied_dates', 'approved_dates', 'myApplication', 'holidays', 'groupLeaveApps', 'holsPaginated', 'myApps'));
    }


    //Store Application
    public function store(Request $request)
    {

        $request->flash();
        //dd($request->emergency_contact_no);
        //Get user id
        $user = auth()->user();
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        //If insufficient balance
        if ($leaveBal == null || $request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return redirect()->to('/leave/apply')->with('error', 'Your have insufficient leave balance. Please contact HR for more info.');
        }

        //Check leave authority

        $appCheck = LeaveApplication::where('user_id', $user->id)->get();
        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        foreach ($appCheck as $la) {
            $startDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);
            if ($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3') {
                while ($startDate->lte($endDate)) {
                    $dates = str_replace("-", "", $startDate->toDateString());
                    $applied_dates[] = $dates;
                    $startDate->addDay();
                }
            }
            if ($la->status == 'APPROVED') {
                while ($startDate->lte($endDate)) {
                    $dates = str_replace("-", "", $startDate->toDateString());
                    $approved_dates[] = $dates;
                    $startDate->addDay();
                }
            }
        }

        $leaveApp = new LeaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id

        //If it is replacement leave claim
        if ($request->leave_type_id == '12') {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
        }



        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;
        //get total days
        $leaveApp->total_days = $request->total_days;
        //get apply for
        $leaveApp->apply_for = $request->apply_for;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;


        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|required_if:leave_type_id,4|required_if:leave_type_id,8|required_if:leave_type_id,9|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return redirect()->to('/leave/apply')->with('error', 'Your file attachment is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save attachment filenam into leave application table
            $leaveApp->attachment = $filename;
        }


        $leaveApp->save();
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        $leaveApp->approver_one->notify(new NewApplication($leaveApp));

        //STORE
        return redirect()->to('/home')->with('message', 'Leave application submitted succesfully');
    }

    public function edit(LeaveApplication $leaveApplication)
    {
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get();

        //Get THIS user id
        $user = $leaveApplication->user;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        //Get all authorities
        $userAuth = User::orderBy('id', 'ASC')->where('id', '!=', '1')->where('user_type', 'Authority')->get()->except($user->id);
        //Get approval authorities for this user
        //Change id to CYNTHIA'S ID
        $leaveAuthReplacement = User::orderBy('id', 'ASC')->where('id', '!=', '4')->get()->except($user->id);


        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        //Get leave applications from same group
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->get()->except($leaveApplication->id);

        $holidays = Holiday::all();
        $all_dates = array();
        foreach ($holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }

        //Get all leave applications date
        $applied_dates = array();
        $approved_dates = array();
        $myApplication = array();
        foreach ($leaveApps as $la) {
             //Get the user applied and approved application
             if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $stardDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);

                while ($stardDate->lte($endDate)) {
                    $dates = str_replace("-", "", $stardDate->toDateString());
                    $myApplication[] = $dates;
                    $stardDate->addDay();
                }
            }
            if ($la->user->emp_group_id == $user->emp_group_id) {
                $startDate = new Carbon($la->date_from);
                $endDate = new Carbon($la->date_to);
                if ($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3') {
                    while ($startDate->lte($endDate)) {
                        $dates = str_replace("-", "", $startDate->toDateString());
                        $applied_dates[] = $dates;
                        $startDate->addDay();
                    }
                }
                if ($la->status == 'APPROVED') {
                    while ($startDate->lte($endDate)) {
                        $dates = str_replace("-", "", $startDate->toDateString());
                        $approved_dates[] = $dates;
                        $startDate->addDay();
                    }
                }
            }
        }

        //dd($leaveApplication->approver_id_1);
        return view('leaveapp.edit')->with(compact('leaveApplication', 'user', 'leaveType', 'groupMates', 'userAuth', 'leaveAuth', 'leaveBal', 'all_dates', 'applied_dates', 'approved_dates', 'leaveAuthReplacement','myApplication'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        //dd($request->emergency_contact_no);
        //Get user id
        $user = $leaveApplication->user;
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        $leaveTaken = TakenLeave::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();
        //dd($leaveBal->no_of_days);
        if ($request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return redirect()->to('/leave/apply')->with('error', 'Your have insufficient leave balance. Please contact HR for more info.');
        }

        $leaveApp = $leaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id
        //If it is replacement leave claim
        if ($request->leave_type_id == '12') {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
        }


        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;

        //get initial total days
        $initial_days = $leaveApp->total_days;
        //If the new set date is more than initial date
        if($initial_days < $request->total_days && $leaveApp->status == "APPROVED"){
            $day_dif = $request->total_days - $initial_days;
            //Minus the day diff to the balance
            $leaveBal->no_of_days -= $day_dif;
            $leaveBal->save();

            //Add the day diff to the taken leave
            $leaveTaken->no_of_days += $day_dif;
            $leaveTaken->save();
        }
        //Else if
        else if($initial_days > $request->total_days && $leaveApp->status == "APPROVED"){
            $day_dif = $initial_days - $request->total_days;

            //Add the day diff to the balance
            $leaveBal->no_of_days += $day_dif;
            $leaveBal->save();

            //Minus the day diff to taken leave
            $leaveTaken->no_of_days -= $day_dif;
            $leaveTaken->save();
        }
        $leaveApp->total_days = $request->total_days;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;

        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return redirect()->to('/leave/apply')->with('error', 'Your file attachment format is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        // if ($request->hasFile('attachment')) {
        //     $att = $request->file('attachment');
        //     $uploaded_file = $att->store('public');
        //     //Pecahkan
        //     $paths = explode('/', $uploaded_file);
        //     $filename = $paths[1];
        //     //dd($uploaded_file);
        //     //Save attachment filenam into leave application table
        //     $leaveApp->attachment = $filename;
        // }

        //Upload image
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save filename into Database
            $leaveApp->update(['attachment' => $filename]);
        }


        $leaveApp->save();
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        if($leaveApp->status == 'PENDING_1'){
            $leaveApp->approver_one->notify(new NewApplication($leaveApp));
        }

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApplication->id;
         $hist->user_id = auth()->user()->id;
         $hist->action = "Edited";
         $hist->save();

        return redirect()->to('/home')->with('message', 'Leave application edited succesfully');
    }

    public function approve(LeaveApplication $leaveApplication)
    {

        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        //If user id same as approver id 1
        if ($la_1 == $user->id) {
            //if no authority 2, terus change to approved
            if ($la_2 == null) {
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 2,
            else {
                $leaveApplication->status = 'PENDING_2';

                //Notify the second approver
                $leaveApplication->approver_two->notify(new NewApplication($leaveApplication));
            }
        }
        //if user id same as approved id 2
        else if ($la_2 == $user->id) {
            //if no authority 3, terus change to approved
            if ($la_3 == null) {
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 3
            else {
                $leaveApplication->status = 'PENDING_3';
                //Notify the third approver
                $leaveApplication->approver_three->notify(new NewApplication($leaveApplication));
            }
        }
        //If user id same as approved id 3, update status to approved
        else {
            $leaveApplication->status = 'APPROVED';
        }
        $leaveApplication->update();

        //If the application is approved
        if ($leaveApplication->status == 'APPROVED') {

            //If the approved leave is a Replacement leave, assign earned to Replacement, and add day balance to Annual
            if ($leaveApplication->leaveType->name == 'Replacement') {
                $lt = LeaveEarning::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();

                $lt->no_of_days += $leaveApplication->total_days;

                $lt->save();

                //Add balance to annual;
                $lb = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '1')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();

                $lb->no_of_days += $leaveApplication->total_days;
                $lb->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Replacement leave application status updated succesfully');
            }

            //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
            if ($leaveApplication->leaveType->name == 'Sick') {
                //Add in amount sick leave taken
                $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in sick leave balance
                $sickBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '3')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $sickBalance->no_of_days -= $leaveApplication->total_days;
                $sickBalance->save();

                //Deduct balance in hosp leave balance
                $hospBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '4')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $hospBalance->no_of_days -= $leaveApplication->total_days;
                $hospBalance->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Sick leave application status updated succesfully');
            }

            //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
            if ($leaveApplication->leaveType->name == 'Emergency') {
                //Add in amount emergency leave taken
                $lt = TakenLeave::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in emergency leave balance
                $emBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '6')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $emBalance->no_of_days -= $leaveApplication->total_days;
                $emBalance->save();

                //Deduct balance in annual leave
                $annBalance = LeaveBalance::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', '1')
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $annBalance->no_of_days -= $leaveApplication->total_days;
                $annBalance->save();
                //dd($annBalance->no_of_days);

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message', 'Emergency leave application status updated succesfully');
            }

            //Update leave taken table
            //Check for existing record
            $dupcheck = TakenLeave::where(function ($query) use ($leaveApplication) {
                $query->where('leave_type_id', $leaveApplication->leave_type_id)
                    ->where('user_id', $leaveApplication->user_id);
            })->first();

            //If does not exist, create new
            if ($dupcheck == null) {
                $tl = new TakenLeave;
                $tl->leave_type_id = $leaveApplication->leave_type_id;
                $tl->user_id = $leaveApplication->user_id;
                $tl->no_of_days = $leaveApplication->total_days;
                $tl->save();
            }
            //else update existing
            else {
                $dupcheck->no_of_days += $leaveApplication->total_days;
                $dupcheck->save();
            }

            //Update leave balance table
            //Check for existing record
            $dupcheck2 = LeaveBalance::where(function ($query) use ($leaveApplication) {
                $query->where('leave_type_id', $leaveApplication->leave_type_id)
                    ->where('user_id', $leaveApplication->user_id);
            })->first();

            //If does not exist, create new
            if ($dupcheck2 == null) {
                $lb = new LeaveBalance;
                $lb->leave_type_id = $leaveApplication->leave_type_id;
                $lb->user_id = $leaveApplication->user_id;
                $le = LeaveEarning::where(function ($query) use ($leaveApplication) {
                    $query->where('leave_type_id', $leaveApplication->leave_type_id)
                        ->where('user_id', $leaveApplication->user_id);
                })->first();
                $lb->no_of_days = $le->no_of_days - $leaveApplication->total_days;
                $lb->save();
            }
            //else update existing
            else {
                $dupcheck2->no_of_days -= $leaveApplication->total_days;
                $dupcheck2->save();
            }
        }

        //Record in activity history
        $hist = new History;
        $hist->leave_application_id = $leaveApplication->id;
        $hist->user_id = $user->id;
        $hist->action = "Approved";
        $hist->save();



        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));

        return redirect()->to('/admin')->with('message', 'Leave application status updated succesfully');
    }

    public function deny(LeaveApplication $leaveApplication)
    {

        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        //If user id same as approver id 1
        if ($la_1 == $user->id) {
            $leaveApplication->status = 'DENIED_1';
        }
        //if user id same as approved id 2
        else if ($la_2 == $user->id) {
            $leaveApplication->status = 'DENIED_2';
        }
        //If user id same as approved id 3,
        else {
            $leaveApplication->status = 'DENIED_3';
        }
        $leaveApplication->update();

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApplication->id;
         $hist->user_id = $user->id;
         $hist->action = "Denied";
         $hist->save();

        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));


        return redirect()->to('/admin')->with('message', 'Leave application status updated succesfully');
    }

    public function view(LeaveApplication $leaveApplication)
    {
        $leaveApp = $leaveApplication;
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get();
        //Get THIS user id
        $user = $leaveApp->user;
        $leaveAuth = $user->approval_authority;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id', 'ASC')->get()->except($user->id);

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        $applied_dates = array();
        $approved_dates = array();
        $startDate = new Carbon($leaveApp->date_from);
        $endDate = new Carbon($leaveApp->date_to);

        if ($leaveApp->status == 'APPROVED') {
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $approved_dates[] = $dates;
                $startDate->addDay();
            }
        } else {
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $applied_dates[] = $dates;
                $startDate->addDay();
            }
        }

        $holidays = Holiday::all();
        $hol_dates = array();
        foreach ($holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $hol_dates[] = $dates;
                $startDate->addDay();
            }
        }

        return view('leaveapp.view')->with(compact('leaveApp', 'leaveType', 'user', 'leaveAuth', 'groupMates', 'leaveBal', 'applied_dates', 'approved_dates', 'hol_dates'));
    }

    public function cancel(LeaveApplication $leaveApplication, Request $request)
    {
        if($leaveApplication->status == "APPROVED"){
            $days = $leaveApplication->total_days;
            $takenLeave = TakenLeave::where('user_id', $leaveApplication->user_id)->where('leave_type_id', $leaveApplication->leave_type_id)->first();
            $leaveBalance = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', $leaveApplication->leave_type_id)->first();

            $takenLeave->no_of_days -= $days;
            $leaveBalance->no_of_days += $days;
            if($leaveApplication->leave_type_id == "3"){
                $leaveBal2 = LeaveBalance::where('user_id', $leaveApplication->user_id)->where('leave_type_id', 4)->first();
                $leaveBal2->no_of_days += $days;
                $leaveBal2->save();
            }
            $takenLeave->save();
            $leaveBalance->save();
        }
        $leaveApplication->remarks = $request->remarks;
        $leaveApplication->remarker_id = auth()->user()->id;
        $prevStatus = $leaveApplication->status;
        $leaveApplication->status = "CANCELLED";
        $leaveApplication->save();

        if(($leaveApplication->remarker_id == $leaveApplication->approver_id_3)){
            if(($prevStatus == 'PENDING_2')||($prevStatus == 'PENDING_3') || ($prevStatus == 'APPROVED')){
                $leaveApplication->approver_two->notify(new CancelApplication($leaveApplication));
            }
        }



        $leaveApplication->approver_one->notify(new CancelApplication($leaveApplication));
        $when = now()->addMinutes(5);
        $leaveApplication->user->notify((new CancelApplication($leaveApplication))->delay($when));

        return redirect()->to('/home')->with('message', 'Leave application cancelled succesfully');
    }

    public function applyFor(User $user){
        $user = $user;

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id', 'ASC')->get()->except('leave_type_id', '=', '12');

        //Get employees who are in the same group (for relieve personnel).
        $groupMates = collect([]);

        $group1 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_id)->first();
        if (isset($group1)) {
            $groupMates1 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_id)->orWhere('emp_group_two_id', $user->emp_group_id)
            ->orWhere('emp_group_three_id', $user->emp_group_id)->orWhere('emp_group_four_id', $user->emp_group_id)->orWhere('emp_group_five_id', $user->emp_group_id)->get()->except($user->id)->except($group1->group_leader_id);
            $groupMates = $groupMates->merge($groupMates1);
        }

        $group2 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_two_id)->first();
        if (isset($group2)) {
            $groupMates2 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_two_id)->orWhere('emp_group_two_id', $user->emp_group_two_id)
                ->orWhere('emp_group_three_id', $user->emp_group_two_id)->orWhere('emp_group_four_id', $user->emp_group_two_id)->orWhere('emp_group_five_id', $user->emp_group_two_id)->get()->except($user->id)->except($group2->group_leader_id);
            $groupMates = $groupMates->merge($groupMates2);
        }

        $group3 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_three_id)->first();
        if (isset($group3)) {
            $groupMates3 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_three_id)->orWhere('emp_group_two_id', $user->emp_group_three_id)
                ->orWhere('emp_group_three_id', $user->emp_group_three_id)->orWhere('emp_group_four_id', $user->emp_group_three_id)->orWhere('emp_group_five_id', $user->emp_group_three_id)->get()->except($user->id)->except($group3->group_leader_id);
            $groupMates = $groupMates->merge($groupMates3);
        }

        $group4 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_four_id)->first();
        if (isset($group4)) {
            $groupMates4 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_four_id)->orWhere('emp_group_two_id', $user->emp_group_four_id)
                ->orWhere('emp_group_three_id', $user->emp_group_four_id)->orWhere('emp_group_four_id', $user->emp_group_four_id)->orWhere('emp_group_five_id', $user->emp_group_four_id)->get()->except($user->id)->except($group4->group_leader_id);
            $groupMates = $groupMates->merge($groupMates4);
        }

        $group5 = EmpGroup::orderby('id', 'ASC')->where('id', $user->emp_group_five_id)->first();
        if (isset($group5)) {
            $groupMates5 = User::orderBy('id', 'ASC')->where('emp_group_id', $user->emp_group_five_id)->orWhere('emp_group_two_id', $user->emp_group_five_id)
                ->orWhere('emp_group_three_id', $user->emp_group_five_id)->orWhere('emp_group_four_id', $user->emp_group_five_id)->orWhere('emp_group_five_id', $user->emp_group_five_id)->get()->except($user->id)->except($group5->group_leader_id);
            $groupMates = $groupMates->merge($groupMates5);
        }
        $groupMates = $groupMates->unique()->values()->all();
        //dd($groupMates->unique()->values()->all());

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        if ($leaveAuth == null) {
            return redirect('home')->with('error', 'Your approval authorities have not been set yet by the HR Admin. Please contact the HR Admin.');
        }

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id', 'ASC')->where('user_id', '=', $user->id)->get();

        //Get all leave applications
        $leaveApps = LeaveApplication::orderBy('date_from', 'ASC')->where('user_id',$user->id)->get();

        //Get my applications
        $myApps = collect([]);
        foreach ($leaveApps as $la) {
            if ($la->user->id == $user->id && ($la->status == 'APPROVED' || $la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3')) {
                $myApps->add($la);
            }
        }

        //Group user's applications by months. Starting from the start of current month until end of year
        $myApps = $myApps->whereBetween('date_from',array(now()->startOfMonth()->format('Y-m-d'),now()->endOfYear()->format('Y-m-d')))->groupBy(function($val) {
            return Carbon::parse($val->date_from)->format('F');
      });

      //Get all leave applications date
      $applied_dates = array();
      $approved_dates = array();
      $myApplication = array();
      foreach ($leaveApps as $la) {
          //Get the user applied and approved application
          if ($la->status == 'APPROVED') {
              $stardDate = new Carbon($la->date_from);
              $endDate = new Carbon($la->date_to);

              while ($stardDate->lte($endDate)) {
                  $dates = str_replace("-", "", $stardDate->toDateString());
                  $myApplication[] = $dates;
                  $approved_dates[] = $dates;
                  $stardDate->addDay();
              }
          }
          if($la->status == 'PENDING_1' || $la->status == 'PENDING_2' || $la->status == 'PENDING_3'){
            $stardDate = new Carbon($la->date_from);
            $endDate = new Carbon($la->date_to);

            while ($stardDate->lte($endDate)) {
                $dates = str_replace("-", "", $stardDate->toDateString());
                $myApplication[] = $dates;
                $applied_dates = $dates;
                $stardDate->addDay();
            }
          }
      }

        return view('leaveapp.applyfor')->with(compact('user','leaveType','groupMates','leaveAuth','leaveBal','myApps','myApplication','applied_dates','approved_dates'));
    }

    public function submitApplyFor(Request $request, User $user){
        $request->flash();
        //dd($request->emergency_contact_no);
        //Get user id
        $user = $user;
        //Check Balance
        $leaveBal = LeaveBalance::where(function ($query) use ($request, $user) {
            $query->where('leave_type_id', '=', $request->leave_type_id)
                ->where('user_id', '=', $user->id);
        })->first();

        //If insufficient balance
        if ($leaveBal == null || $request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12') {
            return back()->with('error', 'Employee have insufficient leave balance. Please contact HR for more info.');
        }

        //Check leave authority

        $leaveApp = new LeaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id

        //If it is replacement leave claim
        if ($request->leave_type_id == '12') {

            //If there is no second approver, move the last approver to the 2nd one
            if ($request->approver_id_2 == null) {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_3;
                $leaveApp->approver_id_3 = null;
            } else {
                $leaveApp->approver_id_1 = $request->approver_id_1;
                $leaveApp->approver_id_2 = $request->approver_id_2;
                $leaveApp->approver_id_3 = $request->approver_id_3;
            }
        } else {
            $leaveApp->approver_id_1 = $request->approver_id_1;
            $leaveApp->approver_id_2 = $request->approver_id_2;
            $leaveApp->approver_id_3 = $request->approver_id_3;
        }



        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;
        //get total days
        $leaveApp->total_days = $request->total_days;
        //get apply for
        $leaveApp->apply_for = $request->apply_for;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact_name = $request->emergency_contact_name;
        $leaveApp->emergency_contact_no = $request->emergency_contact_no;


        //Attachment validation
        $validator = Validator::make(
            $request->all(),
            ['attachment' => 'required_if:leave_type_id,3|required_if:leave_type_id,7|required_if:leave_type_id,4|required_if:leave_type_id,8|required_if:leave_type_id,9|mimes:jpeg,png,jpg,pdf|max:2048']
        );

        // if validation fails
        if ($validator->fails()) {
            return back()->with('error', 'Your file attachment is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        if ($request->hasFile('attachment')) {
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/', $uploaded_file);
            $filename = $paths[1];
            //dd($uploaded_file);
            //Save attachment filenam into leave application table
            $leaveApp->attachment = $filename;
        }

        $leaveApp->status = "Approved";
        $leaveApp->save();

        //If the approved leave is a Replacement leave, assign earned to Replacement, and add day balance to Annual
        if ($leaveApp->leaveType->name == 'Replacement') {
            $lt = LeaveEarning::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();

            $lt->no_of_days += $leaveApp->total_days;

            $lt->save();

            //Add balance to annual;
            $lb = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '1')
                    ->where('user_id', $leaveApp->user_id);
            })->first();

            $lb->no_of_days += $leaveApp->total_days;
            $lb->save();

            //Send status update email
            return back()->with('message', 'Leave Record Added Succesfully');
        }

        //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
        if ($leaveApp->leaveType->name == 'Sick') {
            //Add in amount sick leave taken
            $lt = TakenLeave::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lt->no_of_days += $leaveApp->total_days;
            $lt->save();

            //Deduct balance in sick leave balance
            $sickBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '3')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $sickBalance->no_of_days -= $leaveApp->total_days;
            $sickBalance->save();

            //Deduct balance in hosp leave balance
            $hospBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '4')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $hospBalance->no_of_days -= $leaveApp->total_days;
            $hospBalance->save();

            return back()->with('message', 'Sick leave application status updated succesfully');
        }

        //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
        if ($leaveApp->leaveType->name == 'Emergency') {
            //Add in amount emergency leave taken
            $lt = TakenLeave::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lt->no_of_days += $leaveApp->total_days;
            $lt->save();

            //Deduct balance in emergency leave balance
            $emBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '6')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $emBalance->no_of_days -= $leaveApp->total_days;
            $emBalance->save();

            //Deduct balance in annual leave
            $annBalance = LeaveBalance::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', '1')
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $annBalance->no_of_days -= $leaveApp->total_days;
            $annBalance->save();
            //dd($annBalance->no_of_days);

            return back()->with('message', 'Emergency leave application status updated succesfully');
        }

        //Update leave taken table
        //Check for existing record
        $dupcheck = TakenLeave::where(function ($query) use ($leaveApp) {
            $query->where('leave_type_id', $leaveApp->leave_type_id)
                ->where('user_id', $leaveApp->user_id);
        })->first();

        //If does not exist, create new
        if ($dupcheck == null) {
            $tl = new TakenLeave;
            $tl->leave_type_id = $leaveApp->leave_type_id;
            $tl->user_id = $leaveApp->user_id;
            $tl->no_of_days = $leaveApp->total_days;
            $tl->save();
        }
        //else update existing
        else {
            $dupcheck->no_of_days += $leaveApp->total_days;
            $dupcheck->save();
        }

        //Update leave balance table
        //Check for existing record
        $dupcheck2 = LeaveBalance::where(function ($query) use ($leaveApp) {
            $query->where('leave_type_id', $leaveApp->leave_type_id)
                ->where('user_id', $leaveApp->user_id);
        })->first();

        //If does not exist, create new
        if ($dupcheck2 == null) {
            $lb = new LeaveBalance;
            $lb->leave_type_id = $leaveApp->leave_type_id;
            $lb->user_id = $leaveApp->user_id;
            $le = LeaveEarning::where(function ($query) use ($leaveApp) {
                $query->where('leave_type_id', $leaveApp->leave_type_id)
                    ->where('user_id', $leaveApp->user_id);
            })->first();
            $lb->no_of_days = $le->no_of_days - $leaveApp->total_days;
            $lb->save();
        }
        //else update existing
        else {
            $dupcheck2->no_of_days -= $leaveApp->total_days;
            $dupcheck2->save();
        }

         //Record in activity history
         $hist = new History;
         $hist->leave_application_id = $leaveApplication->id;
         $hist->user_id = auth()->user()->id;
         $hist->action = "Applied on Behalf";
         $hist->save();
        //Send email notification
        //Notification::route('mail', $leaveApp->approver_one->email)->notify(new NewApplication($leaveApp));

        //$leaveApp->approver_one->notify(new NewApplication($leaveApp));

        //STORE
        return back()->with('message', 'Leave record submitted succesfully');
    }
}

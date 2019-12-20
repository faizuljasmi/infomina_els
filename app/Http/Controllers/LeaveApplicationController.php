<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\NewApplication;
use App\Notifications\StatusUpdate;
use App\LeaveType;
use App\User;
use App\ApprovalAuthority;
use App\LeaveApplication;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;

class LeaveApplicationController extends Controller
{
    //Create New Application
    public function create(){

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id','ASC')->get();

        //Get THIS user id
        $user = auth()->user();
        $leaveAuth = $user->approval_authority;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities of THIS user
        //ONLY CAN GET ID, HOW TO GET NAME?

        //TODO: Get leave balance of THIS employee

        return view('leaveapp.create')->with(compact('user','leaveType', 'groupMates','leaveAuth'));
    }


    //Store Application
    public function store(Request $request){
        
        //dd($request->emergency_contact_no);
        //Get user id
        $user = auth()->user();
        $leaveApp = new LeaveApplication;
        //get user id, leave type id
        $leaveApp->user_id = $user->id;
        $leaveApp->leave_type_id = $request->leave_type_id;
        //status set pending 1
        //get all authorities id
        $leaveApp->approver_id_1 = $request->approver_id_1;
        $leaveApp->approver_id_2 = $request->approver_id_2;
        $leaveApp->approver_id_3 = $request->approver_id_3;


        //get date from
        $leaveApp->date_from = $request->date_from;
        //get date to
        $leaveApp->date_to = $request->date_to;
        //get date resume
        $leaveApp->date_resume = $request->date_resume;
        //get total days
        $leaveApp->total_days = $request->total_days;
        //get reason
        $leaveApp->reason = $request->reason;
        //get relief personel id
        $leaveApp->relief_personnel_id = $request->relief_personnel_id;
        //get emergency contact
        $leaveApp->emergency_contact = $request->emergency_contact_no;

        //Attachment validation
        $validator = Validator::make($request->all(),
        ['attachment' => 'required|mimes:jpeg,png,jpg,pdf|max:2048']);

        // if validation fails
        if($validator->fails()) {
            return redirect()->to('/leave/apply')->with('message','Your file attachment format is invalid. Application is not submitted');
        }
        //If validation passes and has a file. Not necessary to check but just to be safe
        if($request->hasFile('attachment')){
            $att = $request->file('attachment');
            $uploaded_file = $att->store('public');
            //Pecahkan
            $paths = explode('/',$uploaded_file);
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
        return redirect()->to('/home')->with('message','Leave application submitted succesfully');

    }

    public function edit(LeaveApplication $leaveApplication){

    }

    public function update(Request $request, LeaveApplication $leaveApplication){

    }

    public function approve(LeaveApplication $leaveApplication){
    
        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        //If user id same as approver id 1
        if($la_1 == $user->id){
            //if no authority 2, terus change to approved
            if($la_2 == null){
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 2, 
            else{
                $leaveApplication->status = 'PENDING_2';
                //Notify the second approver
                $leaveApplication->approver_two->notify(new NewApplication($leaveApplication));
            }
        }
        //if user id same as approved id 2
        else if($la_2 == $user->id){
            //if no authority 3, terus change to approved
            if($la_3 == null){
                $leaveApplication->status = 'APPROVED';
            }
            //else update status to pending 3
            else{
                $leaveApplication->status = 'PENDING_3';
                //Notify the third approver
                $leaveApplication->approver_three->notify(new NewApplication($leaveApplication));
            }
        }
        //If user id same as approved id 3, update status to approved
        else{
            $leaveApplication->status = 'APPROVED';
        }
        $leaveApplication->update();

        //If the application is approved
        if($leaveApplication->status == 'APPROVED'){

            //Update leave taken table
            //Check for existing record
            $dupcheck = TakenLeave::where('leave_type_id', '=', $leaveApplication->leave_type_id, 'AND', 'user_id', '=', $leaveApplication->user_id)->first();

            //If does not exist, create new
            if($dupcheck == null){
                $tl = new TakenLeave;
                $tl->leave_type_id = $leaveApplication->leave_type_id;
                $tl->user_id = $leaveApplication->user_id;
                $tl->no_of_days = $leaveApplication->total_days;
                $tl->save();
            }
            //else update existing
            else{
                $dupcheck->no_of_days += $leaveApplication->total_days;
                $dupcheck->save();
            }

            //Update leave balance table
            //Check for existing record
             $dupcheck2 = LeaveBalance::where('leave_type_id', '=', $leaveApplication->leave_type_id, 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
    
            //If does not exist, create new
            if($dupcheck2 == null){
                $lb = new LeaveBalance;
                $lb->leave_type_id = $leaveApplication->leave_type_id;
                $lb->user_id = $leaveApplication->user_id;
                $le = LeaveEarning::where('leave_type_id','=',$leaveApplication->leave_type_id,'AND','user_id','=',$leaveApplication->user_id)->first();
                $lb->no_of_days = $le->no_of_days - $leaveApplication->total_days;
                $lb->save();
            }
            //else update existing
            else{
                $dupcheck2->no_of_days -= $leaveApplication->total_days;
                $dupcheck2->save();
            }
        }
        
        
        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));

        return redirect()->to('/admin')->with('message','Leave application status updated succesfully');
    }

    public function deny(LeaveApplication $leaveApplication){
    
        //Get current user id
        $user = auth()->user();
        //Get leave application authorities ID
        $la_1 = $leaveApplication->approver_id_1;
        $la_2 = $leaveApplication->approver_id_2;
        $la_3 = $leaveApplication->approver_id_3;

        //If user id same as approver id 1
        if($la_1 == $user->id){
            $leaveApplication->status = 'DENIED_1';
        }
        //if user id same as approved id 2
        else if($la_2 == $user->id){
            $leaveApplication->status = 'DENIED_2';
        }
        //If user id same as approved id 3,
        else{
            $leaveApplication->status = 'DENIED_3';
        }
        $leaveApplication->update();
        //Send status update email
        $leaveApplication->user->notify(new StatusUpdate($leaveApplication));


        return redirect()->to('/admin')->with('message','Leave application status updated succesfully');
    }

    public function view(LeaveApplication $leaveApplication){

        $leaveApp = $leaveApplication;
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id','ASC')->get();
        //Get THIS user id
        $user = $leaveApp->user;
        $leaveAuth = $user->approval_authority;
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);

        return view('leaveapp.view')->with(compact('leaveApp','leaveType','user','leaveAuth','groupMates'));
    }
}

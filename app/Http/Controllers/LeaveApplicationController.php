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
use App\Holiday;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    //Create New Application
    public function create(){

        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id','ASC')->get()->except('leave_type_id','=','12');

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();

        //Get leave applications from same group
        $leaveApps = LeaveApplication::orderBy('date_from','ASC')->get();

        $holidays = Holiday::all();
        $all_dates = array();
        foreach($holidays as $hols){
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
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
              if($la->user->emp_group_id == $user->emp_group_id){
              $startDate = new Carbon($la->date_from);
              $endDate = new Carbon ($la->date_to);
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
        }

        return view('leaveapp.create')->with(compact('user','leaveType', 'groupMates','leaveAuth','leaveBal','all_dates','applied_dates','approved_dates'));
    }


    //Store Application
    public function store(Request $request){
        
        //dd($request->emergency_contact_no);
        //Get user id
        $user = auth()->user();
        //Check Balance
        $leaveBal = LeaveBalance::where('leave_type_id', '=', $request->leave_type_id, 'AND', 'user_id', '=', $request->user_id)->first();
        //dd($leaveBal->no_of_days);
        if($request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12'){
            return redirect()->to('/leave/apply')->with('message','Your have insufficient leave balance. Please contact HR for more info.');
        }
        
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
        ['attachment' => 'required_if:leave_type_id,3|mimes:jpeg,png,jpg,pdf|max:2048']);

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
        //Get all leave types. TODO: show only entitled leave types instead of all leave types
        $leaveType = LeaveType::orderBy('id','ASC')->get();

        //Get THIS user id
        $user = auth()->user();
        //Get employees who are in the same group (for relieve personnel).
        $groupMates = User::orderBy('id','ASC')->where('emp_group_id', '=', $user->emp_group_id)->get()->except($user->id);
        //dd($groupMate->name);

        //Get approval authorities of THIS user
        $leaveAuth = $user->approval_authority;
        //Get approval authorities for this user
        $leaveAuthReplacement = User::orderBy('id','ASC')->get()->except($user->id);

        //TODO: Get leave balance of THIS employee
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();

        //Get leave applications from same group
        $leaveApps = LeaveApplication::orderBy('date_from','ASC')->get();

        $holidays = Holiday::all();
        $all_dates = array();
        foreach($holidays as $hols){
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
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
              if($la->user->emp_group_id == $user->emp_group_id){
              $startDate = new Carbon($la->date_from);
              $endDate = new Carbon ($la->date_to);
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
        }
       
        return view('leaveapp.edit')->with(compact('leaveApplication', 'user','leaveType', 'groupMates','leaveAuth','leaveBal','all_dates','applied_dates','approved_dates','leaveAuthReplacement'));
    }

    public function update(Request $request, LeaveApplication $leaveApplication){
        //dd($request->emergency_contact_no);
        //Get user id
        $user = auth()->user();
        //Check Balance
        $leaveBal = LeaveBalance::where('leave_type_id', '=', $request->leave_type_id, 'AND', 'user_id', '=', $request->user_id)->first();
        //dd($leaveBal->no_of_days);
        if($request->total_days > $leaveBal->no_of_days && $request->leave_type_id != '12'){
            dd('here');
            return redirect()->to('/leave/apply')->with('message','Your have insufficient leave balance. Please contact HR for more info.');
        }
        
        $leaveApp = $leaveApplication;
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
        ['attachment' => 'required_if:leave_type_id,3|mimes:jpeg,png,jpg,pdf|max:2048']);

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
        return redirect()->to('/home')->with('message','Leave application edited succesfully');
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

            //If the approved leave is a Replacement leave, assign taken to Replacement, and add day balance to Annual
            if($leaveApplication->leaveType->name == 'Replacement'){
                $lt = TakenLeave::where('leave_type_id', '=', $leaveApplication->leave_type_id, 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $lt->no_of_days += $leaveApplication->total_days;
                
                $lt->save();

                //Add balance to annual;
                $lb = LeaveBalance::where('leave_type_id', '=', '1', 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $lb->no_of_days += $leaveApplication->total_days;
                $lb->save();

                //Send status update email
                $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                return redirect()->to('/admin')->with('message','Replacement leave application status updated succesfully');
            }
            
            //If the approved leave is a Sick leave, deduct the amount taken in both sick leave and hospitalization balance
            if($leaveApplication->leaveType->name == 'Sick'){
                //Add in amount sick leave taken
                $lt = TakenLeave::where('leave_type_id', '=', $leaveApplication->leave_type_id, 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in sick leave balance
                $sickBalance = LeaveBalance::where('leave_type_id', '=', '3', 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $sickBalance->no_of_days -= $leaveApplication->total_days;
                $sickBalance->save();

                //Deduct balance in hosp leave balance
                $hospBalance = LeaveBalance::where('leave_type_id', '=', '4', 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $hospBalance->no_of_days -= $leaveApplication->total_days;
                $hospBalance->save();

                 //Send status update email
                 $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                 return redirect()->to('/admin')->with('message','Sick leave application status updated succesfully');
            }

            //If the approved leave is an emergency leave, deduct the taken amount to Annual Leave
            if($leaveApplication->leaveType->name == 'Emergency'){
                //Add in amount emergency leave taken
                $lt = TakenLeave::where('leave_type_id', '=', $leaveApplication->leave_type_id, 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $lt->no_of_days += $leaveApplication->total_days;
                $lt->save();

                //Deduct balance in emergency leave balance
                $emBalance = LeaveBalance::where('leave_type_id', '=', '6', 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $emBalance->no_of_days -= $leaveApplication->total_days;
                $emBalance->save();

                //Deduct balance in annual leave
                $annBalance = LeaveBalance::where('leave_type_id', '=', '1', 'AND', 'user_id', '=', $leaveApplication->user_id)->first();
                $annBalance->no_of_days -= $leaveApplication->total_days;
                $annBalance->save();
                //dd($annBalance->no_of_days);

                 //Send status update email
                 $leaveApplication->user->notify(new StatusUpdate($leaveApplication));
                 return redirect()->to('/admin')->with('message','Emergency leave application status updated succesfully');
            }

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

         //TODO: Get leave balance of THIS employee
         $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();

        $applied_dates = array();
        $startDate = new Carbon($leaveApp->date_from);
        $endDate = new Carbon($leaveApp->date_to);
        while ($startDate->lte($endDate)){
            $dates = str_replace("-","",$startDate->toDateString());
            $applied_dates[] = $dates;
            $startDate->addDay();
        }

        $holidays = Holiday::all();
        $hol_dates = array();
        foreach($holidays as $hols){
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            $hol_dates = [];
            while ($startDate->lte($endDate)){
                $dates = str_replace("-","",$startDate->toDateString());
                $hol_dates[] = $dates;
                $startDate->addDay();
            }
        }

        return view('leaveapp.view')->with(compact('leaveApp','leaveType','user','leaveAuth','groupMates','leaveBal','applied_dates','hol_dates'));
    }

    public function cancel(LeaveApplication $leaveApplication){
        $leaveApplication->status = "CANCELLED";
        $leaveApplication->save();
        return redirect()->to('/home')->with('message','Leave application cancelled succesfully');
    }
}

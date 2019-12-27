<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmpType;
use App\EmpGroup;
use App\LeaveType;
use App\LeaveEntitlement;
use App\LeaveEarning;
use App\LeaveBalance;
use App\TakenLeave;
use App\BroughtForwardLeave;


class RegistrationController extends Controller
{
    
    public function create(){
        $users = User::orderBy('staff_id', 'ASC')->simplePaginate(15);
        //dd($users);
        $empTypes = EmpType::orderBy('id','ASC')->get();
        //dd($empTypes);
        $empGroups = EmpGroup::orderBy('id','ASC')->get();
        return view ('registration.create')->with(compact('users','empTypes','empGroups'));
    }

    public function store(Request $request){
       
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
            'staff_id' => ['required','string','min:4','unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'join_date' => ['required'],
            'job_title' => ['required','string'],   
        ]); 
        $user = new User;
        $user->name = $request->name;
        $user->staff_id = $request->staff_id;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->user_type = $request->user_type;
        $user->join_date = $request->join_date;
        $user->gender = $request->gender;
        $user->emp_type_id = $request->emp_type_id;
        $user->emp_group_id = $request->emp_group_id;
       
        $user->job_title = $request->job_title;
        $user->save();
        // $user = User::create(request(['name','staff_id','email','password','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title']));
        
        return redirect()->route('user_view', ['user' => $user])->with('message', 'User created succesfully');
    }

    public function edit(User $user){
        $user = $user;
        $users = User::orderBy('id','ASC')->get()->except($user->id);
        $authUsers = User::where('user_type', '=', 'Admin')->get();
        $empType = $user->emp_types;
        $empTypes = EmpType::orderBy('id','ASC')->get();
        $empGroup = $user->emp_group;
        $empGroups = EmpGroup::orderBy('id','ASC')->get();
        $empAuth = $user->approval_authority;
        //dd($empAuth);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveEarn = LeaveEarning::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.edit')->with(compact('user','users','authUsers','empType','empTypes','empGroup','empGroups','empAuth','leaveTypes','leaveEnt','leaveEarn','broughtFwd','leaveBal','leaveTak'));
    }

    public function update(Request $request, User $user){
        try {
            $user->update($request->only('name','staff_id','email','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title','emergency_contact_name','emergency_contact_no'));
         } catch (\Exception $e) { // It's actually a QueryException but this works too
            if ($e->getCode() == 23000) {
                return redirect()->route('user_view', ['user' => $user])->with('message', 'Staff ID has already been taken. User details not updated.');
            }
         }
        return redirect()->route('user_view', ['user' => $user])->with('message', 'User profile updated succesfully');

    }

    public function profile(User $user){
        $user = $user;
        $users = User::orderBy('id','ASC')->get()->except($user->id);
        $authUsers = User::where('user_type', '=', 'Admin')->get();
        $empType = $user->emp_types;
        $empGroup = $user->emp_group;
        $empAuth = $user->approval_authority;
        //dd($empAuth->getAuthorityOneAttribute);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        $leaveEarn = LeaveEarning::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //dd($leaveEarn[1]->brought_forward);
        $broughtFwd = BroughtForwardLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveBal = LeaveBalance::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        $leaveTak = TakenLeave::orderBy('leave_type_id','ASC')->where('user_id','=',$user->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        //dd($user->name);
        return view('user.profile')->with(compact('user','users','authUsers','empType','empGroup','empAuth','leaveTypes','leaveEnt','leaveEarn', 'broughtFwd','leaveBal','leaveTak'));
    }

    public function deactivate(User $user){
        $user->status = 'Inactive';
        $user->update();
        return redirect()->route('user_create')->with('message', 'User has been deactivated');
    }
    public function destroy(User $user){
        $user->delete();
        return back();
    }
}



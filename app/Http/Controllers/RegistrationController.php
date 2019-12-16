<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmpType;
use App\EmpGroup;
use App\LeaveType;
use App\LeaveEntitlement;


class RegistrationController extends Controller
{
    
    public function create(){
        $users = User::orderBy('id', 'ASC')->simplePaginate(15);
        //dd($users);
        $empTypes = EmpType::orderBy('id','ASC')->get();
        //dd($empTypes);
        $empGroups = EmpGroup::orderBy('id','ASC')->get();
        return view ('registration.create')->with(compact('users','empTypes','empGroups'));
    }

    public function store(Request $request){
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'join_date' => ['required'],
            'job_title' => ['required','string'],   
        ]);        
        $user = User::create(request(['name','email','password','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title']));

        return redirect()->to('/create')->with('message', 'User created succesfully');
    }

    public function edit(User $user){
        $user = $user;
        $users = User::orderBy('id','ASC')->get()->except($user->id);
        $empType = $user->emp_types;
        $empTypes = EmpType::orderBy('id','ASC')->get();
        $empGroup = $user->emp_group;
        $empGroups = EmpGroup::orderBy('id','ASC')->get();
        $empAuth = $user->approval_authority;
        //dd($empAuth);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.edit')->with(compact('user','users','empType','empTypes','empGroup','empGroups','empAuth','leaveTypes','leaveEnt'));
    }

    public function update(Request $request, User $user){
        //dd($request->emp_group_id);
        $user->update($request->only('name','email','user_type','join_date', 'gender', 'emp_type_id','emp_group_id','job_title','emergency_contact_name','emergency_contact_no'));
        return redirect()->route('user_view', ['user' => $user])->with('message', 'User profile updated succesfully');

    }

    public function profile(User $user){
        $user = $user;
        $users = User::orderBy('id','ASC')->get()->except($user->id);
        $empType = $user->emp_types;
        $empGroup = $user->emp_group;
        $empAuth = $user->approval_authority;
        //dd($empAuth->getAuthorityOneAttribute);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.profile')->with(compact('user','users','empType','empGroup','empAuth','leaveTypes','leaveEnt'));
    }
}



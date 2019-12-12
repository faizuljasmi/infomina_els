<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmpType;
use App\LeaveType;
use App\LeaveEntitlement;


class RegistrationController extends Controller
{
    
    public function create(){
        $users = User::orderBy('id', 'ASC')->paginate(config('app.paginate'));
        //dd($users);
        $empTypes = EmpType::orderBy('id','ASC')->get();
        //dd($empTypes);
        return view ('registration.create')->with(compact('users','empTypes'));
    }

    public function store(Request $request){
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);        
        $user = User::create(request(['name','email','password','user_type', 'emp_type_id']));

        return redirect()->to('/create')->with('message', 'User created succesfully');
    }

    public function edit(User $user){
        $user = $user;
        return view('registration.edit')->with(compact('user'));
    }

    public function profile(User $user){
        $user = $user;
        $empType = $user->emp_types;
        //dd($empType->id);
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->where('emp_type_id', '=', $empType->id)->get();
        //dd($leaveEnt);
        $leaveTypes = LeaveType::orderBy('id','ASC')->get();
        return view('user.profile')->with(compact('user','empType','leaveTypes','leaveEnt'));
    }
}



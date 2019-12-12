<?php

namespace App\Http\Controllers;

use App\EmpType;
use Illuminate\Http\Request;
use App\LeaveType;
use App\LeaveEntitlement;

class EmpTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allTypes = $this->getAllTypes();
        $allLeaveTypes = $this->getAllLeaveTypes();
        
        $leaveEnt = LeaveEntitlement::orderBy('id','ASC')->get();
        //dd($leaveEnt);

        return view('emptype.create')->with(compact('allTypes', 'allLeaveTypes', 'leaveEnt'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
        ]);

        $emptype = EmpType::create(request(['name']));

        return redirect()->to('/emptype_create')->with('message', 'Employee Type created succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function show(EmpType $empType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpType $empType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpType $empType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpType  $empType
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpType $empType)
    {
        //
        $empType->delete();
        return back();
    }

    protected function getAllLeaveTypes(){
        return LeaveType::orderBy('id')->get();
    }
    protected function getAllTypes(){
        return EmpType::orderBy('id')->get();
    }
}

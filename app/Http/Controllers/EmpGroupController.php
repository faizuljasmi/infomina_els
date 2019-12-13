<?php

namespace App\Http\Controllers;

use App\EmpGroup;
use Illuminate\Http\Request;

class EmpGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $allGroups = $this->getAllEmpGroups();
        return view('empgroup.create')->with(compact('allGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
        ]);

        $empgroup = EmpGroup::create(request(['name']));

        return redirect()->to('/empgroup/create')->with('message', 'Employee group created succesfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmpGroup  $empGroup
     * @return \Illuminate\Http\Response
     */
    public function show(EmpGroup $empGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpGroup  $empGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(EmpGroup $empGroup)
    {
        //
        return view('empgroup.edit')->with(compact('empGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmpGroup  $empGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmpGroup $empGroup)
    {
        //
        $empGroup->update($request->only('name'));
        return redirect()->to('/empgroup/create')->with('message', 'Employee Group name updated succesfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmpGroup  $empGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmpGroup $empGroup)
    {
        //
        $empGroup->delete();
        return back();
    }

    protected function getAllEmpGroups(){
        return EmpGroup::orderBy('id')->get();
    }
}

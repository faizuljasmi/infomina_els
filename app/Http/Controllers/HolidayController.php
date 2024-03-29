<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Http\Controllers;

use App\Holiday;
use Illuminate\Http\Request;
use App\Country;
use App\State;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $holidays = Holiday::all();
        // Get the current year and the next year
        $currentYear = Carbon::now()->year;
        $nextYear = Carbon::now()->addYear()->year;

        // Fetch holidays for the current year and the next year
        $holidays = Holiday::whereYear('date_from', '>=', $currentYear)
                            ->whereYear('date_to', '<=', $nextYear)
                            ->get();
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
        // $countries = Country::all();
        $countries = Country::with(['holidays' => function ($query) use ($currentYear, $nextYear) {
            $query->whereYear('date_from', '>=', $currentYear)
                ->whereYear('date_to', '<=', $nextYear)
                ->orderBy('date_from', 'desc');
        }])->get();
        //dd($all_dates);
        $states = State::all();
        return view('holiday.index')->with(compact('holidays', 'all_dates','countries'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->total_days);
        $hol = new Holiday;
        $hol->name = $request->holiday_name;
        $hol->date_from = $request->date_from;
        $hol->date_to = $request->date_to;
        $hol->total_days = $request->total_days;
        $hol->state_id = $request->state_id;
        $hol->country_id = $request->country_id;
        $hol->save();

        return redirect()->to('/holiday/view')->with('message', $hol->name . ' created succesfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        $hol_country = $holiday->country;
        $other_holidays = $hol_country->country_wide_holidays;
        if($holiday->state != null){
            $hol_state = $holiday->state;
            $other_holidays = $other_holidays->merge($hol_state->state_wide_holidays);
        }

        $all_dates = array();
        foreach ($other_holidays as $hols) {
            $startDate = new Carbon($hols->date_from);
            $endDate = new Carbon($hols->date_to);
            while ($startDate->lte($endDate)) {
                $dates = str_replace("-", "", $startDate->toDateString());
                $all_dates[] = $dates;
                $startDate->addDay();
            }
        }
        $countries = Country::all();
        $states = State::where('country_id', $holiday->country_id)->get();
        return view('holiday.edit')->with(compact('holiday','all_dates','other_holidays','countries','states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        $holiday->name = $request->holiday_name;
        $holiday->date_from = $request->date_from;
        $holiday->date_to = $request->date_to;
        $holiday->total_days = $request->total_days;
        $holiday->state_id = $request->state_id;
        $holiday->country_id = $request->country_id;
        $holiday->update();

        return redirect()->to('/holiday/view')->with('message', $holiday->name . ' updated succesfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function delete(Holiday $holiday)
    {
        $holName = $holiday->name;
        $holiday->delete();
        return back()->with('message', $holName . ' deleted succesfully');
    }
}

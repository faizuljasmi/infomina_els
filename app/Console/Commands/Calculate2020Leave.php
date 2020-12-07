<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Carbon\Carbon;

class Calculate2020Leave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:2020';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To calculate 2020 prorated leave entitlements only.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $employees = User::get();

        foreach($employees as $emp)
        {
            // if ($emp->id == '13') { // Faizal
                $currentYear = '2020';
    
                $after36months = Carbon::parse($emp->join_date)->addMonths(36)->isoFormat('Y-MM-DD'); // Return String
                $after60months = Carbon::parse($emp->join_date)->addMonths(60)->isoFormat('Y-MM-DD'); // Return String
    
                $is3rdYear = substr($after36months, 0, 4); // Year
                $is5thYear = substr($after60months, 0, 4); // Year
    
                $prorateEnt = 0;
                $entAfter = 0;
    
                if ($is3rdYear == $currentYear) {
                    $prorateEnt = 16;       
                    $month = $after36months;
                } else if ($is5thYear == $currentYear) {
                    $prorateEnt = 18;
                    $month = $after60months;
                }

                $defaultEnt = 14; // Default entitlement for all staff is 14 days.
    
                if ($prorateEnt > 0) {
                    $annMonth = substr($month, 5, 2); // Month
                    $entBefore = ((intval($annMonth) - 1) / 12) * $defaultEnt; // To calculate days entitled before prorated months.
                    $entBefore = round($entBefore);
                    $entAfter = ((12 - (intval($annMonth) - 1)) / 12) * $prorateEnt; // To calculate days entitled for the prorated months.
                    $entAfter = ceil($entAfter);
                    
                    // dd($annMonth, $entBefore, $entAfter);
                    
                    $leaveEarn = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                    if ($leaveEarn) {
                        $tempEarn = $leaveEarn->no_of_days;
                        $leaveEarn->no_of_days = ($tempEarn - $defaultEnt) + $entBefore + $entAfter; 
                        $leaveEarn->update();
                    }
                    
                    $leaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                    if ($leaveBal) {
                        $leaveBal->no_of_days = $leaveBal->no_of_days + ($leaveEarn->no_of_days) - $tempEarn;
                        $leaveBal->update();
                    }
                }
            // }
        }
    }
}

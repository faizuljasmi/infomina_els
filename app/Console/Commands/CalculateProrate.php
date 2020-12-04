<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Carbon\Carbon;

class CalculateProrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:prorate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To calculate prorated leave earning.';

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
        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        // Get this month joined employees
        $monthEmp = User::where('join_date', 'LIKE', '%-'.$currentMonth.'-%')->get();

        foreach($monthEmp as $emp)
        {
            // // Testing
            // $fromYear = 2017;
            // $fromMonth = 11;
            // $toYear = 2020;
            // $toMonth = 11;
            // $from = Carbon::parse($fromYear.'-'.$fromMonth);
            // $to = Carbon::parse($toYear.'-'.$toMonth);

            $from = Carbon::parse($emp->join_date);
            $to = Carbon::parse($currentYear.'-'.$currentMonth);
            $diff = $from->diffInMonths($to);
            // echo $diff;

            $prorateEnt = 0;
            $entAfter = 0;
            $defaultEnt = 14;
            
            if (($diff + 1) == 36) { // If 3 Years
                $prorateEnt = 16;
            } else if (($diff + 1) == 60) { // If 5 Years
                $prorateEnt = 18;
            } 

            $initEnt = (($currentMonth - 1)/12) * 14; // All the entitlement is set to 14 days initially, to calculate days entitled before serving 3/5 years
            
            if ($prorateEnt > 0) {
                $entBefore = ((intval($currentMonth) - 1) / 12) * $defaultEnt; // To calculate days entitled before prorated months.
                $entBefore = round($entBefore);
                $entAfter = ((12 - ($currentMonth - 1))/12) * $prorateEnt ; // To calculate days entitled for the prorated months.
                $entAfter = round($entAfter);

                $leaveEarn = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                if ($leaveEarn) {
                    $tempEarn = $leaveEarn->no_of_days;
                    $leaveEarn->no_of_days = ($tempEarn - $defaultEnt) + $entAfter + $entBefore;
                    $leaveEarn->update();
                }
                
                $leaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                if ($leaveEarn) {
                    $leaveBal->no_of_days = $leaveBal->no_of_days + ($leaveEarn->no_of_days) - $tempEarn;
                    $leaveBal->update();
                }
            }
        }

        return;
    }
}

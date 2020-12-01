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
            // $fromYear = 2017;
            // $fromMonth = 11;
            // $toYear = 2020;
            // $toMonth = 11;
            // $from = Carbon::parse($fromYear.'-'.$fromMonth);
            // $to = Carbon::parse($toYear.'-'.$toMonth);

            $from = Carbon::parse($emp->join_date);
            $to = Carbon::parse($currentYear.'-'.$currentMonth);
            $diff = $from->diffInMonths($to);
            echo $diff;

            $annualEnt = 0;
            $newEnt = 0;
            
            if (($diff + 1) == 36) {
                $annualEnt = 16;
            } else if (($diff + 1) == 60) {
                $annualEnt = 18;
            } 
            
            if ($annualEnt > 0) {
                $newEnt = ((12 - ($currentMonth - 1))/12) * $annualEnt;
                $newEntDay = floor($newEnt * 2)/2; // Round off to nearest half integer

                $leaveEarn = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                $leaveEarn->no_of_days = $leaveEarn->no_of_days + $newEntDay;
                $leaveEarn->update();
                
                $leaveBal = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                $leaveBal->no_of_days = $leaveBal->no_of_days + $newEntDay;
                $leaveBal->update();
            }
        }

        return;
    }
}

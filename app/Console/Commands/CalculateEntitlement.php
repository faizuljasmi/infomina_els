<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Carbon\Carbon;

class CalculateEntitlement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:entitlement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To calculate annual leave entitlement.';

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

        $today = Carbon::now();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        foreach($employees as $emp) {
            $carryForw = 0;
            $annualEnt = 0;
            $annualBal = 0;

            $annualLeave = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();

            if ($annualLeave != null) {
                $annualBal = $annualLeave->no_of_days;
                if ($annualBal > 0 && $annualBal <= 5) {
                    $carryForw = $annualBal;
                } else if ($annualBal > 5) {
                    $carryForw = 5;
                }
            
                $joinDate = Carbon::parse($emp->join_date);
                $to = Carbon::parse($currentYear.'-'.$currentMonth);
                $diff = $joinDate->diffInMonths($to);
                
                if (($diff + 1) < 36) {
                    $annualEnt = 14;
                } else if (($diff + 1) >= 36 && ($diff + 1) < 60) {
                    $annualEnt = 16;
                } else if (($diff + 1) >= 60) {
                    $annualEnt = 18;
                }
                
                for($leaveType = 1; $leaveType <= 13; $leaveType++) // Total 13 leave types
                { 
                    $empEarning = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', $leaveType)->first();
                    if ($empEarning != null) {
                        if ($leaveType == 1) {
                            $empEarning->no_of_days = $carryForw + $annualEnt; // Annual
                        } else if ($leaveType == 2) {
                            $empEarning->no_of_days = 0; // Calamity
                        } else if ($leaveType == 3) { 
                            $empEarning->no_of_days = 0; // Sick
                        } else if ($leaveType == 4) {
                            $empEarning->no_of_days = 60; // Hospitalization
                        } else if ($leaveType == 5) {
                            $empEarning->no_of_days = 0; // Compassionate
                        } else if ($leaveType == 6) {
                            $empEarning->no_of_days = 5; // Emergency
                        } else if ($leaveType == 7) {
                            $empEarning->no_of_days = 0; // Marriage
                        } else if ($leaveType == 8) {
                            $empEarning->no_of_days = 0; // Maternity
                        } else if ($leaveType == 9) {
                            $empEarning->no_of_days = 0; // Paternity
                        } else if ($leaveType == 10) {
                            $empEarning->no_of_days = 0; // Training
                        } else if ($leaveType == 11) {
                            $empEarning->no_of_days = 0; // Unpaid
                        } else if ($leaveType == 12) {
                            $empEarning->no_of_days = 0; // Replacement
                        } else if ($leaveType == 13){
                            $empEarning->no_of_days = 0; // Wedding
                        }
                        $empEarning->update();
                    }
                }
            }
        }
    }
}

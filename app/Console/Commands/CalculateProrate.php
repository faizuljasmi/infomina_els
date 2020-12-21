<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\ProrateUpdate;
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
            $from = Carbon::parse($emp->join_date);
            $to = Carbon::parse($currentYear.'-'.$currentMonth);
            $diff = $from->diffInMonths($to);

            if ($emp->emp_type_id != 3 || $emp->emp_type_id != 4 || $emp->emp_type_id != 5) // Executive and Non-Executive Staff Only.
            {
                // Calculate AL Prorate
                $prorateAL = 0;
                $annualAfter = 0;
                
                if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                    $defaultAL = 14;
                } else if ($emp->emp_type_id == 2) { // Non Executive
                    $defaultAL = 12;
                }
            
                if (($diff + 1) == 36) { // If 3 Years
                    if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                        $prorateAL = 16;
                    } else if ($emp->emp_type_id == 2) { // Non Executive
                        $prorateAL = 14;
                    }
                } else if (($diff + 1) == 60) { // If 5 Years
                    if ($emp->emp_type_id == 1 || $emp->emp_type_id == 6 || $emp->emp_type_id == 7 || $emp->emp_type_id == 8) { // Executive
                        $prorateAL = 18;
                    } else if ($emp->emp_type_id == 2) { // Non Executive
                        $prorateAL = 16;
                    }
                } 

                if ($prorateAL > 0 ) {
                    $annualBefore = ((intval($currentMonth) - 1) / 12) * $defaultAL; // To calculate days entitled before prorated months.
                    $annualBefore = round($annualBefore);
                    $annualAfter = ((12 - ($currentMonth - 1)) / 12) * $prorateAL ; // To calculate days entitled for the prorated months.
                    $annualAfter = ceil($annualAfter);

                    $leaveEarnAL = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                    if ($leaveEarnAL) {
                        $tempEarnAL = $leaveEarnAL->no_of_days;
                        $leaveEarnAL->no_of_days = ($tempEarnAL - $defaultAL) + $annualAfter + $annualBefore;
                        $leaveEarnAL->update();
                    }

                    $gainAL = ($annualBefore + $annualAfter) - $defaultAL;
                    
                    $leaveBalAL = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 1)->first();
                    if ($leaveBalAL && $leaveEarnAL) {
                        $tempBalAL = $leaveBalAL->no_of_days;
                        $leaveBalAL->no_of_days = $tempBalAL + $gainAL;
                        $leaveBalAL->update();
                    }   

                    if ($gainAL > 0) {
                        $staff = ['leave' => 'Annual', 'name' => $emp->name, 'gain' => $gainAL, 'balance' => $leaveBalAL->no_of_days];
                        $emp->notify(new ProrateUpdate($staff));
                    }
                }

                // Calculate MC Prorate
                $prorateMC = 0;
                $mcAfter = 0;
                $defaultMC = 14;

                if (($diff + 1) == 24) { // If 2 Years
                    $prorateMC = 18;
                } else if (($diff + 1) == 60) { // If 5 Years
                    $prorateMC = 22;
                } 

                if ($prorateMC > 0 ) {
                    $mcBefore = ((intval($currentMonth) - 1) / 12) * $defaultMC; // To calculate days entitled before prorated months.
                    $mcBefore = round($mcBefore);
                    $mcAfter = ((12 - ($currentMonth - 1)) / 12) * $prorateMC ; // To calculate days entitled for the prorated months.
                    $mcAfter = ceil($mcAfter);

                    $leaveEarnMC = LeaveEarning::where('user_id', $emp->id)->where('leave_type_id', 3)->first();
                    if ($leaveEarnMC) {
                        $tempEarnMC = $leaveEarnMC->no_of_days;
                        $leaveEarnMC->no_of_days = ($tempEarnMC - $defaultMC) + $mcAfter + $mcBefore;
                        $leaveEarnMC->update();
                    }

                    $gainMC = ($mcBefore + $mcAfter) - $defaultMC;
                    
                    $leaveBalMC = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 3)->first();
                    if ($leaveBalMC && $leaveEarnMC) {
                        $tempBalMC = $leaveBalMC->no_of_days;
                        $leaveBalMC->no_of_days = $tempBalMC + $gainMC;
                        $leaveBalMC->update();
                    }   

                    $leaveBalHosp = LeaveBalance::where('user_id', $emp->id)->where('leave_type_id', 4)->first();
                    if ($leaveBalHosp && $leaveBalMC && $leaveEarnMC) {
                        $tempBalHosp = $leaveBalHosp->no_of_days;
                        $hospBalance = $tempBalHosp + $gainMC;
                        if ($hospBalance >= 60) {
                            $leaveBalHosp->no_of_days = 60;
                        } else {
                            $leaveBalHosp->no_of_days = $hospBalance;
                        }
                        $leaveBalHosp->update();
                    }   

                    if ($gainMC > 0) {
                        $staff = ['leave' => 'Medical', 'name' => $emp->name, 'gain' => $gainMC, 'balance' => $leaveBalMC->no_of_days];
                        $emp->notify(new ProrateUpdate($staff));
                    }
                }
            }
        }

        return;
    }
}

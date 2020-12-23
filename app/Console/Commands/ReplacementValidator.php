<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DateTime;
use App\LeaveApplication;
use Carbon\Carbon;

class ReplacementValidator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validate:replacement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check the validity of the available replacement leave.';

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
        $today = new DateTime();

        $claims = LeaveApplication::where('leave_type_id', 12)->where('remarks', 'Claim')->get();
        
        foreach($claims as $claim) {
            $start = new DateTime($claim->date_from);
            $diff = $start->diff($today)->format('%a');

            if ($diff >= 30) {
                // $claim->status = 'EXPIRED';
                $totalTaken = 0;
    
                foreach($claim->replacement_applications as $app) {
                    if ($app->status == 'APROVED') {
                        $totalTaken += $app->no_of_days;
                    }
                }
    
                $balanceForThisClaim = $claim->no_of_days - $totalTaken;
    
                if ($balanceForThisClaim > 0 && $balanceForThisClaim < $claim->no_of_days) {
                    // This claim has been utilized a part but not fully.
                } else if ($balanceForThisClaim == $claim->no_of_days) {
                    // This claim has never been utilized.
                } else if ($balanceForThisClaim == 0) {
                    // This claim has been utilized fully.
                }
            }

        }
    }
}

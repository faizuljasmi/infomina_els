<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\LeaveEarning;
use App\LeaveBalance;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Notifications\Notifiable;
use Notification;
use App\Notifications\ProrateUpdate;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test.';

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

        $emps = [];
        
        foreach($employees as $emp)
        {
            if ($emp->id == 102) {
                $staff = (object) ['Name' => 'Gobhin'];
                array_push($emps, $emp->name);

                $emp->notify(new ProrateUpdate($staff));
            }
        }

        print_r($emps);
    }
}
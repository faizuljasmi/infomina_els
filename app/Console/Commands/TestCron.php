<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\EarningUpdate;
use App\User;
use App\HealthMetric;
use Notification;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test';

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
        // $hm = HealthMetric::get();

        // foreach($hm as $x) {
        //     $temp = $x->total_days;
        //     $x->total_days = $temp + 1;
        //     $x->save();
        // }

        $u = User::where('id', 102)->first();
        $u->notify(new EarningUpdate($u));
    }
}

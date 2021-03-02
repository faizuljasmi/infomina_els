<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\TestCron;
use App\Jobs\ReplacementValidator;
use App\Jobs\CalculateEarning;
use App\Jobs\CalculateProrate;
use App\Jobs\FetchHealthMetrics;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // 
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->job(new TestCron)->everyMinute()->withoutOverlapping();

        $schedule->job(new ReplacementValidator)->dailyAt('06:00')->withoutOverlapping();

        // $schedule->job(new CalculateEarning)->yearlyOn(1, 1, '03:00')->withoutOverlapping(); // ade problem

        $schedule->job(new CalculateProrate)->monthlyOn(1, '08:00')->withoutOverlapping();

        $schedule->job(new FetchHealthMetrics)->everyTenMinutes()->withoutOverlapping();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

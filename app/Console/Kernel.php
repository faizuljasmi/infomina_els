<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\TestCron;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\CalculateProrate',
        'App\Console\Commands\CalculateEarning',
        'App\Console\Commands\ReplacementValidator',
        'App\Console\Commands\TestCron',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('calculate:prorate')
        //          ->monthlyOn(1, '08:00');

        // $schedule->command('calculate:earning')
        //          ->yearlyOn(1, 1, '03:00');

        // $schedule->command('validate:replacement')
        //          ->dailyAt('06:00');

        // $schedule->command('test:cron')
        //          ->everyTwoMinutes();

        $schedule->job(new TestCron)->everyMinute();
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

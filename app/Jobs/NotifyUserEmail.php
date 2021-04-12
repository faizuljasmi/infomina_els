<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\LeaveApplication;
use App\Notifications\StatusUpdate;

class NotifyUserEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leave_app;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaveApplication $leave_app)
    {
        $this->leave_app = $leave_app;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->leave_app->user->notify(new StatusUpdate($this->leave_app));
    }
}

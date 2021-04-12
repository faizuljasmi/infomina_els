<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\LeaveApplication;
use App\Services\LeaveService;

class NotifyWspace implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $leave_app;
    protected $leaveService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaveApplication $leave_app, LeaveService $leaveService)
    {
        $this->leave_app = $leave_app;
        $this->leaveService = $leaveService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->leaveService->notifyWspace($this->leave_app);
    }
}

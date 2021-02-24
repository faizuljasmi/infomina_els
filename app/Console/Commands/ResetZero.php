<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BurntLeave;

class ResetZero extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:zero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To reset all burnt leave..';

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
        $allBurnt = BurntLeave::get();
        
        foreach($allBurnt as $burnt) {
            $burnt->no_of_days = 0;
            $burnt->update();
        }
    }
}

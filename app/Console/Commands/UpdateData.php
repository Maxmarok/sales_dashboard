<?php

namespace App\Console\Commands;

use App\Jobs\UpdateReportByPeriodJob;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::get();

        foreach ($users as $user){
            UpdateReportByPeriodJob::dispatch($user);
        }
    }
}

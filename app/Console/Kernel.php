<?php

namespace App\Console;

use App\Console\Commands\SyncEmployeeGroups;
use App\Console\Commands\SyncEmployees;
use App\Console\Commands\SyncStudentGroups;
use App\Console\Commands\SyncStudents;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SyncStudents::class)->hourly();
        $schedule->command(SyncEmployees::class)->hourly();
        $schedule->command(SyncStudentGroups::class)->hourly();
        $schedule->command(SyncEmployeeGroups::class)->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

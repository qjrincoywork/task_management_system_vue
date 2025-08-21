<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CleanupOldTasks;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new CleanupOldTasks)->dailyAt("00:00");
    }

    protected function commands(): void
    {
        $this->load(__DIR__."/Commands");
    }
}

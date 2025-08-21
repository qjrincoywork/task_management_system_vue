<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupOldTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Delete tasks older than 30 days.
     *
     * This job is used to remove old tasks from the database.
     *
     * @return void
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays(30);

        $deletedCount = Task::where("created_at", "<", $cutoffDate)->delete();

        Log::info("CleanupOldTasks: Deleted {$deletedCount} tasks older than 30 days");
    }
}

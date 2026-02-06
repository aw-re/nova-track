<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'Created task: ' . $task->title,
                'model_type' => 'Task',
                'model_id' => $task->id,
            ]);
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Updated task: ' . $task->title,
                'model_type' => 'Task',
                'model_id' => $task->id,
            ]);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => 'Deleted task: ' . $task->title,
                'model_type' => 'Task',
                'model_id' => $task->id,
            ]);
        }
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}

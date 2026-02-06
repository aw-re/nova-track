<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Enums\TaskStatusEnum;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all tasks for the engineer as a collection (not paginated)
        $tasks = Task::with(['project', 'createdBy'])
            ->where('assigned_to', Auth::id())
            ->orderBy('due_date')
            ->get();

        // For pagination in the main view
        $paginatedTasks = Task::with(['project', 'createdBy'])
            ->where('assigned_to', Auth::id())
            ->orderBy('due_date')
            ->paginate(10);

        return view('engineer.tasks.index', compact('tasks', 'paginatedTasks'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load([
            'project',
            'createdBy',
            'updates' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'updates.user'
        ]);

        return view('engineer.tasks.show', compact('task'));
    }

    /**
     * Start working on a task.
     */
    public function startTask(Task $task)
    {
        $this->authorize('start', $task);

        // Check if the task is in a startable state
        if (!in_array($task->status, [TaskStatusEnum::BACKLOG, TaskStatusEnum::TODO])) {
            return redirect()->route('engineer.tasks.show', $task)
                ->with('error', __('messages.error.invalid_status_start'));
        }

        $oldStatus = $task->status;
        $task->update([
            'status' => TaskStatusEnum::IN_PROGRESS,
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => TaskStatusEnum::IN_PROGRESS,
            'comment' => 'Task started.',
        ]);

        return redirect()->route('engineer.tasks.show', $task)
            ->with('success', __('messages.success.started', ['model' => __('app.task')]));
    }

    /**
     * Complete a task.
     */
    public function completeTask(Request $request, Task $task)
    {
        $this->authorize('complete', $task);

        // Check if the task is in a completable state
        if ($task->status !== TaskStatusEnum::IN_PROGRESS) {
            return redirect()->route('engineer.tasks.show', $task)
                ->with('error', __('messages.error.invalid_status_complete'));
        }

        $request->validate([
            'completion_notes' => 'required|string',
        ]);

        $oldStatus = $task->status;
        $task->update([
            'status' => TaskStatusEnum::COMPLETED,
            'completed_at' => now(),
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => TaskStatusEnum::COMPLETED,
            'comment' => $request->completion_notes,
        ]);

        return redirect()->route('engineer.tasks.show', $task)
            ->with('success', __('messages.success.completed', ['model' => __('app.task')]));
    }

    /**
     * Update task progress.
     */
    public function updateProgress(Request $request, Task $task)
    {
        $this->authorize('updateProgress', $task);

        // Check if the task is in progress
        if ($task->status !== TaskStatusEnum::IN_PROGRESS) {
            return redirect()->route('engineer.tasks.show', $task)
                ->with('error', __('messages.error.invalid_status_progress'));
        }

        $request->validate([
            'progress_update' => 'required|string',
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $task->status,
            'new_status' => $task->status,
            'comment' => $request->progress_update,
        ]);

        return redirect()->route('engineer.tasks.show', $task)
            ->with('success', __('messages.success.progress_updated'));
    }
}

<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Enums\TaskStatusEnum;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:contractor']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['project', 'createdBy', 'assignedBy'])
            ->where('assigned_to', Auth::id())
            ->orderBy('due_date')
            ->get();

        $paginatedTasks = Task::with(['project', 'createdBy', 'assignedBy'])
            ->where('assigned_to', Auth::id())
            ->orderBy('due_date')
            ->paginate(10);

        return view('contractor.tasks.index', compact('tasks', 'paginatedTasks'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', __('messages.error.unauthorized'));
        }

        $task->load([
            'project',
            'createdBy',
            'assignedBy',
            'updates' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'updates.user'
        ]);

        return view('contractor.tasks.show', compact('task'));
    }

    /**
     * Start working on a task.
     */
    public function startTask(Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', __('messages.error.unauthorized'));
        }

        // Check if the task is in a startable state
        if (!in_array($task->status, [TaskStatusEnum::BACKLOG, TaskStatusEnum::TODO])) {
            return redirect()->route('contractor.tasks.show', $task)
                ->with('error', __('messages.error.invalid_status_start'));
        }

        $oldStatus = $task->status;
        $task->update([
            'status' => TaskStatusEnum::IN_PROGRESS,
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus instanceof TaskStatusEnum ? $oldStatus->value : $oldStatus,
            'new_status' => TaskStatusEnum::IN_PROGRESS->value,
            'comment' => __('messages.task_started'),
        ]);

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', __('messages.success.started', ['model' => __('app.task')]));
    }

    /**
     * Complete a task.
     */
    public function completeTask(Request $request, Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', __('messages.error.unauthorized'));
        }

        // Check if the task is in a completable state
        if ($task->status !== TaskStatusEnum::IN_PROGRESS) {
            return redirect()->route('contractor.tasks.show', $task)
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
            'old_status' => $oldStatus instanceof TaskStatusEnum ? $oldStatus->value : $oldStatus,
            'new_status' => TaskStatusEnum::COMPLETED->value,
            'comment' => $request->completion_notes,
        ]);

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', __('messages.success.completed', ['model' => __('app.task')]));
    }

    /**
     * Update task progress.
     */
    public function updateProgress(Request $request, Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', __('messages.error.unauthorized'));
        }

        // Check if the task is in progress
        if ($task->status !== TaskStatusEnum::IN_PROGRESS) {
            return redirect()->route('contractor.tasks.show', $task)
                ->with('error', __('messages.error.invalid_status_progress'));
        }

        $request->validate([
            'progress_update' => 'required|string',
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $task->status instanceof TaskStatusEnum ? $task->status->value : $task->status,
            'new_status' => $task->status instanceof TaskStatusEnum ? $task->status->value : $task->status,
            'comment' => $request->progress_update,
        ]);

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', __('messages.success.progress_updated'));
    }
}


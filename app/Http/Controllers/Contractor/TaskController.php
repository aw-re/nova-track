<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all tasks for the contractor as a collection (not paginated)
        $tasks = Task::with(['project', 'createdBy'])
            ->where('assigned_to', Auth::id())
            ->orderBy('due_date')
            ->get();
            
        // For pagination in the main view
        $paginatedTasks = Task::with(['project', 'createdBy'])
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
                ->with('error', 'You do not have permission to view this task.');
        }

        $task->load(['project', 'createdBy', 'updates' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'updates.user']);
        
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
                ->with('error', 'You do not have permission to start this task.');
        }

        // Check if the task is in a startable state
        if (!in_array($task->status, ['backlog', 'todo'])) {
            return redirect()->route('contractor.tasks.show', $task)
                ->with('error', 'This task cannot be started because it is already in progress or completed.');
        }

        $oldStatus = $task->status;
        $task->update([
            'status' => 'in_progress',
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'in_progress',
            'comment' => 'Task started.',
        ]);

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', 'Task started successfully.');
    }

    /**
     * Complete a task.
     */
    public function completeTask(Request $request, Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', 'You do not have permission to complete this task.');
        }

        // Check if the task is in a completable state
        if ($task->status !== 'in_progress') {
            return redirect()->route('contractor.tasks.show', $task)
                ->with('error', 'This task cannot be completed because it is not in progress.');
        }

        $request->validate([
            'completion_notes' => 'required|string',
        ]);

        $oldStatus = $task->status;
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Create task update record
        $task->updates()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => 'completed',
            'comment' => $request->completion_notes,
        ]);

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', 'Task completed successfully.');
    }

    /**
     * Update task progress.
     */
    public function updateProgress(Request $request, Task $task)
    {
        // Check if the contractor is assigned to the task
        if ($task->assigned_to !== Auth::id()) {
            return redirect()->route('contractor.tasks.index')
                ->with('error', 'You do not have permission to update this task.');
        }

        // Check if the task is in progress
        if ($task->status !== 'in_progress') {
            return redirect()->route('contractor.tasks.show', $task)
                ->with('error', 'You can only update progress for tasks that are in progress.');
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

        return redirect()->route('contractor.tasks.show', $task)
            ->with('success', 'Progress update added successfully.');
    }
}

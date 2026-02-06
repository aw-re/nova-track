<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
use App\Enums\TaskStatusEnum;
use App\Enums\TaskPriorityEnum;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Task::with(['project', 'assignedBy', 'assignedTo']);

        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned to
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $tasks = $query->latest()->paginate(15);

        // Get data for filters
        $projects = Project::all();
        $users = User::all();
        $statuses = TaskStatusEnum::cases();
        $priorities = TaskPriorityEnum::cases();

        return view('admin.tasks.index', compact('tasks', 'projects', 'users', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::all();
        $users = User::all();
        return view('admin.tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTaskRequest $request)
    {
        Task::create(array_merge($request->validated(), [
            'assigned_by' => Auth::id(),
            'completed_at' => $request->status === TaskStatusEnum::COMPLETED->value ? now() : null,
        ]));

        return redirect()->route('admin.tasks.index')
            ->with('success', __('messages.success.created', ['model' => __('app.task')]));
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignedBy', 'assignedTo', 'updates.user', 'resourceRequests.resource']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function edit(Task $task)
    {
        $projects = Project::all();
        $users = User::all();
        return view('admin.tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $oldStatus = $task->status;
        $newStatus = $request->status;

        $task->update($request->validated());

        // Update completed_at timestamp if status changed to completed
        if ($oldStatus !== TaskStatusEnum::COMPLETED && $newStatus === TaskStatusEnum::COMPLETED->value) {
            $task->update(['completed_at' => now()]);
        } elseif ($oldStatus === TaskStatusEnum::COMPLETED && $newStatus !== TaskStatusEnum::COMPLETED->value) {
            $task->update(['completed_at' => null]);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', __('messages.success.updated', ['model' => __('app.task')]));
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', __('messages.success.deleted', ['model' => __('app.task')]));
    }
}

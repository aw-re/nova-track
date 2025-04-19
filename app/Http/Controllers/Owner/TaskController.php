<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
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
        $this->middleware(['auth', 'role:project_owner']);
    }

    /**
     * Display a listing of the tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::whereHas('project', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->with(['project', 'assignedBy', 'assignedTo']);
        
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
        $projects = $user->ownedProjects;
        $projectMembers = User::whereHas('projectMemberships', function($query) use ($user) {
            $query->whereHas('project', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            });
        })->get();
        $statuses = ['backlog', 'todo', 'in_progress', 'review', 'completed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        
        return view('owner.tasks.index', compact('tasks', 'projects', 'projectMembers', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new task.
     *
     * @return \Illuminate\View\View
     */
    public function create()
{
    $user = Auth::user();
    $projects = $user->ownedProjects;
    
    // Get engineers and contractors separately
    $engineers = User::whereHas('roles', function($q) {
        $q->where('name', 'engineer');
    })->get();
    
    $contractors = User::whereHas('roles', function($q) {
        $q->where('name', 'contractor');
    })->get();
    
    return view('owner.tasks.create', compact('projects', 'engineers', 'contractors'));
}

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,review,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        // Check if user is the owner of the project
        $project = Project::find($request->project_id);
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.tasks.index')
                ->with('error', 'You can only create tasks for your own projects.');
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_by' => Auth::id(),
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
            'completed_at' => $request->status === 'completed' ? now() : null,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Created task: ' . $task->title,
            'model_type' => 'Task',
            'model_id' => $task->id,
        ]);

        return redirect()->route('owner.tasks.index')
            ->with('success', 'Task created successfully.');
    }
    public function assignTask(Request $request, Task $task)
{
    // التحقق من أن المستخدم هو مالك المشروع
    if ($task->project->owner_id !== auth()->id()) {
        return back()->with('error', 'غير مصرح لك بتعيين هذه المهمة');
    }

    // التحقق من صحة البيانات
    $validated = $request->validate([
        'assigned_to' => 'required|exists:users,id',
        'comment' => 'nullable|string'
    ]);

    // تحديث المهمة
    $task->update([
        'assigned_to' => $validated['assigned_to'],
        'status' => 'in_progress' // أو أي حالة تريدها
    ]);

    // إضافة تعليق إذا وجد
    if (!empty($validated['comment'])) {
        Comment::create([
            'content' => $validated['comment'],
            'task_id' => $task->id,
            'user_id' => auth()->id()
        ]);
    }

    return redirect()->route('owner.tasks.show', $task)
           ->with('success', 'task has been assigned successfully.');
}

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(Task $task)
{
    // التحقق من أن المستخدم هو مالك المشروع
    if ($task->project->owner_id !== Auth::id()) {
        return redirect()->route('owner.tasks.index')
            ->with('error', 'You can only view tasks for your own projects.');
    }

    // جلب المهندسين والمقاولين
    $engineers = User::whereHas('roles', function($q) {
        $q->where('name', 'engineer');
    })->get();

    $contractors = User::whereHas('roles', function($q) {
        $q->where('name', 'contractor');
    })->get();

    $task->load(['project', 'assignedTo', 'updates']);

    return view('owner.tasks.show', compact('task', 'engineers', 'contractors'));
}
public function assign(Request $request, Task $task)
{
    // التحقق من صلاحيات المستخدم
    if ($task->project->owner_id !== auth()->id()) {
        return redirect()->back()->with('error', 'You are not authorized to assign this task');
    }

    $validated = $request->validate([
        'assigned_to' => 'required|exists:users,id',
        'comment' => 'nullable|string'
    ]);

    // تعيين المهمة
    $task->update([
        'assigned_to' => $validated['assigned_to'],
        'status' => 'in_progress'
    ]);

    // تسجيل النشاط (إذا كنت تستخدم activity log)
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'task_assigned',
        'description' => 'Assigned task to user ID: ' . $validated['assigned_to']
    ]);

    return redirect()->route('owner.tasks.show', $task)
           ->with('success', 'Task assigned successfully');
}

    /**
     * Show the form for editing the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function edit(Task $task)
{
    // Check if user is the owner of the project
    if ($task->project->owner_id !== Auth::id()) {
        return redirect()->route('owner.tasks.index')
            ->with('error', 'You can only edit tasks for your own projects.');
    }

    $user = Auth::user();
    $projects = $user->ownedProjects;
    
    // Get engineers and contractors separately
    $engineers = User::whereHas('roles', function($q) {
        $q->where('name', 'engineer');
    })->get();
    
    $contractors = User::whereHas('roles', function($q) {
        $q->where('name', 'contractor');
    })->get();
    
    return view('owner.tasks.edit', compact('task', 'projects', 'engineers', 'contractors'));
}

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {
        
        // Check if user is the owner of the project
        if ($task->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.tasks.index')
                ->with('error', 'You can only update tasks for your own projects.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,review,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        // Check if user is the owner of the new project
        $project = Project::find($request->project_id);
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.tasks.index')
                ->with('error', 'You can only assign tasks to your own projects.');
        }

        $oldStatus = $task->status;
        $newStatus = $request->status;

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => $newStatus,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
        ]);

        // Update completed_at timestamp if status changed to completed
        if ($oldStatus !== 'completed' && $newStatus === 'completed') {
            $task->update(['completed_at' => now()]);
        } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
            $task->update(['completed_at' => null]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Updated task: ' . $task->title,
            'model_type' => 'Task',
            'model_id' => $task->id,
        ]);

        return redirect()->route('owner.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        // Check if user is the owner of the project
        if ($task->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.tasks.index')
                ->with('error', 'You can only delete tasks for your own projects.');
        }

        // Log activity before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => 'Deleted task: ' . $task->title,
            'model_type' => 'Task',
            'model_id' => $task->id,
        ]);

        $task->delete();

        return redirect()->route('owner.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
 public function changeStatus(Request $request, Task $task)
{
    if ($task->project->owner_id !== auth()->id()) {
        return back()->with('error', 'You are not authorized to change this task status');
    }

    $validated = $request->validate([
        'status' => 'required|in:backlog,todo,in_progress,review,completed',
        'comment' => 'nullable|string'
    ]);

    $oldStatus = $task->status;
    $newStatus = $validated['status'];

    $task->update([
        'status' => $newStatus,
        'completed_at' => $newStatus === 'completed' ? now() : null
    ]);

    if ($validated['comment'] || $oldStatus !== $newStatus) {
        TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'comment' => $validated['comment'] ?? 'Status changed',
            'status_change' => $oldStatus !== $newStatus,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'hours_spent' => 0
        ]);
    }

    return redirect()->route('owner.tasks.show', $task)
           ->with('success', 'Task status updated successfully');
}
    
}

<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view tasks for this project.');
        }
        
        $tasks = $project->tasks()
            ->with(['assignedBy', 'assignedTo'])
            ->latest()
            ->get();
            
        // Group tasks by status for Kanban view
        $tasksByStatus = $tasks->groupBy('status');
        
        return view('projects.tasks.index', compact('project', 'tasksByStatus'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Only admin, project owner, and engineers can create tasks
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !($user->isEngineer() && $project->projectMembers()->where('user_id', $user->id)->exists())) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'You do not have permission to create tasks for this project.');
        }
        
        // Get contractors for this project for assignment
        $contractors = $project->projectMembers()
            ->whereHas('role', function($query) {
                $query->where('name', 'contractor');
            })
            ->with('user')
            ->get()
            ->pluck('user');
            
        return view('projects.tasks.create', compact('project', 'contractors'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Only admin, project owner, and engineers can create tasks
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !($user->isEngineer() && $project->projectMembers()->where('user_id', $user->id)->exists())) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'You do not have permission to create tasks for this project.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,review,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);
        
        // If assigned_to is provided, check if the user is a contractor in this project
        if ($request->assigned_to) {
            $isContractorInProject = $project->projectMembers()
                ->where('user_id', $request->assigned_to)
                ->whereHas('role', function($query) {
                    $query->where('name', 'contractor');
                })
                ->exists();
                
            if (!$isContractorInProject) {
                return redirect()->route('projects.tasks.create', $project)
                    ->with('error', 'The selected user is not a contractor in this project.')
                    ->withInput();
            }
        }
        
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $project->id,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
        ]);
        
        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        $task->load(['assignedBy', 'assignedTo', 'updates.user', 'updates.images']);
        
        return view('projects.tasks.show', compact('project', 'task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Only admin, project owner, task creator, and assigned contractor can edit tasks
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $task->assigned_by !== $user->id &&
            $task->assigned_to !== $user->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'You do not have permission to edit this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        // Get contractors for this project for assignment
        $contractors = $project->projectMembers()
            ->whereHas('role', function($query) {
                $query->where('name', 'contractor');
            })
            ->with('user')
            ->get()
            ->pluck('user');
            
        return view('projects.tasks.edit', compact('project', 'task', 'contractors'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Only admin, project owner, task creator, and assigned contractor can update tasks
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $task->assigned_by !== $user->id &&
            $task->assigned_to !== $user->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'You do not have permission to update this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:backlog,todo,in_progress,review,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);
        
        // If assigned_to is provided, check if the user is a contractor in this project
        if ($request->assigned_to) {
            $isContractorInProject = $project->projectMembers()
                ->where('user_id', $request->assigned_to)
                ->whereHas('role', function($query) {
                    $query->where('name', 'contractor');
                })
                ->exists();
                
            if (!$isContractorInProject) {
                return redirect()->route('projects.tasks.edit', [$project, $task])
                    ->with('error', 'The selected user is not a contractor in this project.')
                    ->withInput();
            }
        }
        
        // Update task
        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
        ]);
        
        // If status is changed to completed, set completed_at
        if ($request->status === 'completed' && $task->status !== 'completed') {
            $task->update([
                'completed_at' => now(),
            ]);
        } elseif ($request->status !== 'completed' && $task->status === 'completed') {
            $task->update([
                'completed_at' => null,
            ]);
        }
        
        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Only admin, project owner, and task creator can delete tasks
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $task->assigned_by !== $user->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'You do not have permission to delete this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        $task->delete();
        
        return redirect()->route('projects.tasks.index', $project)
            ->with('success', 'Task deleted successfully.');
    }
}

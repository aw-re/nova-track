<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\Report;
use App\Models\Resource;
use App\Models\ResourceRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
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
     * Display a listing of all projects.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $projects = Project::with('owner')
            ->withCount(['tasks', 'projectMembers'])
            ->latest()
            ->paginate(10);
            
        // Get all project owners for the filter dropdown
        $owners = User::whereHas('roles', function($query) {
            $query->where('name', 'project_owner');
        })->get();
        
        // Generate data for the status chart
        $projectsByStatus = [
            'planning' => Project::where('status', 'planning')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'on_hold' => Project::where('status', 'on_hold')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'cancelled' => Project::where('status', 'cancelled')->count()
        ];
        
        // Generate data for the owner chart
        $projectsByOwner = User::whereHas('roles', function($query) {
                $query->where('name', 'project_owner');
            })
            ->withCount('ownedProjects')
            ->having('owned_projects_count', '>', 0)
            ->get()
            ->map(function($owner) {
                return [
                    'name' => $owner->name,
                    'count' => $owner->owned_projects_count
                ];
            });
            
        return view('admin.projects.index', compact('projects', 'owners', 'projectsByStatus', 'projectsByOwner'));
    }

    /**
     * Show the form for creating a new project.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $owners = User::whereHas('roles', function($query) {
            $query->where('name', 'project_owner');
        })->get();
        
        return view('admin.projects.create', compact('owners'));
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'owner_id' => 'required|exists:users,id',
        ]);
        
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'status' => $request->status,
            'owner_id' => $request->owner_id,
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Created project: ' . $project->name,
            'model_type' => 'Project',
            'model_id' => $project->id,
        ]);
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function show(Project $project)
    {
        $project->load([
            'owner', 
            'projectMembers.user', 
            'projectMembers.role', 
            'tasks',
            'reports',
            'resourceRequests'
        ]);
        
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function edit(Project $project)
{
    $users = User::all(); // أو يمكنك تصفية المستخدمين حسب الدور إذا لزم الأمر
    return view('admin.projects.edit', compact('project', 'users'));
}

    /**
     * Update the specified project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'owner_id' => 'required|exists:users,id',
        ]);
        
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'status' => $request->status,
            'owner_id' => $request->owner_id,
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Updated project: ' . $project->name,
            'model_type' => 'Project',
            'model_id' => $project->id,
        ]);
        
        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        // Log activity before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => 'Deleted project: ' . $project->name,
            'model_type' => 'Project',
            'model_id' => $project->id,
        ]);
        
        $project->delete();
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Role;
use App\Models\User;
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all projects
            $projects = Project::with('owner')->latest()->paginate(10);
        } elseif ($user->isProjectOwner()) {
            // Project owner can see their own projects
            $projects = $user->ownedProjects()->with('owner')->latest()->paginate(10);
        } else {
            // Engineers and contractors can see projects they are members of
            $projects = $user->projects()->with('owner')->latest()->paginate(10);
        }
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        // Only admin and project owners can create projects
        if (!Auth::user()->isAdmin() && !Auth::user()->isProjectOwner()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create projects.');
        }
        
        return view('projects.create');
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        // Only admin and project owners can create projects
        if (!Auth::user()->isAdmin() && !Auth::user()->isProjectOwner()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create projects.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
        ]);
        
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'status' => $request->status,
            'owner_id' => Auth::id(),
        ]);
        
        // Add the owner as a project member with project_owner role
        $ownerRole = Role::where('name', 'project_owner')->first();
        
        if ($ownerRole) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => Auth::id(),
                'role_id' => $ownerRole->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);
        }
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view this project.');
        }
        
        $project->load(['owner', 'projectMembers.user', 'projectMembers.role', 'tasks']);
        
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $user = Auth::user();
        
        // Only admin and project owner can edit the project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to edit this project.');
        }
        
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Only admin and project owner can update the project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to update this project.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
        ]);
        
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'status' => $request->status,
        ]);
        
        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $user = Auth::user();
        
        // Only admin and project owner can delete the project
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to delete this project.');
        }
        
        $project->delete();
        
        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}

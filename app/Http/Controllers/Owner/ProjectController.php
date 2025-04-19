<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use App\Models\ProjectMember;
use App\Models\ProjectInvitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::where('owner_id', Auth::id())
            ->withCount('members', 'tasks')
            ->paginate(10);
            
        return view('owner.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('owner.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'location' => $request->location,
            'status' => $request->status,
            'owner_id' => Auth::id(),
        ]);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to view this project.');
        }

        $project->load([
            'members.user', 
            'tasks' => function($query) {
                $query->latest()->take(5);
            },
            'resourceRequests' => function($query) {
                $query->latest()->take(5);
            },
            'reports' => function($query) {
                $query->latest()->take(5);
            },
            'files' => function($query) {
                $query->latest()->take(5);
            }
        ]);

        // Get task statistics
        $taskStats = [
            'total' => $project->tasks->count(),
            'completed' => $project->tasks->where('status', 'completed')->count(),
            'in_progress' => $project->tasks->whereIn('status', ['in_progress', 'review'])->count(),
            'pending' => $project->tasks->whereIn('status', ['backlog', 'todo'])->count(),
        ];

        // Get engineers and contractors for assignment
        $engineers = User::where('role', 'engineer')
            ->orWhereHas('roles', function($query) {
                $query->where('name', 'engineer');
            })
            ->whereNotIn('id', function($query) use ($project) {
                $query->select('user_id')
                    ->from('project_members')
                    ->where('project_id', $project->id);
            })
            ->whereNotIn('id', function($query) use ($project) {
                $query->select('user_id')
                    ->from('project_invitations')
                    ->where('project_id', $project->id)
                    ->where('status', 'pending');
            })
            ->get();
        
        $contractors = User::where('role', 'contractor')
            ->orWhereHas('roles', function($query) {
                $query->where('name', 'contractor');
            })
            ->whereNotIn('id', function($query) use ($project) {
                $query->select('user_id')
                    ->from('project_members')
                    ->where('project_id', $project->id);
            })
            ->whereNotIn('id', function($query) use ($project) {
                $query->select('user_id')
                    ->from('project_invitations')
                    ->where('project_id', $project->id)
                    ->where('status', 'pending');
            })
            ->get();

        return view('owner.projects.show', compact('project', 'taskStats', 'engineers', 'contractors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to edit this project.');
        }

        return view('owner.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to update this project.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'budget' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'budget' => $request->budget,
            'location' => $request->location,
            'status' => $request->status,
        ]);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to delete this project.');
        }

        // Check if the project has tasks, reports, or resource requests
        if ($project->tasks()->count() > 0 || $project->reports()->count() > 0 || $project->resourceRequests()->count() > 0) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'Cannot delete project with existing tasks, reports, or resource requests.');
        }

        // Delete project members
        $project->members()->delete();
        
        // Delete project files
        foreach ($project->files as $file) {
            // Delete the physical file
            if ($file->file_path) {
                \Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }
        
        $project->delete();

        return redirect()->route('owner.projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    /**
     * Invite members to the project.
     */
    public function inviteMembers(Request $request, Project $project)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to invite members to this project.');
        }

        $request->validate([
            'engineers' => 'nullable|array',
            'engineers.*' => 'exists:users,id',
            'contractors' => 'nullable|array',
            'contractors.*' => 'exists:users,id',
        ]);

        // Process engineers
        if ($request->has('engineers') && is_array($request->engineers)) {
            foreach ($request->engineers as $userId) {
                // Check if user is an engineer
                $user = User::find($userId);
                if ($user && $user->hasRole('engineer')) {
                    // Check if already a member or has a pending invitation
                    $existingMember = ProjectMember::where('project_id', $project->id)
                        ->where('user_id', $userId)
                        ->first();
                        
                    $existingInvitation = ProjectInvitation::where('project_id', $project->id)
                        ->where('user_id', $userId)
                        ->where('status', 'pending')
                        ->first();
                        
                    if (!$existingMember && !$existingInvitation) {
                        ProjectInvitation::create([
                            'project_id' => $project->id,
                            'user_id' => $userId,
                            'invited_by' => Auth::id(),
                            'status' => 'pending',
                            'role' => 'engineer',
                        ]);
                    }
                }
            }
        }

        // Process contractors
        if ($request->has('contractors') && is_array($request->contractors)) {
            foreach ($request->contractors as $userId) {
                // Check if user is a contractor
                $user = User::find($userId);
                if ($user && $user->hasRole('contractor')) {
                    // Check if already a member or has a pending invitation
                    $existingMember = ProjectMember::where('project_id', $project->id)
                        ->where('user_id', $userId)
                        ->first();
                        
                    $existingInvitation = ProjectInvitation::where('project_id', $project->id)
                        ->where('user_id', $userId)
                        ->where('status', 'pending')
                        ->first();
                        
                    if (!$existingMember && !$existingInvitation) {
                        ProjectInvitation::create([
                            'project_id' => $project->id,
                            'user_id' => $userId,
                            'invited_by' => Auth::id(),
                            'status' => 'pending',
                            'role' => 'contractor',
                        ]);
                    }
                }
            }
        }

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Invitations sent successfully.');
    }

    /**
     * Remove a member from the project.
     */
    public function removeMember(Project $project, User $user)
    {
        // Check if the authenticated user is the owner of the project
        if ($project->owner_id !== Auth::id()) {
            return redirect()->route('owner.projects.index')
                ->with('error', 'You do not have permission to remove members from this project.');
        }

        // Check if the user is a member of the project
        $member = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->first();

        if ($member) {
            // Check if the member has assigned tasks
            $assignedTasks = $project->tasks()->where('assigned_to', $user->id)->count();
            if ($assignedTasks > 0) {
                return redirect()->route('owner.projects.show', $project)
                    ->with('error', 'Cannot remove member with assigned tasks. Please reassign tasks first.');
            }

            $member->delete();
            return redirect()->route('owner.projects.show', $project)
                ->with('success', 'Member removed successfully.');
        }

        return redirect()->route('owner.projects.show', $project)
            ->with('error', 'User is not a member of this project.');
    }
}

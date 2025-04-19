<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\ResourceRequest;
use App\Models\ProjectInvitation;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:contractor']);
    }

    /**
     * Show the contractor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get projects the contractor is a member of
        $projects = $user->projects()
            ->with('owner')
            ->get();
            
        // Get tasks assigned to the contractor
        $assignedTasks = $user->assignedTasks()
            ->with(['project', 'assignedBy'])
            ->latest()
            ->get();
            
        // Get resource requests made by the contractor
        $resourceRequests = $user->resourceRequests()
            ->with(['project'])
            ->latest()
            ->take(5)
            ->get();
            
        // Get counts for dashboard statistics
        $projectCount = $projects->count();
        $taskCount = $assignedTasks->count();
        $assignedTaskCount = $assignedTasks->count(); // Same as taskCount for contractors
        $resourceRequestCount = $user->resourceRequests()->count();
        $pendingTaskCount = $assignedTasks->whereIn('status', ['backlog', 'todo'])->count();
        $inProgressTaskCount = $assignedTasks->where('status', 'in_progress')->count();
        $completedTaskCount = $assignedTasks->where('status', 'completed')->count();
        
        // Get recent task updates
        $recentUpdates = TaskUpdate::where('user_id', $user->id)
            ->with('task')
            ->latest()
            ->take(5)
            ->get();
            
        // Get recent tasks for display in the dashboard
        $recentTasks = $assignedTasks->take(5);
        
        // Get tasks by status for the tabs
        $pendingTasks = $assignedTasks->whereIn('status', ['backlog', 'todo'])->take(5);
        $inProgressTasks = $assignedTasks->where('status', 'in_progress')->take(5);
        $completedTasks = $assignedTasks->where('status', 'completed')->take(5);
        
        // Get pending project invitations
        $pendingInvitations = ProjectInvitation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('project', 'invitedBy')
            ->latest()
            ->get();
            
        $invitationCount = $pendingInvitations->count();
        
        return view('contractor.dashboard', compact(
            'projects',
            'assignedTasks',
            'resourceRequests',
            'projectCount',
            'taskCount',
            'assignedTaskCount',
            'resourceRequestCount',
            'pendingTaskCount',
            'inProgressTaskCount',
            'completedTaskCount',
            'recentUpdates',
            'recentTasks',
            'pendingTasks',
            'inProgressTasks',
            'completedTasks',
            'pendingInvitations',
            'invitationCount'
        ));
    }
    
    /**
     * Show all project invitations for the contractor.
     *
     * @return \Illuminate\View\View
     */
    public function invitations()
    {
        $user = Auth::user();
        
        // Get all project invitations for the user
        $pendingInvitations = ProjectInvitation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('project', 'invitedBy')
            ->latest()
            ->get();
            
        $acceptedInvitations = ProjectInvitation::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with('project', 'invitedBy')
            ->latest()
            ->get();
            
        $rejectedInvitations = ProjectInvitation::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->with('project', 'invitedBy')
            ->latest()
            ->get();
            
        return view('contractor.invitations.index', compact(
            'pendingInvitations',
            'acceptedInvitations',
            'rejectedInvitations'
        ));
    }
    
    /**
     * Accept a project invitation.
     *
     * @param  \App\Models\ProjectInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInvitation(ProjectInvitation $invitation)
    {
        $user = Auth::user();
        
        // Check if the invitation belongs to the authenticated user
        if ($invitation->user_id !== $user->id) {
            return redirect()->route('contractor.invitations.index')
                ->with('error', 'You do not have permission to accept this invitation.');
        }
        
        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('contractor.invitations.index')
                ->with('error', 'This invitation has already been processed.');
        }
        
        // Create a new project member
        ProjectMember::create([
            'project_id' => $invitation->project_id,
            'user_id' => $user->id,
            'role' => $invitation->role,
            'added_by' => $invitation->invited_by,
            'joined_at' => now(),
        ]);
        
        // Update the invitation status
        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
        
        return redirect()->route('contractor.invitations.index')
            ->with('success', 'You have successfully joined the project.');
    }
    
    /**
     * Reject a project invitation.
     *
     * @param  \App\Models\ProjectInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectInvitation(ProjectInvitation $invitation)
    {
        $user = Auth::user();
        
        // Check if the invitation belongs to the authenticated user
        if ($invitation->user_id !== $user->id) {
            return redirect()->route('contractor.invitations.index')
                ->with('error', 'You do not have permission to reject this invitation.');
        }
        
        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('contractor.invitations.index')
                ->with('error', 'This invitation has already been processed.');
        }
        
        // Update the invitation status
        $invitation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
        
        return redirect()->route('contractor.invitations.index')
            ->with('success', 'You have declined the project invitation.');
    }
    
    /**
     * Show all projects the contractor is a member of.
     *
     * @return \Illuminate\View\View
     */
    public function projects()
    {
        $user = Auth::user();
        
        // Get projects the contractor is a member of
        $projects = $user->projects()
            ->with('owner')
            ->latest()
            ->paginate(10);
            
        return view('contractor.projects.index', compact('projects'));
    }
    
    /**
     * Show a specific project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\View\View
     */
    public function showProject(Project $project)
    {
        $user = Auth::user();
        
        // Check if the user is a member of the project
        $isMember = $project->members()->where('user_id', $user->id)->exists();
        
        if (!$isMember) {
            return redirect()->route('contractor.projects.index')
                ->with('error', 'You do not have permission to view this project.');
        }
        
        $project->load([
            'members.user', 
            'tasks' => function($query) use ($user) {
                $query->where('assigned_to', $user->id);
            },
            'resourceRequests' => function($query) use ($user) {
                $query->where('requested_by', $user->id);
            },
            'files'
        ]);
        
        return view('contractor.projects.show', compact('project'));
    }
}

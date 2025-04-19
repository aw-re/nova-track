<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Report;
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
        $this->middleware(['auth', 'role:engineer']);
    }

    /**
     * Show the engineer dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get projects the engineer is a member of
        $projects = $user->projects()
            ->with('owner')
            ->get();
            
        // Get tasks created by the engineer
        $createdTasks = $user->createdTasks()
            ->with(['project', 'assignedTo'])
            ->latest()
            ->get();
            
        // Get tasks assigned to the engineer
        $assignedTasks = $user->assignedTasks()
            ->with(['project', 'assignedBy'])
            ->latest()
            ->get();
            
        // Get all tasks for the engineer (both created and assigned)
        $allTasks = $createdTasks->merge($assignedTasks);
            
        // Get counts for dashboard statistics
        $projectCount = $projects->count();
        $assignedProjectCount = $projectCount; // Same as projectCount for engineers
        $taskCount = $createdTasks->count();
        $createdTaskCount = $taskCount; // Same as taskCount
        $assignedTaskCount = $assignedTasks->count();
        $pendingTaskCount = $createdTasks->whereIn('status', ['backlog', 'todo'])->count();
        $inProgressTaskCount = $createdTasks->where('status', 'in_progress')->count();
        $completedTaskCount = $createdTasks->where('status', 'completed')->count();
        
        // Get recent tasks
        $recentTasks = $assignedTasks->take(5);
        
        // Get assigned projects for display
        $assignedProjects = $projects->take(5);
        
        // Get reports created by the engineer
        $reports = Report::where('created_by', $user->id)
            ->with('project')
            ->latest()
            ->get();
            
        $recentReports = $reports->take(5);
        $submittedReportCount = $reports->count();
        
        // Get resource requests created by the engineer
        $resourceRequests = ResourceRequest::where('requested_by', $user->id)
            ->with('project')
            ->latest()
            ->get();
            
        $recentResourceRequests = $resourceRequests->take(5);
        
        // Get pending project invitations
        $pendingInvitations = ProjectInvitation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('project', 'invitedBy')
            ->latest()
            ->get();
            
        $invitationCount = $pendingInvitations->count();
        
        return view('engineer.dashboard', compact(
            'projects',
            'createdTasks',
            'assignedTasks',
            'allTasks',
            'projectCount',
            'assignedProjectCount',
            'taskCount',
            'createdTaskCount',
            'assignedTaskCount',
            'pendingTaskCount',
            'inProgressTaskCount',
            'completedTaskCount',
            'recentTasks',
            'assignedProjects',
            'recentReports',
            'submittedReportCount',
            'recentResourceRequests',
            'pendingInvitations',
            'invitationCount'
        ));
    }
    
    /**
     * Show all project invitations for the engineer.
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
            
        return view('engineer.invitations.index', compact(
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
            return redirect()->route('engineer.invitations.index')
                ->with('error', 'You do not have permission to accept this invitation.');
        }
        
        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('engineer.invitations.index')
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
        
        return redirect()->route('engineer.invitations.index')
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
            return redirect()->route('engineer.invitations.index')
                ->with('error', 'You do not have permission to reject this invitation.');
        }
        
        // Check if the invitation is still pending
        if ($invitation->status !== 'pending') {
            return redirect()->route('engineer.invitations.index')
                ->with('error', 'This invitation has already been processed.');
        }
        
        // Update the invitation status
        $invitation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);
        
        return redirect()->route('engineer.invitations.index')
            ->with('success', 'You have declined the project invitation.');
    }
    
    /**
     * Show all projects the engineer is a member of.
     *
     * @return \Illuminate\View\View
     */
    public function projects()
    {
        $user = Auth::user();
        
        // Get projects the engineer is a member of
        $projects = $user->projects()
            ->with('owner')
            ->latest()
            ->paginate(10);
            
        return view('engineer.projects.index', compact('projects'));
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
            return redirect()->route('engineer.projects.index')
                ->with('error', 'You do not have permission to view this project.');
        }
        
        $project->load([
            'members.user', 
            'tasks' => function($query) use ($user) {
                $query->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            },
            'resourceRequests' => function($query) use ($user) {
                $query->where('requested_by', $user->id);
            },
            'reports' => function($query) use ($user) {
                $query->where('created_by', $user->id);
            },
            'files'
        ]);
        
        return view('engineer.projects.show', compact('project'));
    }
}

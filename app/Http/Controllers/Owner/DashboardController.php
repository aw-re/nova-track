<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Report;
use App\Models\ResourceRequest;
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
        $this->middleware(['auth', 'role:project_owner']);
    }

    /**
     * Show the project owner dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get projects owned by the user
        $projects = $user->ownedProjects()
            ->withCount(['tasks', 'projectMembers'])
            ->latest()
            ->get();
            
        // Get counts for dashboard statistics
        $projectCount = $projects->count();
        $activeProjectCount = $projects->where('status', 'in_progress')->count();
        $completedProjectCount = $projects->where('status', 'completed')->count();
        
        // Get tasks from all owned projects
        $projectIds = $projects->pluck('id')->toArray();
        $taskCount = Task::whereIn('project_id', $projectIds)->count();
        $completedTaskCount = Task::whereIn('project_id', $projectIds)
            ->where('status', 'completed')
            ->count();
            
        // Get recent projects
        $recentProjects = $projects->take(5);
        
        // Get recent tasks
        $recentTasks = Task::whereIn('project_id', $projectIds)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();
            
        // Get pending reports
        $pendingReports = Report::whereIn('project_id', $projectIds)
            ->where('status', 'pending')
            ->with(['project', 'createdBy'])
            ->latest()
            ->take(5)
            ->get();
            
        $pendingReportCount = Report::whereIn('project_id', $projectIds)
            ->where('status', 'pending')
            ->count();
            
        // Get pending resource requests
        $pendingResourceRequests = ResourceRequest::whereIn('project_id', $projectIds)
            ->where('status', 'pending')
            ->with(['project', 'requestedBy'])
            ->latest()
            ->take(5)
            ->get();
            
        $pendingResourceRequestCount = ResourceRequest::whereIn('project_id', $projectIds)
            ->where('status', 'pending')
            ->count();
        
        return view('owner.dashboard', compact(
            'projects',
            'projectCount',
            'activeProjectCount',
            'completedProjectCount',
            'taskCount',
            'completedTaskCount',
            'recentProjects',
            'recentTasks',
            'pendingReports',
            'pendingReportCount',
            'pendingResourceRequests',
            'pendingResourceRequestCount'
        ));
    }
}

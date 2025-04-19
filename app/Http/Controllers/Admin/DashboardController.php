<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\ActivityLog;

class DashboardController extends Controller
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
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard statistics
        $userCount = User::count();
        $projectCount = Project::count();
        $taskCount = Task::count();
        $completedTaskCount = Task::where('status', 'completed')->count();
        
        // Get recent activity logs
        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Get user distribution by role
        $usersByRole = User::with('roles')
            ->get()
            ->groupBy(function($user) {
                return $user->roles->first()->name ?? 'unknown';
            })
            ->map(function($users) {
                return count($users);
            });
            
        return view('admin.dashboard', compact(
            'userCount', 
            'projectCount', 
            'taskCount', 
            'completedTaskCount', 
            'recentActivities',
            'usersByRole'
        ));
    }
}

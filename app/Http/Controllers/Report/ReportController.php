<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
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
     * Display a listing of the reports.
     */
    public function index(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view reports for this project.');
        }
        
        $reports = $project->reports()
            ->with(['createdBy', 'approvedBy'])
            ->latest()
            ->paginate(10);
            
        return view('projects.reports.index', compact('project', 'reports'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create reports for this project.');
        }
        
        return view('projects.reports.create', compact('project'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create reports for this project.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:daily,weekly,monthly,progress,final',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);
        
        $report = Report::create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'status' => $request->status,
            'submitted_at' => $request->status === 'draft' ? null : now(),
        ]);
        
        return redirect()->route('projects.reports.show', [$project, $report])
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified report.
     */
    public function show(Project $project, Report $report)
    {
        $user = Auth::user();
        
        // Check if report belongs to the project
        if ($report->project_id !== $project->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'The report does not belong to this project.');
        }
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view reports for this project.');
        }
        
        $report->load(['createdBy', 'approvedBy']);
        
        return view('projects.reports.show', compact('project', 'report'));
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit(Project $project, Report $report)
    {
        $user = Auth::user();
        
        // Check if report belongs to the project
        if ($report->project_id !== $project->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'The report does not belong to this project.');
        }
        
        // Only the creator, project owner, and admin can edit reports
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $report->created_by !== $user->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'You do not have permission to edit this report.');
        }
        
        // Cannot edit if already approved or rejected
        if (in_array($report->status, ['approved', 'rejected'])) {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'Cannot edit report with status: ' . $report->status);
        }
        
        return view('projects.reports.edit', compact('project', 'report'));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, Project $project, Report $report)
    {
        $user = Auth::user();
        
        // Check if report belongs to the project
        if ($report->project_id !== $project->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'The report does not belong to this project.');
        }
        
        // Only the creator, project owner, and admin can update reports
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $report->created_by !== $user->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'You do not have permission to update this report.');
        }
        
        // Cannot update if already approved or rejected
        if (in_array($report->status, ['approved', 'rejected'])) {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'Cannot update report with status: ' . $report->status);
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:daily,weekly,monthly,progress,final',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);
        
        // Update submitted_at if status changes from draft to submitted
        $submitted_at = $report->submitted_at;
        if ($report->status === 'draft' && $request->status === 'submitted') {
            $submitted_at = now();
        }
        
        $report->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'status' => $request->status,
            'submitted_at' => $submitted_at,
        ]);
        
        return redirect()->route('projects.reports.show', [$project, $report])
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy(Project $project, Report $report)
    {
        $user = Auth::user();
        
        // Check if report belongs to the project
        if ($report->project_id !== $project->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'The report does not belong to this project.');
        }
        
        // Only the creator, project owner, and admin can delete reports
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $report->created_by !== $user->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'You do not have permission to delete this report.');
        }
        
        // Cannot delete if already approved
        if ($report->status === 'approved') {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'Cannot delete approved report.');
        }
        
        $report->delete();
        
        return redirect()->route('projects.reports.index', $project)
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Generate a progress report for the project.
     */
    public function generateProgress(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to generate reports for this project.');
        }
        
        // Get project statistics
        $totalTasks = $project->tasks()->count();
        $completedTasks = $project->tasks()->where('status', 'completed')->count();
        $inProgressTasks = $project->tasks()->where('status', 'in_progress')->count();
        $pendingTasks = $project->tasks()->whereIn('status', ['backlog', 'todo'])->count();
        
        // Calculate progress percentage
        $progressPercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        // Get recent task updates
        $recentUpdates = $project->tasks()
            ->with(['updates' => function($query) {
                $query->latest()->take(5);
            }, 'updates.user'])
            ->get()
            ->pluck('updates')
            ->flatten()
            ->sortByDesc('created_at')
            ->take(10);
            
        // Generate report content
        $content = "# Project Progress Report\n\n";
        $content .= "## Project Overview\n";
        $content .= "- **Project Name:** {$project->name}\n";
        $content .= "- **Start Date:** " . ($project->start_date ? $project->start_date->format('Y-m-d') : 'Not set') . "\n";
        $content .= "- **End Date:** " . ($project->end_date ? $project->end_date->format('Y-m-d') : 'Not set') . "\n";
        $content .= "- **Status:** " . ucfirst($project->status) . "\n";
        $content .= "- **Overall Progress:** {$progressPercentage}%\n\n";
        
        $content .= "## Task Statistics\n";
        $content .= "- **Total Tasks:** {$totalTasks}\n";
        $content .= "- **Completed Tasks:** {$completedTasks}\n";
        $content .= "- **In Progress Tasks:** {$inProgressTasks}\n";
        $content .= "- **Pending Tasks:** {$pendingTasks}\n\n";
        
        $content .= "## Recent Updates\n";
        if ($recentUpdates->count() > 0) {
            foreach ($recentUpdates as $update) {
                $task = Task::find($update->task_id);
                $content .= "- **" . $update->created_at->format('Y-m-d') . "** - ";
                $content .= $update->user->name . " updated task \"" . $task->title . "\": ";
                $content .= substr($update->description, 0, 100) . (strlen($update->description) > 100 ? '...' : '') . "\n";
            }
        } else {
            $content .= "No recent updates found.\n";
        }
        
        // Create the report
        $report = Report::create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'title' => 'Progress Report - ' . now()->format('Y-m-d'),
            'content' => $content,
            'type' => 'progress',
            'status' => 'draft',
        ]);
        
        return redirect()->route('projects.reports.edit', [$project, $report])
            ->with('success', 'Progress report generated successfully. Please review and submit.');
    }

    /**
     * Approve the specified report.
     */
    public function approve(Request $request, Report $report)
{
    $validated = $request->validate([
        'comment' => 'nullable|string|max:255'
    ]);

    $report->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approver_id' => auth()->id(),
        'comments' => $validated['comment'] ?? null
    ]);

    return redirect()->route('owner.reports.index')
        ->with('success', 'Report approved successfully');
}
    /**
     * Reject the specified report.
     */
    public function reject(Request $request, Project $project, Report $report)
    {
        $user = Auth::user();
        
        // Check if report belongs to the project
        if ($report->project_id !== $project->id) {
            return redirect()->route('projects.reports.index', $project)
                ->with('error', 'The report does not belong to this project.');
        }
        
        // Only project owner and admin can reject reports
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'You do not have permission to reject this report.');
        }
        
        // Cannot reject if not submitted
        if ($report->status !== 'submitted') {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'Cannot reject report with status: ' . $report->status);
        }
        
        // Cannot reject own report
        if ($report->created_by === $user->id) {
            return redirect()->route('projects.reports.show', [$project, $report])
                ->with('error', 'You cannot reject your own report.');
        }
        
        $report->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('projects.reports.show', [$project, $report])
            ->with('success', 'Report rejected successfully.');
    }
}

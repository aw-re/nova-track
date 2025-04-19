<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
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
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the reports.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
{
    $query = Report::with(['project', 'submittedBy']);
    
    // فلترة حسب البحث
    if ($request->has('search')) {
        $query->where('title', 'like', '%'.$request->search.'%');
    }
    
    // فلترة حسب المشروع
    if ($request->has('project_id')) {
        $query->where('project_id', $request->project_id);
    }
    
    // فلترة حسب النوع
    if ($request->has('type')) {
        $query->where('type', $request->type);
    }
    
    $reports = $query->paginate(10);
    $projects = Project::all(); // جلب جميع المشاريع
    
    // إحصائيات لأنواع التقارير
    $reportsByType = Report::select('type', DB::raw('count(*) as total'))
                          ->groupBy('type')
                          ->pluck('total', 'type');
    
    // إحصائيات لحالات التقارير
    $reportsByStatus = Report::select('status', DB::raw('count(*) as total'))
                            ->groupBy('status')
                            ->pluck('total', 'status');
    
    return view('admin.reports.index', compact('reports', 'projects', 'reportsByType', 'reportsByStatus'));
}

    /**
     * Show the form for creating a new report.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $projects = Project::all();
        return view('admin.reports.create', compact('projects'));
    }

    /**
     * Store a newly created report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:daily,weekly,monthly,progress,final',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);

        $report = Report::create([
            'project_id' => $request->project_id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'status' => $request->status,
            'submitted_at' => $request->status !== 'draft' ? now() : null,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Created report: ' . $report->title,
            'model_type' => 'Report',
            'model_id' => $report->id,
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified report.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\View\View
     */
    public function show(Report $report)
    {
        $report->load(['project', 'createdBy', 'approvedBy']);
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified report.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\View\View
     */
    public function edit(Report $report)
    {
        $projects = Project::all();
        return view('admin.reports.edit', compact('report', 'projects'));
    }

    /**
     * Update the specified report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:daily,weekly,monthly,progress,final',
            'status' => 'required|in:draft,submitted,approved,rejected',
        ]);

        $oldStatus = $report->status;
        $newStatus = $request->status;

        $report->update([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'status' => $newStatus,
        ]);

        // Update timestamps based on status changes
        if ($oldStatus === 'draft' && in_array($newStatus, ['submitted', 'approved', 'rejected'])) {
            $report->update(['submitted_at' => now()]);
        }

        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            $report->update([
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        } elseif ($newStatus !== 'approved' && $oldStatus === 'approved') {
            $report->update([
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'description' => 'Updated report: ' . $report->title,
            'model_type' => 'Report',
            'model_id' => $report->id,
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Report $report)
    {
        // Log activity before deletion
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => 'Deleted report: ' . $report->title,
            'model_type' => 'Report',
            'model_id' => $report->id,
        ]);

        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Approve the specified report.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Report $report)
    {
        if ($report->status !== 'submitted') {
            return redirect()->route('admin.reports.show', $report)
                ->with('error', 'Only submitted reports can be approved.');
        }

        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'approved',
            'description' => 'Approved report: ' . $report->title,
            'model_type' => 'Report',
            'model_id' => $report->id,
        ]);

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Report approved successfully.');
    }

    /**
     * Reject the specified report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Report $report)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        if ($report->status !== 'submitted') {
            return redirect()->route('admin.reports.show', $report)
                ->with('error', 'Only submitted reports can be rejected.');
        }

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'description' => 'Rejected report: ' . $report->title,
            'model_type' => 'Report',
            'model_id' => $report->id,
        ]);

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Report rejected successfully.');
    }
}

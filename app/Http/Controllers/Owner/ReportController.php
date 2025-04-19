<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendingReports = Report::with(['project', 'createdBy'])
            ->whereHas('project', function($query) {
                $query->where('owner_id', Auth::id());
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');
            
        $approvedReports = Report::with(['project', 'createdBy', 'approvedBy'])
            ->whereHas('project', function($query) {
                $query->where('owner_id', Auth::id());
            })
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'approved_page');
            
        $rejectedReports = Report::with(['project', 'createdBy', 'approvedBy'])
            ->whereHas('project', function($query) {
                $query->where('owner_id', Auth::id());
            })
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'rejected_page');
            
        return view('owner.reports.index', compact('pendingReports', 'approvedReports', 'rejectedReports'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Check if the owner owns the project
        if ($report->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.reports.index')
                ->with('error', 'You do not have permission to view this report.');
        }

        $report->load(['project', 'createdBy', 'approvedBy']);
        
        return view('owner.reports.show', compact('report'));
    }

    /**
     * Approve a report.
     */
    public function approve(Request $request, Report $report)
    {
        // Check if the owner owns the project
        if ($report->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.reports.index')
                ->with('error', 'You do not have permission to approve this report.');
        }

        // Check if the report is pending
        if ($report->status !== 'pending') {
            return redirect()->route('owner.reports.show', $report)
                ->with('error', 'This report has already been processed.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $report->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'feedback' => $request->approval_notes,
        ]);

        return redirect()->route('owner.reports.show', $report)
            ->with('success', 'Report approved successfully.');
    }

    /**
     * Reject a report.
     */
    public function reject(Request $request, Report $report)
    {
        // Check if the owner owns the project
        if ($report->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.reports.index')
                ->with('error', 'You do not have permission to reject this report.');
        }

        // Check if the report is pending
        if ($report->status !== 'pending') {
            return redirect()->route('owner.reports.show', $report)
                ->with('error', 'This report has already been processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $report->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(), // Still record who rejected it
            'approved_at' => now(),
            'feedback' => $request->rejection_reason,
        ]);

        return redirect()->route('owner.reports.show', $report)
            ->with('success', 'Report rejected successfully.');
    }
}

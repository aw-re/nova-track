<?php

namespace App\Http\Controllers\Engineer;

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
        $reports = Report::with(['project', 'createdBy', 'approvedBy'])
            ->where('created_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('engineer.reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get projects where the engineer is a member
        $projects = Project::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        return view('engineer.reports.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'report_type' => 'required|in:progress,technical,incident,final',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        // Verify the engineer is a member of the project
        $isMember = Project::find($request->project_id)
            ->members()
            ->where('user_id', Auth::id())
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('engineer.reports.create')
                ->with('error', 'You are not a member of this project.');
        }

        $reportData = [
            'title' => $request->title,
            'content' => $request->content,
            'project_id' => $request->project_id,
            'created_by' => Auth::id(),
            'report_type' => $request->report_type,
            'status' => 'pending',
        ];

        // Handle file upload
        if ($request->hasFile('report_file')) {
            $path = $request->file('report_file')->store('reports', 'public');
            $reportData['file_path'] = $path;
        }

        $report = Report::create($reportData);

        return redirect()->route('engineer.reports.show', $report)
            ->with('success', 'Report submitted successfully and is pending approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        // Check if the engineer created the report
        if ($report->created_by !== Auth::id()) {
            return redirect()->route('engineer.reports.index')
                ->with('error', 'You do not have permission to view this report.');
        }

        $report->load(['project', 'createdBy', 'approvedBy']);
        
        return view('engineer.reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        // Check if the engineer created the report and it's still pending
        if ($report->created_by !== Auth::id()) {
            return redirect()->route('engineer.reports.index')
                ->with('error', 'You do not have permission to edit this report.');
        }

        if ($report->status !== 'pending') {
            return redirect()->route('engineer.reports.show', $report)
                ->with('error', 'You cannot edit a report that has already been approved or rejected.');
        }

        // Get projects where the engineer is a member
        $projects = Project::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        return view('engineer.reports.edit', compact('report', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        // Check if the engineer created the report and it's still pending
        if ($report->created_by !== Auth::id()) {
            return redirect()->route('engineer.reports.index')
                ->with('error', 'You do not have permission to update this report.');
        }

        if ($report->status !== 'pending') {
            return redirect()->route('engineer.reports.show', $report)
                ->with('error', 'You cannot update a report that has already been approved or rejected.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'project_id' => 'required|exists:projects,id',
            'report_type' => 'required|in:progress,technical,incident,final',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        ]);

        // Verify the engineer is a member of the project
        $isMember = Project::find($request->project_id)
            ->members()
            ->where('user_id', Auth::id())
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('engineer.reports.edit', $report)
                ->with('error', 'You are not a member of this project.');
        }

        $reportData = [
            'title' => $request->title,
            'content' => $request->content,
            'project_id' => $request->project_id,
            'report_type' => $request->report_type,
            'status' => 'pending', // Reset to pending if it was rejected
        ];

        // Handle file upload
        if ($request->hasFile('report_file')) {
            // Delete old file if exists
            if ($report->file_path) {
                Storage::disk('public')->delete($report->file_path);
            }
            $path = $request->file('report_file')->store('reports', 'public');
            $reportData['file_path'] = $path;
        }

        $report->update($reportData);

        return redirect()->route('engineer.reports.show', $report)
            ->with('success', 'Report updated successfully and is pending approval.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        // Check if the engineer created the report and it's still pending
        if ($report->created_by !== Auth::id()) {
            return redirect()->route('engineer.reports.index')
                ->with('error', 'You do not have permission to delete this report.');
        }

        if ($report->status !== 'pending') {
            return redirect()->route('engineer.reports.show', $report)
                ->with('error', 'You cannot delete a report that has already been approved or rejected.');
        }

        // Delete file if exists
        if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }

        $report->delete();

        return redirect()->route('engineer.reports.index')
            ->with('success', 'Report deleted successfully.');
    }
}

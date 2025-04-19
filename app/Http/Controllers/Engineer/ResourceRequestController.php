<?php

namespace App\Http\Controllers\Engineer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResourceRequest;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResourceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all requests as a collection for filtering in the view
        $allRequests = ResourceRequest::with(['project'])
            ->where('requested_by', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        $pendingRequests = ResourceRequest::with(['project'])
            ->where('requested_by', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'pending_page');
            
        $fulfilledRequests = ResourceRequest::with(['project'])
            ->where('requested_by', Auth::id())
            ->where('status', 'fulfilled')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'fulfilled_page');
            
        $rejectedRequests = ResourceRequest::with(['project'])
            ->where('requested_by', Auth::id())
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'rejected_page');
            
        return view('engineer.resource-requests.index', compact('allRequests', 'pendingRequests', 'fulfilledRequests', 'rejectedRequests'));
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
        
        return view('engineer.resource-requests.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'resource_type' => 'required|in:material,equipment,labor',
            'resource_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'required_by' => 'required|date|after:today',
            'description' => 'required|string',
            'supporting_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // Verify the engineer is a member of the project
        $project = Project::find($request->project_id);
        if (!$project) {
            return redirect()->route('engineer.resource-requests.create')
                ->with('error', 'Project not found.');
        }
        
        $isMember = $project->members()
            ->where('user_id', Auth::id())
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('engineer.resource-requests.create')
                ->with('error', 'You are not a member of this project.');
        }

        $requestData = [
            'project_id' => $request->project_id,
            'requested_by' => Auth::id(),
            'resource_type' => $request->resource_type,
            'resource_name' => $request->resource_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'required_by' => $request->required_by,
            'description' => $request->description,
            'status' => 'pending',
        ];

        // Handle file upload
        if ($request->hasFile('supporting_document')) {
            $path = $request->file('supporting_document')->store('resource_requests', 'public');
            $requestData['document_path'] = $path;
        }

        $resourceRequest = ResourceRequest::create($requestData);

        return redirect()->route('engineer.resource-requests.show', $resourceRequest)
            ->with('success', 'Resource request submitted successfully and is pending approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceRequest $resourceRequest)
    {
        // Check if the engineer created the request
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('engineer.resource-requests.index')
                ->with('error', 'You do not have permission to view this resource request.');
        }

        $resourceRequest->load(['project', 'requestedBy', 'approvedBy']);
        
        return view('engineer.resource-requests.show', compact('resourceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceRequest $resourceRequest)
    {
        // Check if the engineer created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('engineer.resource-requests.index')
                ->with('error', 'You do not have permission to edit this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('engineer.resource-requests.show', $resourceRequest)
                ->with('error', 'You cannot edit a resource request that has already been processed.');
        }

        // Get projects where the engineer is a member
        $projects = Project::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        return view('engineer.resource-requests.edit', compact('resourceRequest', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceRequest $resourceRequest)
    {
        // Check if the engineer created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('engineer.resource-requests.index')
                ->with('error', 'You do not have permission to update this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('engineer.resource-requests.show', $resourceRequest)
                ->with('error', 'You cannot update a resource request that has already been processed.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'resource_type' => 'required|in:material,equipment,labor',
            'resource_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'required_by' => 'required|date|after:today',
            'description' => 'required|string',
            'supporting_document' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        // Verify the engineer is a member of the project
        $project = Project::find($request->project_id);
        if (!$project) {
            return redirect()->route('engineer.resource-requests.edit', $resourceRequest)
                ->with('error', 'Project not found.');
        }
        
        $isMember = $project->members()
            ->where('user_id', Auth::id())
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('engineer.resource-requests.edit', $resourceRequest)
                ->with('error', 'You are not a member of this project.');
        }

        $requestData = [
            'project_id' => $request->project_id,
            'resource_type' => $request->resource_type,
            'resource_name' => $request->resource_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'required_by' => $request->required_by,
            'description' => $request->description,
        ];

        // Handle file upload
        if ($request->hasFile('supporting_document')) {
            // Delete old document if exists
            if ($resourceRequest->document_path) {
                Storage::disk('public')->delete($resourceRequest->document_path);
            }
            $path = $request->file('supporting_document')->store('resource_requests', 'public');
            $requestData['document_path'] = $path;
        }

        $resourceRequest->update($requestData);

        return redirect()->route('engineer.resource-requests.show', $resourceRequest)
            ->with('success', 'Resource request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceRequest $resourceRequest)
    {
        // Check if the engineer created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('engineer.resource-requests.index')
                ->with('error', 'You do not have permission to delete this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('engineer.resource-requests.show', $resourceRequest)
                ->with('error', 'You cannot delete a resource request that has already been processed.');
        }

        // Delete document if exists
        if ($resourceRequest->document_path) {
            Storage::disk('public')->delete($resourceRequest->document_path);
        }

        $resourceRequest->delete();

        return redirect()->route('engineer.resource-requests.index')
            ->with('success', 'Resource request deleted successfully.');
    }
}

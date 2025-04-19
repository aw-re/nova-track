<?php

namespace App\Http\Controllers\Contractor;

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
            
        return view('contractor.resource-requests.index', compact('pendingRequests', 'fulfilledRequests', 'rejectedRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $projects = Project::all(); // تأكد من استيراد نموذج Project في الأعلى
    return view('contractor.resource-requests.create', compact('projects'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required|exists:projects,id', // تأكد من أنه مطلوب ويوجد في الجدول
        // باقي الحقول...
    ]);


    return redirect()->route('contractor.resource-requests.index')
        ->with('success', 'تم إنشاء طلب المورد بنجاح');
}

    /**
     * Display the specified resource.
     */
    public function show(ResourceRequest $resourceRequest)
    {
        // Check if the contractor created the request
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('contractor.resource-requests.index')
                ->with('error', 'You do not have permission to view this resource request.');
        }

        $resourceRequest->load(['project', 'requestedBy', 'approvedBy']);
        
        return view('contractor.resource-requests.show', compact('resourceRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceRequest $resourceRequest)
    {
        // Check if the contractor created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('contractor.resource-requests.index')
                ->with('error', 'You do not have permission to edit this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('contractor.resource-requests.show', $resourceRequest)
                ->with('error', 'You cannot edit a resource request that has already been processed.');
        }

        // Get projects where the contractor is a member
        $projects = Project::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
        
        return view('contractor.resource-requests.edit', compact('resourceRequest', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceRequest $resourceRequest)
    {
        // Check if the contractor created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('contractor.resource-requests.index')
                ->with('error', 'You do not have permission to update this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('contractor.resource-requests.show', $resourceRequest)
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

        // Verify the contractor is a member of the project
        $isMember = Project::find($request->project_id)
            ->members()
            ->where('user_id', Auth::id())
            ->exists();
            
        if (!$isMember) {
            return redirect()->route('contractor.resource-requests.edit', $resourceRequest)
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

        return redirect()->route('contractor.resource-requests.show', $resourceRequest)
            ->with('success', 'Resource request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceRequest $resourceRequest)
    {
        // Check if the contractor created the request and it's still pending
        if ($resourceRequest->requested_by !== Auth::id()) {
            return redirect()->route('contractor.resource-requests.index')
                ->with('error', 'You do not have permission to delete this resource request.');
        }

        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('contractor.resource-requests.show', $resourceRequest)
                ->with('error', 'You cannot delete a resource request that has already been processed.');
        }

        // Delete document if exists
        if ($resourceRequest->document_path) {
            Storage::disk('public')->delete($resourceRequest->document_path);
        }

        $resourceRequest->delete();

        return redirect()->route('contractor.resource-requests.index')
            ->with('success', 'Resource request deleted successfully.');
    }
}

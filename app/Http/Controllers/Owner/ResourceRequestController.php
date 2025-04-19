<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Task; // المسار الصحيح لنموذج Task
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
    // app/Http/Controllers/Owner/ResourceRequestController.php

public function index()
{
    $user = auth()->user();
    
    // جلب جميع طلبات الموارد للمشاريع المملوكة لهذا المستخدم
    $resourceRequests = ResourceRequest::whereHas('project', function($query) use ($user) {
        $query->where('owner_id', $user->id);
    })
    ->with(['project', 'requestedBy', 'approvedBy', 'rejectedBy'])
    ->latest()
    ->get();

    return view('owner.resource-requests.index', compact('resourceRequests'));
}

    /**
     * Approve a resource request.
     */
    // app/Http/Controllers/Owner/ResourceRequestController.php

public function approve(Request $request, ResourceRequest $resourceRequest)
{
    if ($resourceRequest->project->owner_id !== auth()->id()) {
        return back()->with('error', 'You are not authorized to approve this request');
    }

    $resourceRequest->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
        'approval_comment' => $request->comment
    ]);

    return redirect()->route('owner.resource-requests.index')
           ->with('success', 'Resource request approved successfully');
}

public function reject(Request $request, ResourceRequest $resourceRequest)
{
    if ($resourceRequest->project->owner_id !== auth()->id()) {
        return back()->with('error', 'You are not authorized to reject this request');
    }

    $request->validate([
        'comment' => 'required|string'
    ]);

    $resourceRequest->update([
        'status' => 'rejected',
        'rejected_by' => auth()->id(),
        'rejected_at' => now(),
        'rejection_comment' => $request->comment
    ]);

    return redirect()->route('owner.resource-requests.index')
           ->with('success', 'Resource request rejected successfully');
}

    /**
     * Mark a resource request as fulfilled.
     */
    public function markFulfilled(Request $request, ResourceRequest $resourceRequest)
    {
        // Check if the owner owns the project
        if ($resourceRequest->project->owner_id !== Auth::id()) {
            return redirect()->route('owner.resource-requests.index')
                ->with('error', 'You do not have permission to update this resource request.');
        }

        // Check if the request is approved
        if ($resourceRequest->status !== 'approved') {
            return redirect()->route('owner.resource-requests.show', $resourceRequest)
                ->with('error', 'Only approved resource requests can be marked as fulfilled.');
        }

        $request->validate([
            'fulfillment_notes' => 'nullable|string',
        ]);

        $resourceRequest->update([
            'status' => 'fulfilled',
            'fulfilled_at' => now(),
            'fulfillment_notes' => $request->fulfillment_notes,
        ]);

        return redirect()->route('owner.resource-requests.show', $resourceRequest)
            ->with('success', 'Resource request marked as fulfilled successfully.');
    }
    // app/Http/Controllers/Owner/ResourceRequestController.php

    public function show(ResourceRequest $resourceRequest)
    {
        // استخدم العلاقة الصحيحة حسب هيكل قاعدة البيانات
        $relatedTasks = Task::where('project_id', $resourceRequest->project_id)->get();
        
        return view('owner.resource-requests.show', [
            'resourceRequest' => $resourceRequest,
            'relatedTasks' => $relatedTasks
        ]);
    }
}

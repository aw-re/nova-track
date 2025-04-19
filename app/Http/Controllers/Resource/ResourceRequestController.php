<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Resource;
use App\Models\ResourceRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceRequestController extends Controller
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
     * Display a listing of the resource requests.
     */
    public function index(Request $request, Project $project = null)
    {
        $user = Auth::user();
        
        if ($project) {
            // Check if user has access to this project
            if (!$user->isAdmin() && 
                $project->owner_id !== $user->id && 
                !$project->projectMembers()->where('user_id', $user->id)->exists()) {
                return redirect()->route('projects.index')
                    ->with('error', 'You do not have permission to view resource requests for this project.');
            }
            
            $resourceRequests = $project->resourceRequests()
                ->with(['resource', 'requestedBy', 'approvedBy', 'task'])
                ->latest()
                ->paginate(10);
                
            return view('projects.resources.requests.index', compact('project', 'resourceRequests'));
        } else {
            // Global resource requests view (admin only)
            if (!$user->isAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to view all resource requests.');
            }
            
            $resourceRequests = ResourceRequest::with(['resource', 'requestedBy', 'approvedBy', 'project', 'task'])
                ->latest()
                ->paginate(10);
                
            return view('resources.requests.index', compact('resourceRequests'));
        }
    }

    /**
     * Show the form for creating a new resource request.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create resource requests for this project.');
        }
        
        $resources = Resource::orderBy('name')->get();
        $tasks = $project->tasks;
        
        return view('projects.resources.requests.create', compact('project', 'resources', 'tasks'));
    }

    /**
     * Store a newly created resource request in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to create resource requests for this project.');
        }
        
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'task_id' => 'nullable|exists:tasks,id',
            'quantity' => 'required|numeric|min:0.01',
            'requested_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:requested_date',
            'notes' => 'nullable|string',
        ]);
        
        // If task_id is provided, check if it belongs to the project
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->project_id !== $project->id) {
                return redirect()->route('projects.resources.requests.create', $project)
                    ->with('error', 'The selected task does not belong to this project.')
                    ->withInput();
            }
        }
        
        $resourceRequest = ResourceRequest::create([
            'project_id' => $project->id,
            'task_id' => $request->task_id,
            'resource_id' => $request->resource_id,
            'requested_by' => $user->id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'requested_date' => $request->requested_date,
            'required_date' => $request->required_date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('projects.resources.requests.index', $project)
            ->with('success', 'Resource request created successfully.');
    }

    /**
     * Display the specified resource request.
     */
    public function show(Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view resource requests for this project.');
        }
        
        $resourceRequest->load(['resource', 'requestedBy', 'approvedBy', 'task']);
        
        return view('projects.resources.requests.show', compact('project', 'resourceRequest'));
    }

    /**
     * Show the form for editing the specified resource request.
     */
    public function edit(Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only the requester, project owner, and admin can edit resource requests
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $resourceRequest->requested_by !== $user->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'You do not have permission to edit this resource request.');
        }
        
        // Cannot edit if already approved, rejected, delivered, or cancelled
        if (in_array($resourceRequest->status, ['approved', 'rejected', 'delivered', 'cancelled'])) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot edit resource request with status: ' . $resourceRequest->status);
        }
        
        $resources = Resource::orderBy('name')->get();
        $tasks = $project->tasks;
        
        return view('projects.resources.requests.edit', compact('project', 'resourceRequest', 'resources', 'tasks'));
    }

    /**
     * Update the specified resource request in storage.
     */
    public function update(Request $request, Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only the requester, project owner, and admin can update resource requests
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $resourceRequest->requested_by !== $user->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'You do not have permission to update this resource request.');
        }
        
        // Cannot update if already approved, rejected, delivered, or cancelled
        if (in_array($resourceRequest->status, ['approved', 'rejected', 'delivered', 'cancelled'])) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot update resource request with status: ' . $resourceRequest->status);
        }
        
        $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'task_id' => 'nullable|exists:tasks,id',
            'quantity' => 'required|numeric|min:0.01',
            'requested_date' => 'required|date',
            'required_date' => 'required|date|after_or_equal:requested_date',
            'notes' => 'nullable|string',
        ]);
        
        // If task_id is provided, check if it belongs to the project
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->project_id !== $project->id) {
                return redirect()->route('projects.resources.requests.edit', [$project, $resourceRequest])
                    ->with('error', 'The selected task does not belong to this project.')
                    ->withInput();
            }
        }
        
        $resourceRequest->update([
            'resource_id' => $request->resource_id,
            'task_id' => $request->task_id,
            'quantity' => $request->quantity,
            'requested_date' => $request->requested_date,
            'required_date' => $request->required_date,
            'notes' => $request->notes,
        ]);
        
        return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
            ->with('success', 'Resource request updated successfully.');
    }

    /**
     * Remove the specified resource request from storage.
     */
    public function destroy(Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only the requester, project owner, and admin can delete resource requests
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $resourceRequest->requested_by !== $user->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'You do not have permission to delete this resource request.');
        }
        
        // Cannot delete if already approved, rejected, delivered, or cancelled
        if (in_array($resourceRequest->status, ['approved', 'delivered'])) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot delete resource request with status: ' . $resourceRequest->status);
        }
        
        $resourceRequest->delete();
        
        return redirect()->route('projects.resources.requests.index', $project)
            ->with('success', 'Resource request deleted successfully.');
    }

    /**
     * Approve the specified resource request.
     */
    public function approve(Request $request, Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only project owner and admin can approve resource requests
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'You do not have permission to approve this resource request.');
        }
        
        // Cannot approve if not pending
        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot approve resource request with status: ' . $resourceRequest->status);
        }
        
        $resourceRequest->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
            ->with('success', 'Resource request approved successfully.');
    }

    /**
     * Reject the specified resource request.
     */
    public function reject(Request $request, Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only project owner and admin can reject resource requests
        if (!$user->isAdmin() && $project->owner_id !== $user->id) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'You do not have permission to reject this resource request.');
        }
        
        // Cannot reject if not pending
        if ($resourceRequest->status !== 'pending') {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot reject resource request with status: ' . $resourceRequest->status);
        }
        
        $resourceRequest->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
            ->with('success', 'Resource request rejected successfully.');
    }

    /**
     * Mark the specified resource request as delivered.
     */
    public function deliver(Request $request, Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only project owner, admin, and the requester can mark as delivered
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $resourceRequest->requested_by !== $user->id) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'You do not have permission to mark this resource request as delivered.');
        }
        
        // Cannot mark as delivered if not approved
        if ($resourceRequest->status !== 'approved') {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot mark resource request as delivered with status: ' . $resourceRequest->status);
        }
        
        $resourceRequest->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        
        return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
            ->with('success', 'Resource request marked as delivered successfully.');
    }

    /**
     * Cancel the specified resource request.
     */
    public function cancel(Request $request, Project $project, ResourceRequest $resourceRequest)
    {
        $user = Auth::user();
        
        // Check if resource request belongs to the project
        if ($resourceRequest->project_id !== $project->id) {
            return redirect()->route('projects.resources.requests.index', $project)
                ->with('error', 'The resource request does not belong to this project.');
        }
        
        // Only the requester, project owner, and admin can cancel resource requests
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $resourceRequest->requested_by !== $user->id) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'You do not have permission to cancel this resource request.');
        }
        
        // Cannot cancel if already delivered or cancelled
        if (in_array($resourceRequest->status, ['delivered', 'cancelled'])) {
            return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
                ->with('error', 'Cannot cancel resource request with status: ' . $resourceRequest->status);
        }
        
        $resourceRequest->update([
            'status' => 'cancelled',
        ]);
        
        return redirect()->route('projects.resources.requests.show', [$project, $resourceRequest])
            ->with('success', 'Resource request cancelled successfully.');
    }
}

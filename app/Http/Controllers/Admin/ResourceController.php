<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resources.
     */
    public function index()
    {
        // Get counts for different resource types
        $materialCount = Resource::where('type', 'material')->count();
        $equipmentCount = Resource::where('type', 'equipment')->count();
        $laborCount = Resource::where('type', 'labor')->count();
        
        // Get all resources with pagination
        $resources = Resource::with(['project', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('admin.resources.index', compact(
            'resources', 
            'materialCount', 
            'equipmentCount', 
            'laborCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.resources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:material,equipment,labor',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $resource = Resource::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'cost' => $request->cost,
            'supplier' => $request->supplier,
            'project_id' => $request->project_id,
            'status' => 'available',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Resource created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $resource->load(['project', 'createdBy', 'updatedBy']);
        
        return view('admin.resources.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        return view('admin.resources.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:material,equipment,labor',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'status' => 'required|in:available,allocated,depleted',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $resource->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'cost' => $request->cost,
            'supplier' => $request->supplier,
            'status' => $request->status,
            'project_id' => $request->project_id,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.resources.show', $resource)
            ->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        // Check if the resource is being used
        if ($resource->status === 'allocated') {
            return redirect()->route('admin.resources.show', $resource)
                ->with('error', 'Cannot delete a resource that is currently allocated.');
        }

        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }
}

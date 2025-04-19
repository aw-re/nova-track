<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUpdate;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskUpdateController extends Controller
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
     * Display a listing of the task updates.
     */
    public function index(Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view updates for this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        $updates = $task->updates()->with(['user', 'images'])->latest()->get();
        
        return view('projects.tasks.updates.index', compact('project', 'task', 'updates'));
    }

    /**
     * Show the form for creating a new task update.
     */
    public function create(Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Only assigned contractor, project owner, and admin can add updates
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $task->assigned_to !== $user->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'You do not have permission to add updates to this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        return view('projects.tasks.updates.create', compact('project', 'task'));
    }

    /**
     * Store a newly created task update in storage.
     */
    public function store(Request $request, Project $project, Task $task)
    {
        $user = Auth::user();
        
        // Only assigned contractor, project owner, and admin can add updates
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $task->assigned_to !== $user->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'You do not have permission to add updates to this task.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        $request->validate([
            'description' => 'required|string',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'hours_spent' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Create task update
        $update = TaskUpdate::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'description' => $request->description,
            'progress_percentage' => $request->progress_percentage,
            'hours_spent' => $request->hours_spent,
        ]);
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('task-images', 'public');
                
                TaskImage::create([
                    'task_update_id' => $update->id,
                    'image_path' => $path,
                    'caption' => null,
                ]);
            }
        }
        
        // Update task progress if progress_percentage is provided
        if ($request->has('progress_percentage') && $request->progress_percentage !== null) {
            // If progress is 100%, set status to completed
            if ($request->progress_percentage == 100 && $task->status !== 'completed') {
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } 
            // If progress is between 1-99%, set status to in_progress
            elseif ($request->progress_percentage > 0 && $request->progress_percentage < 100 && $task->status !== 'in_progress') {
                $task->update([
                    'status' => 'in_progress',
                ]);
            }
        }
        
        // Update actual hours if hours_spent is provided
        if ($request->has('hours_spent') && $request->hours_spent !== null) {
            $task->update([
                'actual_hours' => $task->actual_hours + $request->hours_spent,
            ]);
        }
        
        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Task update added successfully.');
    }

    /**
     * Display the specified task update.
     */
    public function show(Project $project, Task $task, TaskUpdate $update)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view this task update.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        // Check if update belongs to the task
        if ($update->task_id !== $task->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'The update does not belong to this task.');
        }
        
        $update->load(['user', 'images']);
        
        return view('projects.tasks.updates.show', compact('project', 'task', 'update'));
    }

    /**
     * Show the form for editing the specified task update.
     */
    public function edit(Project $project, Task $task, TaskUpdate $update)
    {
        $user = Auth::user();
        
        // Only the update creator, project owner, and admin can edit updates
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $update->user_id !== $user->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'You do not have permission to edit this task update.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        // Check if update belongs to the task
        if ($update->task_id !== $task->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'The update does not belong to this task.');
        }
        
        $update->load('images');
        
        return view('projects.tasks.updates.edit', compact('project', 'task', 'update'));
    }

    /**
     * Update the specified task update in storage.
     */
    public function update(Request $request, Project $project, Task $task, TaskUpdate $update)
    {
        $user = Auth::user();
        
        // Only the update creator, project owner, and admin can update updates
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $update->user_id !== $user->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'You do not have permission to update this task update.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        // Check if update belongs to the task
        if ($update->task_id !== $task->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'The update does not belong to this task.');
        }
        
        $request->validate([
            'description' => 'required|string',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'hours_spent' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_captions.*' => 'nullable|string|max:255',
        ]);
        
        // Calculate the difference in hours spent
        $hoursDifference = 0;
        if ($request->has('hours_spent') && $request->hours_spent !== null) {
            $hoursDifference = $request->hours_spent - $update->hours_spent;
        }
        
        // Update task update
        $update->update([
            'description' => $request->description,
            'progress_percentage' => $request->progress_percentage,
            'hours_spent' => $request->hours_spent,
        ]);
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('task-images', 'public');
                
                TaskImage::create([
                    'task_update_id' => $update->id,
                    'image_path' => $path,
                    'caption' => $request->image_captions[$index] ?? null,
                ]);
            }
        }
        
        // Update image captions
        if ($request->has('existing_image_captions')) {
            foreach ($request->existing_image_captions as $imageId => $caption) {
                $taskImage = TaskImage::find($imageId);
                if ($taskImage && $taskImage->task_update_id === $update->id) {
                    $taskImage->update(['caption' => $caption]);
                }
            }
        }
        
        // Delete images if requested
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $taskImage = TaskImage::find($imageId);
                if ($taskImage && $taskImage->task_update_id === $update->id) {
                    // Delete the file from storage
                    Storage::disk('public')->delete($taskImage->image_path);
                    // Delete the record
                    $taskImage->delete();
                }
            }
        }
        
        // Update task progress if progress_percentage is provided
        if ($request->has('progress_percentage') && $request->progress_percentage !== null) {
            // If progress is 100%, set status to completed
            if ($request->progress_percentage == 100 && $task->status !== 'completed') {
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } 
            // If progress is between 1-99%, set status to in_progress
            elseif ($request->progress_percentage > 0 && $request->progress_percentage < 100 && $task->status !== 'in_progress') {
                $task->update([
                    'status' => 'in_progress',
                ]);
            }
        }
        
        // Update actual hours if hours_spent has changed
        if ($hoursDifference != 0) {
            $task->update([
                'actual_hours' => $task->actual_hours + $hoursDifference,
            ]);
        }
        
        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Task update modified successfully.');
    }

    /**
     * Remove the specified task update from storage.
     */
    public function destroy(Project $project, Task $task, TaskUpdate $update)
    {
        $user = Auth::user();
        
        // Only the update creator, project owner, and admin can delete updates
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $update->user_id !== $user->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'You do not have permission to delete this task update.');
        }
        
        // Check if task belongs to the project
        if ($task->project_id !== $project->id) {
            return redirect()->route('projects.tasks.index', $project)
                ->with('error', 'The task does not belong to this project.');
        }
        
        // Check if update belongs to the task
        if ($update->task_id !== $task->id) {
            return redirect()->route('projects.tasks.show', [$project, $task])
                ->with('error', 'The update does not belong to this task.');
        }
        
        // Adjust task actual hours
        if ($update->hours_spent) {
            $task->update([
                'actual_hours' => max(0, $task->actual_hours - $update->hours_spent),
            ]);
        }
        
        // Delete associated images
        foreach ($update->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        $update->delete();
        
        return redirect()->route('projects.tasks.show', [$project, $task])
            ->with('success', 'Task update deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
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
     * Display a listing of the files.
     */
    public function index(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view files for this project.');
        }
        
        $files = $project->files()
            ->with(['uploadedBy', 'task'])
            ->latest()
            ->paginate(20);
            
        return view('projects.files.index', compact('project', 'files'));
    }

    /**
     * Show the form for creating a new file.
     */
    public function create(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to upload files to this project.');
        }
        
        $tasks = $project->tasks;
        
        return view('projects.files.create', compact('project', 'tasks'));
    }

    /**
     * Store a newly created file in storage.
     */
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to upload files to this project.');
        }
        
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max file size
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
        ]);
        
        // If task_id is provided, check if it belongs to the project
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->project_id !== $project->id) {
                return redirect()->route('projects.files.create', $project)
                    ->with('error', 'The selected task does not belong to this project.')
                    ->withInput();
            }
        }
        
        $uploadedFile = $request->file('file');
        $fileName = $uploadedFile->getClientOriginalName();
        $fileSize = $uploadedFile->getSize();
        $fileType = $uploadedFile->getMimeType();
        
        // Store the file
        $path = $uploadedFile->store('project-files/' . $project->id, 'public');
        
        // Create file record
        $file = File::create([
            'project_id' => $project->id,
            'task_id' => $request->task_id,
            'uploaded_by' => $user->id,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'description' => $request->description,
            'version' => $request->version,
        ]);
        
        return redirect()->route('projects.files.index', $project)
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Display the specified file.
     */
    public function show(Project $project, File $file)
    {
        $user = Auth::user();
        
        // Check if file belongs to the project
        if ($file->project_id !== $project->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'The file does not belong to this project.');
        }
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to view files for this project.');
        }
        
        $file->load(['uploadedBy', 'task']);
        
        return view('projects.files.show', compact('project', 'file'));
    }

    /**
     * Show the form for editing the specified file.
     */
    public function edit(Project $project, File $file)
    {
        $user = Auth::user();
        
        // Check if file belongs to the project
        if ($file->project_id !== $project->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'The file does not belong to this project.');
        }
        
        // Only the uploader, project owner, and admin can edit file details
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $file->uploaded_by !== $user->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'You do not have permission to edit this file.');
        }
        
        $tasks = $project->tasks;
        
        return view('projects.files.edit', compact('project', 'file', 'tasks'));
    }

    /**
     * Update the specified file in storage.
     */
    public function update(Request $request, Project $project, File $file)
    {
        $user = Auth::user();
        
        // Check if file belongs to the project
        if ($file->project_id !== $project->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'The file does not belong to this project.');
        }
        
        // Only the uploader, project owner, and admin can update file details
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $file->uploaded_by !== $user->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'You do not have permission to update this file.');
        }
        
        $request->validate([
            'task_id' => 'nullable|exists:tasks,id',
            'description' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'new_file' => 'nullable|file|max:10240', // 10MB max file size
        ]);
        
        // If task_id is provided, check if it belongs to the project
        if ($request->task_id) {
            $task = Task::find($request->task_id);
            if (!$task || $task->project_id !== $project->id) {
                return redirect()->route('projects.files.edit', [$project, $file])
                    ->with('error', 'The selected task does not belong to this project.')
                    ->withInput();
            }
        }
        
        // Update file details
        $file->update([
            'task_id' => $request->task_id,
            'description' => $request->description,
            'version' => $request->version,
        ]);
        
        // If a new file is uploaded, replace the old one
        if ($request->hasFile('new_file')) {
            $uploadedFile = $request->file('new_file');
            $fileName = $uploadedFile->getClientOriginalName();
            $fileSize = $uploadedFile->getSize();
            $fileType = $uploadedFile->getMimeType();
            
            // Delete the old file
            Storage::disk('public')->delete($file->file_path);
            
            // Store the new file
            $path = $uploadedFile->store('project-files/' . $project->id, 'public');
            
            // Update file record
            $file->update([
                'file_name' => $fileName,
                'file_path' => $path,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ]);
        }
        
        return redirect()->route('projects.files.show', [$project, $file])
            ->with('success', 'File updated successfully.');
    }

    /**
     * Remove the specified file from storage.
     */
    public function destroy(Project $project, File $file)
    {
        $user = Auth::user();
        
        // Check if file belongs to the project
        if ($file->project_id !== $project->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'The file does not belong to this project.');
        }
        
        // Only the uploader, project owner, and admin can delete files
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            $file->uploaded_by !== $user->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'You do not have permission to delete this file.');
        }
        
        // Delete the file from storage
        Storage::disk('public')->delete($file->file_path);
        
        // Delete the file record
        $file->delete();
        
        return redirect()->route('projects.files.index', $project)
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Download the specified file.
     */
    public function download(Project $project, File $file)
    {
        $user = Auth::user();
        
        // Check if file belongs to the project
        if ($file->project_id !== $project->id) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'The file does not belong to this project.');
        }
        
        // Check if user has access to this project
        if (!$user->isAdmin() && 
            $project->owner_id !== $user->id && 
            !$project->projectMembers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('projects.index')
                ->with('error', 'You do not have permission to download files from this project.');
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->route('projects.files.index', $project)
                ->with('error', 'File not found in storage.');
        }
        
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}

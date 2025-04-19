<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = File::with(['project', 'uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.files.index', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        return view('admin.files.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'file' => 'required|file|max:20480', // 20MB max
        ]);

        $path = $request->file('file')->store('project_files', 'public');
        $originalName = $request->file('file')->getClientOriginalName();
        $fileSize = $request->file('file')->getSize();
        $fileType = $request->file('file')->getMimeType();

        File::create([
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'file_path' => $path,
            'file_name' => $originalName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.files.index')
            ->with('success', 'File uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        $file->load(['project', 'uploadedBy']);
        return view('admin.files.show', compact('file'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(File $file)
    {
        $projects = Project::all();
        return view('admin.files.edit', compact('file', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, File $file)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'file' => 'nullable|file|max:20480', // 20MB max
        ]);

        $fileData = [
            'title' => $request->title,
            'description' => $request->description,
            'project_id' => $request->project_id,
        ];

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($file->file_path);
            
            // Upload new file
            $path = $request->file('file')->store('project_files', 'public');
            $originalName = $request->file('file')->getClientOriginalName();
            $fileSize = $request->file('file')->getSize();
            $fileType = $request->file('file')->getMimeType();
            
            $fileData['file_path'] = $path;
            $fileData['file_name'] = $originalName;
            $fileData['file_size'] = $fileSize;
            $fileData['file_type'] = $fileType;
        }

        $file->update($fileData);

        return redirect()->route('admin.files.index')
            ->with('success', 'File updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(File $file)
    {
        // Delete the physical file
        Storage::disk('public')->delete($file->file_path);
        
        // Delete the database record
        $file->delete();

        return redirect()->route('admin.files.index')
            ->with('success', 'File deleted successfully.');
    }

    /**
     * Download the specified file.
     */
    public function download(File $file)
    {
        // Check if file exists
        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->route('admin.files.index')
                ->with('error', 'File not found.');
        }

        // Increment download count
        $file->increment('download_count');

        // Return file for download
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}

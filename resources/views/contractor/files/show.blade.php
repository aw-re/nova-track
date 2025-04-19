@extends('layouts.app')

@section('title', 'View File - CPMS')

@section('page_title', 'File Details')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('contractor.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $file->name }}</h1>
        <div>
            <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download
            </a>
            @if($file->user_id == auth()->id())
                <form action="{{ route('contractor.files.destroy', $file) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger ms-2">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">File Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">File Name:</div>
                        <div class="col-md-8">{{ $file->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">File Type:</div>
                        <div class="col-md-8">{{ strtoupper($file->extension) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">File Size:</div>
                        <div class="col-md-8">{{ $file->size_formatted }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Uploaded By:</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Upload Date:</div>
                        <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($file->created_at)) }}</div>
                    </div>
                    @if($file->description)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Description:</div>
                            <div class="col-md-8">{{ $file->description }}</div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Related To:</div>
                        <div class="col-md-8">
                            @if($file->project_id)
                                <a href="{{ route('contractor.projects.show', $file->project_id) }}">Project: {{ $file->project->name }}</a>
                            @elseif($file->task_id)
                                <a href="{{ route('contractor.tasks.show', $file->task_id) }}">Task: {{ $file->task->title }}</a>
                            @else
                                General File
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(in_array(strtolower($file->extension), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ route('contractor.files.preview', $file) }}" alt="{{ $file->name }}" class="img-fluid" style="max-height: 500px;">
                    </div>
                </div>
            @elseif(in_array(strtolower($file->extension), ['pdf', 'txt', 'csv', 'md']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Preview is available for this file type. Please download the file to view its contents.
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Comments</h5>
                </div>
                <div class="card-body">
                    <div class="comments-list mb-3">
                            <div class="comment mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center">No comments yet.</p>
                    </div>
                        @csrf
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="comment" name="content" rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">File History</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Created</strong>
                                    <div class="text-muted">{{ date('M d, Y h:i A', strtotime($file->created_at)) }}</div>
                                </div>
                            </div>
                        </li>
                        @if($file->updated_at != $file->created_at)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Updated</strong>
                                        <div class="text-muted">{{ date('M d, Y h:i A', strtotime($file->updated_at)) }}</div>
                                    </div>
                                    <span>{{ $file->user->name }}</span>
                                </div>
                            </li>
                        @endif
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Downloaded</strong>
                                    </div>
                                </div>
                            </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any file-specific JavaScript can go here
    </script>
@endsection

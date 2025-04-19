@extends('layouts.app')

@section('title', 'View File - CPMS')

@section('page_title', 'File Details')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.projects.index') }}">
            <i class="fas fa-project-diagram"></i> My Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('owner.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $file->name }}</h1>
        <div>
            <a href="{{ route('owner.files.download', $file) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download
            </a>
            <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Delete
            </button>
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
                        <div class="col-md-8">{{ $file->uploadedBy->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Upload Date:</div>
                        <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($file->created_at)) }}</div>
                    </div>
                    @if($file->project_id)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Related Project:</div>
                            <div class="col-md-8">
                                <a href="{{ route('owner.projects.show', $file->project_id) }}">{{ $file->project->name }}</a>
                            </div>
                        </div>
                    @endif
                    @if($file->task_id)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Related Task:</div>
                            <div class="col-md-8">
                                <a href="{{ route('owner.tasks.show', $file->task_id) }}">{{ $file->task->title }}</a>
                            </div>
                        </div>
                    @endif
                    @if($file->report_id)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Related Report:</div>
                            <div class="col-md-8">
                                <a href="{{ route('owner.reports.show', $file->report_id) }}">{{ $file->report->title }}</a>
                            </div>
                        </div>
                    @endif
                    @if($file->description)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Description:</div>
                            <div class="col-md-8">{{ $file->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if(in_array(strtolower($file->extension), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ route('owner.files.preview', $file) }}" alt="{{ $file->name }}" class="img-fluid" style="max-height: 500px;">
                    </div>
                </div>
            @elseif(in_array(strtolower($file->extension), ['pdf']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ route('owner.files.preview', $file) }}" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            @elseif(in_array(strtolower($file->extension), ['txt', 'md', 'html', 'css', 'js', 'php', 'json', 'xml']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3 rounded"><code>{{ $fileContent ?? 'Preview not available' }}</code></pre>
                    </div>
                </div>
            @else
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Preview</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="p-5 bg-light rounded">
                            <i class="fas fa-file fa-5x text-secondary mb-3"></i>
                            <p>Preview not available for this file type.</p>
                            <a href="{{ route('owner.files.download', $file) }}" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download to View
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">File History</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-upload me-2"></i> Uploaded
                                </div>
                                <div class="text-muted">{{ date('M d, Y h:i A', strtotime($file->created_at)) }}</div>
                            </div>
                            <div class="ms-4 mt-1 text-muted">
                                By {{ $file->uploadedBy->name }}
                            </div>
                        </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="fas fa-download me-2"></i> Downloaded
                                    </div>
                                </div>
                                <div class="ms-4 mt-1 text-muted">
                                </div>
                            </li>
                    </ul>
                </div>
            </div>

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
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the file "{{ $file->name }}"? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('owner.files.destroy', $file) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
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

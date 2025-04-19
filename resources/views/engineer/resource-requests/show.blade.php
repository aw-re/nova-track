@extends('layouts.app')

@section('title', 'View Resource Request - CPMS')

@section('page_title', 'Resource Request Details')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('engineer.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $resourceRequest->title }}</h1>
        <div>
            @if($resourceRequest->status == 'pending' && $resourceRequest->user_id == auth()->id())
                <a href="{{ route('engineer.resource-requests.edit', $resourceRequest) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Request
                </a>
            @endif
            <a href="{{ route('engineer.projects.show', $resourceRequest->project) }}" class="btn btn-primary ms-2">
                <i class="fas fa-project-diagram"></i> View Project
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project:</div>
                        <div class="col-md-8">
                            <a href="{{ route('engineer.projects.show', $resourceRequest->project) }}">{{ $resourceRequest->project->name }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Resource Type:</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $resourceRequest->type)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($resourceRequest->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($resourceRequest->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($resourceRequest->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Quantity:</div>
                        <div class="col-md-8">{{ $resourceRequest->quantity }} {{ $resourceRequest->unit }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Estimated Cost:</div>
                        <div class="col-md-8">{{ $resourceRequest->estimated_cost ? '$' . number_format($resourceRequest->estimated_cost, 2) : 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Requested By:</div>
                        <div class="col-md-8">{{ $resourceRequest->user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Required By:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($resourceRequest->required_by)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Submitted On:</div>
                        <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($resourceRequest->created_at)) }}</div>
                    </div>
                    @if($resourceRequest->status == 'approved' && $resourceRequest->approved_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved On:</div>
                            <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($resourceRequest->approved_at)) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved By:</div>
                            <div class="col-md-8">{{ $resourceRequest->approvedBy ? $resourceRequest->approvedBy->name : 'N/A' }}</div>
                        </div>
                    @endif
                    @if($resourceRequest->status == 'rejected' && $resourceRequest->rejected_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected On:</div>
                            <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($resourceRequest->rejected_at)) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected By:</div>
                            <div class="col-md-8">{{ $resourceRequest->rejectedBy ? $resourceRequest->rejectedBy->name : 'N/A' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Description</h5>
                </div>
                <div class="card-body">
                    <div class="request-description">
                        {!! nl2br(e($resourceRequest->description)) !!}
                    </div>
                </div>
            </div>

            @if($resourceRequest->specifications)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Specifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="request-specifications">
                            {!! nl2br(e($resourceRequest->specifications)) !!}
                        </div>
                    </div>
                </div>
            @endif

            @if($resourceRequest->status == 'rejected' && $resourceRequest->rejection_reason)
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">Rejection Reason</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $resourceRequest->rejection_reason }}</p>
                    </div>
                </div>
            @endif

            @if($resourceRequest->status == 'approved' && $resourceRequest->approval_notes)
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Approval Notes</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ $resourceRequest->approval_notes }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Attachments</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($resourceRequest->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i> {{ $file->name }}
                                    <div class="text-muted small">{{ $file->size_formatted }}</div>
                                </div>
                                <a href="{{ route('engineer.files.download', $file) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">No attachments found for this request.</li>
                        @endforelse
                    </ul>
                    @if($resourceRequest->status == 'pending' && $resourceRequest->user_id == auth()->id())
                        <div class="mt-3">
                            <form action="{{ route('engineer.files.upload-to-resource-request', $resourceRequest) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label">Add Attachment</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Comments</h5>
                </div>
                <div class="card-body">
                    <div class="comments-list mb-3">
                        @forelse($resourceRequest->comments as $comment)
                            <div class="comment mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $comment->user->name }}</strong>
                                                <span class="text-muted small ms-2">{{ date('M d, Y h:i A', strtotime($comment->created_at)) }}</span>
                                            </div>
                                        </div>
                                        <p class="mb-0 mt-1">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">No comments yet.</p>
                        @endforelse
                    </div>
                    <form action="{{ route('engineer.resource-requests.comment', $resourceRequest) }}" method="POST">
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
@endsection

@section('scripts')
    <script>
        // Any resource request-specific JavaScript can go here
    </script>
@endsection

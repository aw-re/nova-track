@extends('layouts.app')

@section('title', 'View Resource Request - CPMS')

@section('page_title', 'Resource Request Details')

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
        <a class="nav-link active" href="{{ route('contractor.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $resourceRequest->resource_name ?? 'Resource Request' }}</h1>
        <div>
            @if($resourceRequest->status == 'pending' && $resourceRequest->requested_by == auth()->id())
                <a href="{{ route('contractor.resource-requests.edit', $resourceRequest) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Request
                </a>
            @endif
            <a href="{{ route('contractor.projects.show', $resourceRequest->project_id) }}" class="btn btn-primary ms-2">
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
                            @if(isset($resourceRequest->project) && is_object($resourceRequest->project))
                                <a href="{{ route('contractor.projects.show', $resourceRequest->project_id) }}">{{ $resourceRequest->project->name }}</a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Resource Type:</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $resourceRequest->resource_type ?? 'N/A')) }}</div>
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
                            @elseif($resourceRequest->status == 'fulfilled')
                                <span class="badge bg-info">Fulfilled</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Quantity:</div>
                        <div class="col-md-8">{{ $resourceRequest->quantity ?? 'N/A' }} {{ $resourceRequest->unit ?? '' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Requested By:</div>
                        <div class="col-md-8">
                            @if(isset($resourceRequest->requestedBy) && is_object($resourceRequest->requestedBy))
                                {{ $resourceRequest->requestedBy->name }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Required By:</div>
                        <div class="col-md-8">{{ $resourceRequest->required_by ? date('M d, Y', strtotime($resourceRequest->required_by)) : 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Submitted On:</div>
                        <div class="col-md-8">{{ $resourceRequest->created_at ? date('M d, Y h:i A', strtotime($resourceRequest->created_at)) : 'N/A' }}</div>
                    </div>
                    @if($resourceRequest->status == 'approved' && $resourceRequest->approved_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved On:</div>
                            <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($resourceRequest->approved_at)) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved By:</div>
                            <div class="col-md-8">
                                @if(isset($resourceRequest->approvedBy) && is_object($resourceRequest->approvedBy))
                                    {{ $resourceRequest->approvedBy->name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($resourceRequest->status == 'rejected' && $resourceRequest->rejected_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected On:</div>
                            <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($resourceRequest->rejected_at)) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected By:</div>
                            <div class="col-md-8">
                                @if(isset($resourceRequest->rejectedBy) && is_object($resourceRequest->rejectedBy))
                                    {{ $resourceRequest->rejectedBy->name }}
                                @else
                                    N/A
                                @endif
                            </div>
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
                        {!! nl2br(e($resourceRequest->description ?? 'No description provided.')) !!}
                    </div>
                </div>
            </div>

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
            @if($resourceRequest->document_path)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Supporting Document</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid">
                            <a href="{{ Storage::url($resourceRequest->document_path) }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-file-download me-2"></i> View Document
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Actions</h5>
                </div>
                <div class="card-body">
                    @if($resourceRequest->status == 'pending')
                        @if($resourceRequest->requested_by == auth()->id())
                            <div class="d-grid gap-2">
                                <a href="{{ route('contractor.resource-requests.edit', $resourceRequest) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i> Edit Request
                                </a>
                                <form action="{{ route('contractor.resource-requests.destroy', $resourceRequest) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this request? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i> Delete Request
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
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

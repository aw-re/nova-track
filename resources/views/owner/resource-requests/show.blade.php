@extends('layouts.app')

@section('title', 'View Resource Request - CPMS')

@section('page_title', 'Resource Request Details')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $resourceRequest->title }}</h1>
        <div>
            @if($resourceRequest->status == 'pending')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times"></i> Reject
                </button>
            @endif
            <a href="{{ route('owner.projects.show', $resourceRequest->project) }}" class="btn btn-primary ms-2">
                <i class="fas fa-project-diagram"></i> View Project
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Resource Request Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project:</div>
                        <div class="col-md-8">
                            <a href="{{ route('owner.projects.show', $resourceRequest->project) }}">{{ $resourceRequest->project->name }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Resource Type:</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $resourceRequest->resource_type)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Quantity:</div>
                        <div class="col-md-8">{{ $resourceRequest->quantity }}</div>
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
                        <div class="col-md-4 fw-bold">Requested By:</div>
                        <div class="col-md-8">{{ $resourceRequest->requestedBy->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Requested Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($resourceRequest->created_at)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Required By Date:</div>
                        <div class="col-md-8">{{ $resourceRequest->required_by_date ? date('M d, Y', strtotime($resourceRequest->required_by_date)) : 'Not specified' }}</div>
                    </div>
                    @if($resourceRequest->status == 'approved')
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved By:</div>
                            <div class="col-md-8">{{ $resourceRequest->approvedBy ? $resourceRequest->approvedBy->name : 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved Date:</div>
                            <div class="col-md-8">{{ $resourceRequest->approved_at ? date('M d, Y', strtotime($resourceRequest->approved_at)) : 'N/A' }}</div>
                        </div>
                        @if($resourceRequest->approval_comment)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Approval Comment:</div>
                                <div class="col-md-8">{{ $resourceRequest->approval_comment }}</div>
                            </div>
                        @endif
                    @elseif($resourceRequest->status == 'rejected')
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected By:</div>
                            <div class="col-md-8">{{ $resourceRequest->rejectedBy ? $resourceRequest->rejectedBy->name : 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected Date:</div>
                            <div class="col-md-8">{{ $resourceRequest->rejected_at ? date('M d, Y', strtotime($resourceRequest->rejected_at)) : 'N/A' }}</div>
                        </div>
                        @if($resourceRequest->rejection_comment)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Rejection Reason:</div>
                                <div class="col-md-8">{{ $resourceRequest->rejection_comment }}</div>
                            </div>
                        @endif
                    @endif
                    <div class="row">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $resourceRequest->description }}</div>
                    </div>
                </div>
            </div>

            @if($resourceRequest->related_tasks && $resourceRequest->related_tasks->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Related Tasks</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resourceRequest->related_tasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                @if($task->status == 'backlog')
                                                    <span class="badge bg-secondary">Backlog</span>
                                                @elseif($task->status == 'todo')
                                                    <span class="badge bg-info">To Do</span>
                                                @elseif($task->status == 'in_progress')
                                                    <span class="badge bg-primary">In Progress</span>
                                                @elseif($task->status == 'review')
                                                    <span class="badge bg-warning">Review</span>
                                                @elseif($task->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                            <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                            <td>
                                                <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                </div>
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                            <li class="list-group-item">No attachments found for this request.</li>
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

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Approve Resource Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.resource-requests.approve', $resourceRequest) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to approve this resource request?</p>
                        <div class="mb-3">
                            <label for="approval_comment" class="form-label">Comment (Optional)</label>
                            <textarea class="form-control" id="approval_comment" name="comment" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Resource Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.resource-requests.reject', $resourceRequest) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject this resource request?</p>
                        <div class="mb-3">
                            <label for="rejection_comment" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_comment" name="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any resource request-specific JavaScript can go here
    </script>
@endsection
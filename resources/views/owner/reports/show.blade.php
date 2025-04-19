@extends('layouts.app')

@section('title', 'View Report - CPMS')

@section('page_title', 'Report Details')

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
        <a class="nav-link active" href="{{ route('owner.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $report->title }}</h1>
        <div>
            @if($report->status == 'pending')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times"></i> Reject
                </button>
            @endif
            <a href="{{ route('owner.projects.show', $report->project) }}" class="btn btn-primary ms-2">
                <i class="fas fa-project-diagram"></i> View Project
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Report Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project:</div>
                        <div class="col-md-8">
                            <a href="{{ route('owner.projects.show', $report->project) }}">{{ $report->project->name }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Type:</div>
                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $report->type)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($report->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($report->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($report->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Submitted By:</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Submitted Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($report->created_at)) }}</div>
                    </div>
                    @if($report->status == 'approved')
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved By:</div>
                            <div class="col-md-8">{{ $report->approvedBy ? $report->approvedBy->name : 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Approved Date:</div>
                            <div class="col-md-8">{{ $report->approved_at ? date('M d, Y', strtotime($report->approved_at)) : 'N/A' }}</div>
                        </div>
                        @if($report->approval_comment)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Approval Comment:</div>
                                <div class="col-md-8">{{ $report->approval_comment }}</div>
                            </div>
                        @endif
                    @elseif($report->status == 'rejected')
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected By:</div>
                            <div class="col-md-8">{{ $report->rejectedBy ? $report->rejectedBy->name : 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Rejected Date:</div>
                            <div class="col-md-8">{{ $report->rejected_at ? date('M d, Y', strtotime($report->rejected_at)) : 'N/A' }}</div>
                        </div>
                        @if($report->rejection_comment)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Rejection Reason:</div>
                                <div class="col-md-8">{{ $report->rejection_comment }}</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Report Content</h5>
                </div>
                <div class="card-body">
                    <div class="report-content">
                        {!! $report->content !!}
                    </div>
                </div>
            </div>

            @if($report->related_tasks && $report->related_tasks->count() > 0)
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
                                    @foreach($report->related_tasks as $task)
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
                            <li class="list-group-item">No attachments found for this report.</li>
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
    <!-- Approve Modal -->
<div class="modal fade" id="approveReportModal" tabindex="-1" aria-labelledby="approveReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveReportModalLabel">Approve Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveReportForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Are you sure you want to approve this report?</p>
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
                    <h5 class="modal-title" id="rejectModalLabel">Reject Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.reports.reject', $report) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to reject this report?</p>
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
        // Any report-specific JavaScript can go here
    </script>
@endsection

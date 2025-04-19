@extends('layouts.app')

@section('title', 'Engineer Dashboard - CPMS')

@section('page_title', 'Engineer Dashboard')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('engineer.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
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
        <a class="nav-link" href="{{ route('engineer.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.invitations.index') }}">
            <i class="fas fa-envelope"></i> Invitations
            @if(isset($invitationCount) && $invitationCount > 0)
                <span class="badge bg-danger">{{ $invitationCount }}</span>
            @endif
        </a>
    </li>
@endsection

@section('content')
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
    <div class="alert alert-info">
        <i class="fas fa-envelope me-2"></i> You have {{ $pendingInvitations->count() }} pending project invitation(s). 
        <a href="{{ route('engineer.invitations.index') }}" class="alert-link">View invitations</a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $assignedProjectCount }}</div>
                            <div class="stat-label">Assigned Projects</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $assignedTaskCount }}</div>
                            <div class="stat-label">Assigned Tasks</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $createdTaskCount }}</div>
                            <div class="stat-label">Created Tasks</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $submittedReportCount }}</div>
                            <div class="stat-label">Submitted Reports</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks me-2"></i> My Tasks
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedTasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->project->name }}</td>
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
                                        <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No assigned tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('engineer.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-project-diagram me-2"></i> Assigned Projects
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedProjects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>{{ $project->owner->name }}</td>
                                        <td>
                                            @if($project->status == 'planning')
                                                <span class="badge bg-info">Planning</span>
                                            @elseif($project->status == 'in_progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @elseif($project->status == 'on_hold')
                                                <span class="badge bg-warning">On Hold</span>
                                            @elseif($project->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($project->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('engineer.projects.show', $project) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No assigned projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-alt me-2"></i> Recent Reports
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentReports as $report)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $report->title }}</strong>
                                        <div class="text-muted small">{{ $report->project->name }} - {{ $report->report_type }}</div>
                                    </div>
                                    <div>
                                        @if($report->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($report->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($report->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No reports found.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('engineer.reports.index') }}" class="btn btn-sm btn-outline-primary">View All Reports</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tools me-2"></i> Recent Resource Requests
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentResourceRequests as $request)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $request->resource_name }}</strong> ({{ $request->quantity }} {{ $request->unit }})
                                        <div class="text-muted small">{{ $request->project->name }} - Required by {{ date('M d, Y', strtotime($request->required_by)) }}</div>
                                    </div>
                                    <div>
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($request->status == 'fulfilled')
                                            <span class="badge bg-primary">Fulfilled</span>
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No resource requests found.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('engineer.resource-requests.index') }}" class="btn btn-sm btn-outline-primary">View All Requests</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Add any dashboard-specific JavaScript here
    </script>
@endsection

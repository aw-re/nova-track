@extends('layouts.app')

@section('title', 'Contractor Dashboard - CPMS')

@section('page_title', 'Contractor Dashboard')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('contractor.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contractor.invitations.index') }}">
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
        <a href="{{ route('contractor.invitations.index') }}" class="alert-link">View invitations</a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card primary">
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
            <div class="card stat-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $completedTaskCount }}</div>
                            <div class="stat-label">Completed Tasks</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
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
                            <div class="stat-value">{{ $resourceRequestCount }}</div>
                            <div class="stat-label">Resource Requests</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-tools"></i>
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
                            <div class="stat-value">{{ $projectCount }}</div>
                            <div class="stat-label">Assigned Projects</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-project-diagram"></i>
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
                    <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">Pending</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab" aria-controls="in-progress" aria-selected="false">In Progress</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Completed</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="taskTabsContent">
                        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Due Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pendingTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->project->name }}</td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('contractor.tasks.show', $task) }}" class="btn btn-sm btn-primary">View</a>
                                                    <a href="{{ route('contractor.tasks.start', $task) }}" class="btn btn-sm btn-success">Start</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No pending tasks found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="in-progress" role="tabpanel" aria-labelledby="in-progress-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Due Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inProgressTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->project->name }}</td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('contractor.tasks.show', $task) }}" class="btn btn-sm btn-primary">View</a>
                                                    <a href="{{ route('contractor.tasks.complete', $task) }}" class="btn btn-sm btn-success">Complete</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No in-progress tasks found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Project</th>
                                            <th>Completed Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($completedTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->project->name }}</td>
                                                <td>{{ $task->completed_at ? date('M d, Y', strtotime($task->completed_at)) : 'Unknown' }}</td>
                                                <td>
                                                    <a href="{{ route('contractor.tasks.show', $task) }}" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No completed tasks found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('contractor.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tools me-2"></i> Recent Resource Requests
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Resource</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Required By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resourceRequests as $request)
                                    <tr>
                                        <td>{{ $request->resource_name }} ({{ $request->quantity }} {{ $request->unit }})</td>
                                        <td>{{ $request->project->name }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($request->status == 'fulfilled')
                                                <span class="badge bg-primary">Fulfilled</span>
                                            @elseif($request->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($request->required_by)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No resource requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('contractor.resource-requests.index') }}" class="btn btn-sm btn-outline-primary">View All Requests</a>
                    <a href="{{ route('contractor.resource-requests.create') }}" class="btn btn-sm btn-primary">New Request</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-project-diagram me-2"></i> My Projects
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Location</th>
                                    <th>Tasks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
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
                                        <td>{{ $project->location }}</td>
                                        <td>{{ $project->tasks->count() }}</td>
                                        <td>
                                            <a href="{{ route('contractor.projects.show', $project) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

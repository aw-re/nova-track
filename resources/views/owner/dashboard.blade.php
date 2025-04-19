@extends('layouts.app')

@section('title', __('app.owner_dashboard') . ' - ' . __('app.app_name'))

@section('page_title', __('app.owner_dashboard'))

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('owner.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.projects.index') }}">
            <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.tasks.index') }}">
            <i class="fas fa-tasks"></i> {{ __('app.tasks') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.reports.index') }}">
            <i class="fas fa-file-alt"></i> {{ __('app.reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.resource-requests.index') }}">
            <i class="fas fa-tools"></i> {{ __('app.resources') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.files.index') }}">
            <i class="fas fa-file"></i> {{ __('app.files') }}
        </a>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $projectCount }}</div>
                            <div class="stat-label">{{ __('app.my_projects') }}</div>
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
                            <div class="stat-value">{{ $taskCount }}</div>
                            <div class="stat-label">{{ __('app.total_tasks') }}</div>
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
                            <div class="stat-value">{{ $pendingReportCount }}</div>
                            <div class="stat-label">{{ __('app.pending_reports') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
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
                            <div class="stat-value">{{ $pendingResourceRequestCount }}</div>
                            <div class="stat-label">{{ __('app.pending_requests') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-tools"></i>
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
                    <i class="fas fa-project-diagram me-2"></i> {{ __('app.recent_projects') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.project_status') }}</th>
                                    <th>{{ __('app.progress') }}</th>
                                    <th>{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProjects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>
                                            @if($project->status == 'planning')
                                                <span class="badge bg-info">{{ __('app.status_planning') }}</span>
                                            @elseif($project->status == 'in_progress')
                                                <span class="badge bg-primary">{{ __('app.status_in_progress') }}</span>
                                            @elseif($project->status == 'on_hold')
                                                <span class="badge bg-warning">{{ __('app.status_on_hold') }}</span>
                                            @elseif($project->status == 'completed')
                                                <span class="badge bg-success">{{ __('app.status_completed') }}</span>
                                            @elseif($project->status == 'cancelled')
                                                <span class="badge bg-danger">{{ __('app.status_cancelled') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $completedTasks = $project->tasks->where('status', 'completed')->count();
                                                $totalTasks = $project->tasks->count();
                                                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                            @endphp
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('owner.projects.show', $project) }}" class="btn btn-sm btn-primary">{{ __('app.view') }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __('app.no_projects_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('owner.projects.index') }}" class="btn btn-sm btn-outline-primary">{{ __('app.view_all_projects') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks me-2"></i> Recent Tasks
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
                                @forelse($recentTasks as $task)
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
                                        <td colspan="4" class="text-center">No tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('owner.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-alt me-2"></i> Pending Reports
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($pendingReports as $report)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $report->title }}</strong>
                                        <div class="text-muted small">{{ $report->project->name }} - {{ $report->report_type }}</div>
                                    </div>
                                    <a href="{{ route('owner.reports.show', $report) }}" class="btn btn-sm btn-primary">Review</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No pending reports found.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('owner.reports.index') }}" class="btn btn-sm btn-outline-primary">View All Reports</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tools me-2"></i> Pending Resource Requests
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($pendingResourceRequests as $request)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $request->resource_name }}</strong> ({{ $request->quantity }} {{ $request->unit }})
                                        <div class="text-muted small">{{ $request->project->name }} - Requested by {{ $request->requestedBy->name }}</div>
                                    </div>
                                    <a href="{{ route('owner.resource-requests.show', $request) }}" class="btn btn-sm btn-primary">Review</a>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No pending resource requests found.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('owner.resource-requests.index') }}" class="btn btn-sm btn-outline-primary">View All Requests</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Add any dashboard-specific JavaScript here
    </script>
@endsection

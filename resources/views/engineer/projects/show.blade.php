@extends('layouts.app')

@section('title', 'View Project - CPMS')

@section('page_title', 'Project Details')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $project->name }}</h1>
        <div>
            <a href="{{ route('engineer.reports.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                <i class="fas fa-file-alt"></i> Submit Report
            </a>
            <a href="{{ route('engineer.resource-requests.create', ['project_id' => $project->id]) }}" class="btn btn-success ms-2">
                <i class="fas fa-tools"></i> Request Resources
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project Name:</div>
                        <div class="col-md-8">{{ $project->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project Owner:</div>
                        <div class="col-md-8">{{ $project->owner->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
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
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Start Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($project->start_date)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">End Date:</div>
                        <div class="col-md-8">{{ $project->end_date ? date('M d, Y', strtotime($project->end_date)) : 'Not set' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Budget:</div>
                        <div class="col-md-8">${{ number_format($project->budget, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Progress:</div>
                        <div class="col-md-8">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $project->progress }}%</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $project->description }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tasks-tab" data-bs-toggle="tab" data-bs-target="#all-tasks" type="button" role="tab" aria-controls="all-tasks" aria-selected="true">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="my-tasks-tab" data-bs-toggle="tab" data-bs-target="#my-tasks" type="button" role="tab" aria-controls="my-tasks" aria-selected="false">My Tasks</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tasks-tab" data-bs-toggle="tab" data-bs-target="#pending-tasks" type="button" role="tab" aria-controls="pending-tasks" aria-selected="false">Pending</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tasks-tab" data-bs-toggle="tab" data-bs-target="#completed-tasks" type="button" role="tab" aria-controls="completed-tasks" aria-selected="false">Completed</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="taskTabsContent">
                        <div class="tab-pane fade show active" id="all-tasks" role="tabpanel" aria-labelledby="all-tasks-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Assigned To</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->tasks as $task)
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
                                                <td>
                                                    @if($task->priority == 'low')
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($task->priority == 'medium')
                                                        <span class="badge bg-warning">Medium</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-danger">High</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="my-tasks" role="tabpanel" aria-labelledby="my-tasks-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $myTasks = $project->tasks->where('assigned_to', auth()->id());
                                        @endphp
                                        @forelse($myTasks as $task)
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
                                                <td>
                                                    @if($task->priority == 'low')
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($task->priority == 'medium')
                                                        <span class="badge bg-warning">Medium</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-danger">High</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No tasks assigned to you for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pending-tasks" role="tabpanel" aria-labelledby="pending-tasks-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Assigned To</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pendingTasks = $project->tasks->whereIn('status', ['backlog', 'todo', 'in_progress', 'review']);
                                        @endphp
                                        @forelse($pendingTasks as $task)
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
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($task->priority == 'low')
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($task->priority == 'medium')
                                                        <span class="badge bg-warning">Medium</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-danger">High</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No pending tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="completed-tasks" role="tabpanel" aria-labelledby="completed-tasks-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Priority</th>
                                            <th>Assigned To</th>
                                            <th>Completed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $completedTasks = $project->tasks->where('status', 'completed');
                                        @endphp
                                        @forelse($completedTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>
                                                    @if($task->priority == 'low')
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($task->priority == 'medium')
                                                        <span class="badge bg-warning">Medium</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-danger">High</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>{{ $task->completed_at ? date('M d, Y', strtotime($task->completed_at)) : date('M d, Y', strtotime($task->updated_at)) }}</td>
                                                <td>
                                                    <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No completed tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Team</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $project->owner->name }}</strong>
                                    <div class="text-muted">Project Owner</div>
                                </div>
                            </div>
                        </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                    </div>
                                </div>
                            </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Reports</h5>
                    <a href="{{ route('engineer.reports.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> New Report
                    </a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->reports->sortByDesc('created_at')->take(5) as $report)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('engineer.reports.show', $report) }}">{{ $report->title }}</a>
                                        <div class="text-muted">{{ date('M d, Y', strtotime($report->created_at)) }}</div>
                                    </div>
                                    <span class="badge bg-{{ $report->status == 'pending' ? 'warning' : ($report->status == 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">No reports submitted yet.</li>
                        @endforelse
                    </ul>
                    @if($project->reports->count() > 5)
                        <div class="mt-3 text-center">
                            <a href="{{ route('engineer.reports.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">View All Reports</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Resource Requests</h5>
                    <a href="{{ route('engineer.resource-requests.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> New Request
                    </a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->resourceRequests->sortByDesc('created_at')->take(5) as $request)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('engineer.resource-requests.show', $request) }}">{{ $request->title }}</a>
                                        <div class="text-muted">{{ date('M d, Y', strtotime($request->created_at)) }}</div>
                                    </div>
                                    <span class="badge bg-{{ $request->status == 'pending' ? 'warning' : ($request->status == 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center">No resource requests submitted yet.</li>
                        @endforelse
                    </ul>
                    @if($project->resourceRequests->count() > 5)
                        <div class="mt-3 text-center">
                            <a href="{{ route('engineer.resource-requests.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">View All Requests</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Files</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->files->sortByDesc('created_at')->take(5) as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i> {{ $file->name }}
                                    <div class="text-muted small">{{ $file->size_formatted }} - {{ date('M d, Y', strtotime($file->created_at)) }}</div>
                                </div>
                                <a href="{{ route('engineer.files.download', $file) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-center">No files uploaded yet.</li>
                        @endforelse
                    </ul>
                    @if($project->files->count() > 5)
                        <div class="mt-3 text-center">
                            <a href="{{ route('engineer.files.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-outline-primary">View All Files</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any project-specific JavaScript can go here
    </script>
@endsection
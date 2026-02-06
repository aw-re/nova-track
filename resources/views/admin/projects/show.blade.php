@extends('layouts.app')

@section('title', 'Project Details - CPMS')

@section('page_title', 'Project Details')



@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-project-diagram me-2"></i> Project Information</span>
            <div>
                <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Project Name</th>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <th>Owner</th>
                            <td>
                                <a href="{{ route('admin.users.show', $project->owner) }}">
                                    {{ $project->owner->name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Budget</th>
                            <td>${{ number_format($project->budget, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Location</th>
                            <td>{{ $project->location ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $project->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i> Project Progress
                        </div>
                        <div class="card-body">
                            @php
                                $totalTasks = $project->tasks->count();
                                $completedTasks = $project->tasks->where('status', 'completed')->count();
                                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                
                                $tasksByStatus = [
                                    'backlog' => $project->tasks->where('status', 'backlog')->count(),
                                    'todo' => $project->tasks->where('status', 'todo')->count(),
                                    'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
                                    'review' => $project->tasks->where('status', 'review')->count(),
                                    'completed' => $completedTasks
                                ];
                            @endphp
                            
                            <div class="text-center mb-4">
                                <h1 class="display-4">{{ $progress }}%</h1>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                                </div>
                            </div>
                            
                            <canvas id="taskStatusChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>Description</h5>
                <div class="card">
                    <div class="card-body">
                        {{ $project->description }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users me-2"></i> Project Members</span>
                    <a href="{{ route('admin.projects.edit', $project) }}#members" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Manage Members
                    </a>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="membersTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="engineers-tab" data-bs-toggle="tab" data-bs-target="#engineers" type="button" role="tab" aria-controls="engineers" aria-selected="true">Engineers</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contractors-tab" data-bs-toggle="tab" data-bs-target="#contractors" type="button" role="tab" aria-controls="contractors" aria-selected="false">Contractors</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="membersTabContent">
                        <div class="tab-pane fade show active" id="engineers" role="tabpanel" aria-labelledby="engineers-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $engineers = $project->members->filter(function($member) {
                                                return $member->role->name === 'engineer';
                                            });
                                        @endphp
                                        
                                        @forelse($engineers as $engineer)
                                            <tr>
                                                <td>{{ $engineer->name }}</td>
                                                <td>{{ $engineer->email }}</td>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $engineer) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No engineers assigned to this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contractors" role="tabpanel" aria-labelledby="contractors-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $contractors = $project->members->filter(function($member) {
                                                return $member->role->name === 'contractor';
                                            });
                                        @endphp
                                        
                                        @forelse($contractors as $contractor)
                                            <tr>
                                                <td>{{ $contractor->name }}</td>
                                                <td>{{ $contractor->email }}</td>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $contractor) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No contractors assigned to this project.</td>
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
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-tasks me-2"></i> Recent Tasks</span>
                    <a href="{{ route('admin.tasks.index', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> View All Tasks
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks->take(5) as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
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
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No tasks found for this project.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file-alt me-2"></i> Recent Reports</span>
                    <a href="{{ route('admin.reports.index', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> View All Reports
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Submitted By</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->reports->take(5) as $report)
                                    <tr>
                                        <td>{{ $report->title }}</td>
                                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No reports found for this project.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <p>Are you sure you want to delete project <strong>{{ $project->name }}</strong>?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Warning: This will also delete all tasks, files, and other data associated with this project.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('taskStatusChart').getContext('2d');
            
            const taskStatusData = {
                'Backlog': {{ $tasksByStatus['backlog'] }},
                'To Do': {{ $tasksByStatus['todo'] }},
                'In Progress': {{ $tasksByStatus['in_progress'] }},
                'Review': {{ $tasksByStatus['review'] }},
                'Completed': {{ $tasksByStatus['completed'] }}
            };
            
            const statusColors = {
                'Backlog': '#6c757d',
                'To Do': '#17a2b8',
                'In Progress': '#007bff',
                'Review': '#ffc107',
                'Completed': '#28a745'
            };
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(taskStatusData),
                    datasets: [{
                        data: Object.values(taskStatusData),
                        backgroundColor: Object.keys(taskStatusData).map(status => statusColors[status]),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
@endsection
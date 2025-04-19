@extends('layouts.app')

@section('title', 'User Details - CPMS')

@section('page_title', 'User Details')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i> Users
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.roles.index') }}">
            <i class="fas fa-user-tag"></i> Roles
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.resources.index') }}">
            <i class="fas fa-tools"></i> Resources
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
            <i class="fas fa-clipboard-list"></i> Activity Logs
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
            <i class="fas fa-bell"></i> Notifications
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.settings.index') }}">
            <i class="fas fa-cog"></i> Settings
        </a>
    </li>
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user me-2"></i> User Information</span>
            <div>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
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
                            <th style="width: 30%">Name</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $user->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $user->address ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i> User Activity
                        </div>
                        <div class="card-body">
                            <canvas id="userActivityChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-project-diagram me-2"></i> Projects ({{ $user->projects->count() }})
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Status</th>
                                    <th>Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
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
                                            @if($project->owner_id == $user->id)
                                                <span class="badge bg-primary">Owner</span>
                                            @else
                                                <span class="badge bg-secondary">Member</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-tasks me-2"></i> Assigned Tasks ({{ $user->assignedTasks->count() }})
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->assignedTasks->take(5) as $task)
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
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No tasks assigned.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($user->assignedTasks->count() > 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.tasks.index', ['assigned_to' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Tasks
                            </a>
                        </div>
                    @endif
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
                    Are you sure you want to delete user <strong>{{ $user->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
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
            const ctx = document.getElementById('userActivityChart').getContext('2d');
            
            // Sample data - in a real app, this would come from the backend
            const activityData = {
                'Projects': {{ $user->projects->count() }},
                'Tasks': {{ $user->assignedTasks->count() }},
                'Resource Requests': {{ $user->resourceRequests->count() }},
                'Files': {{ $user->uploadedFiles->count() ?? 0 }}
            };
            
            // Define colors
            const colors = [
                '#3498db',
                '#2ecc71',
                '#f39c12',
                '#e74c3c'
            ];
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(activityData),
                    datasets: [{
                        data: Object.values(activityData),
                        backgroundColor: colors.slice(0, Object.keys(activityData).length),
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

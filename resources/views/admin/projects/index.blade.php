@extends('layouts.app')

@section('title', __('app.projects_overview') . ' - ' . __('app.app_name'))

@section('page_title', __('app.projects_overview'))

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-users"></i> {{ __('app.users') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.roles.index') }}">
            <i class="fas fa-user-tag"></i> {{ __('app.roles') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.projects.index') }}">
            <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.resources.index') }}">
            <i class="fas fa-tools"></i> {{ __('app.resources') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.reports.index') }}">
            <i class="fas fa-file-alt"></i> {{ __('app.reports') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
            <i class="fas fa-clipboard-list"></i> {{ __('app.activity_logs') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
            <i class="fas fa-bell"></i> {{ __('app.notifications') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.settings.index') }}">
            <i class="fas fa-cog"></i> {{ __('app.settings') }}
        </a>
    </li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-project-diagram me-2"></i> {{ __('app.all_projects') }}</span>
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> {{ __('app.add_new_project') }}
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('admin.projects.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="{{ __('app.search_by_name') }}" name="search" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('app.all_statuses') }}</option>
                                <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>{{ __('app.status_planning') }}</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('app.status_in_progress') }}</option>
                                <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>{{ __('app.status_on_hold') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('app.status_completed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('app.status_cancelled') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="owner_id" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('app.all_owners') }}</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i> {{ __('app.reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project Name</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
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
                                    @php
                                        $totalTasks = $project->tasks->count();
                                        $completedTasks = $project->tasks->where('status', 'completed')->count();
                                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                    @endphp
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                                    </div>
                                </td>
                                <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</td>
                                <td>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.projects.show', $project) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $project->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $project->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $project->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $project->id }}">Confirm Delete</h5>
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $projects->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Projects by Status
                </div>
                <div class="card-body">
                    <canvas id="projectStatusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i> Projects by Owner
                </div>
                <div class="card-body">
                    <canvas id="projectOwnerChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Projects by Status Chart
            const statusCtx = document.getElementById('projectStatusChart').getContext('2d');
            
            // Count projects by status
            const statusCounts = {
                'planning': {{ $projectsByStatus['planning'] ?? 0 }},
                'in_progress': {{ $projectsByStatus['in_progress'] ?? 0 }},
                'on_hold': {{ $projectsByStatus['on_hold'] ?? 0 }},
                'completed': {{ $projectsByStatus['completed'] ?? 0 }},
                'cancelled': {{ $projectsByStatus['cancelled'] ?? 0 }}
            };
            
            // Define colors for each status
            const statusColors = {
                'planning': '#17a2b8',
                'in_progress': '#007bff',
                'on_hold': '#ffc107',
                'completed': '#28a745',
                'cancelled': '#dc3545'
            };
            
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(statusCounts).map(status => status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')),
                    datasets: [{
                        data: Object.values(statusCounts),
                        backgroundColor: Object.keys(statusCounts).map(status => statusColors[status]),
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

            // Projects by Owner Chart
            const ownerCtx = document.getElementById('projectOwnerChart').getContext('2d');
            
            // Data for projects by owner
            const ownerData = @json($projectsByOwner);
            
            new Chart(ownerCtx, {
                type: 'bar',
                data: {
                    labels: ownerData.map(item => item.name),
                    datasets: [{
                        label: 'Number of Projects',
                        data: ownerData.map(item => item.count),
                        backgroundColor: '#3498db',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
@endsection

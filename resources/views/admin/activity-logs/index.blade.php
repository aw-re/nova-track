@extends('layouts.app')

@section('title', 'Activity Logs - CPMS')

@section('page_title', 'Activity Logs')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
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
        <a class="nav-link active" href="{{ route('admin.activity-logs.index') }}">
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clipboard-list me-2"></i> System Activity Logs</span>
            <div>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
                <a href="{{ route('admin.activity-logs.export') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-export"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search by description" name="search" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="user_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="action" class="form-select" onchange="this.form.submit()">
                                <option value="">All Actions</option>
                                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="logged_in" {{ request('action') == 'logged_in' ? 'selected' : '' }}>Logged In</option>
                                <option value="logged_out" {{ request('action') == 'logged_out' ? 'selected' : '' }}>Logged Out</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text">Date Range</span>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
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
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Model Type</th>
                            <th>Model ID</th>
                            <th>IP Address</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('admin.users.show', $log->user) }}">
                                            {{ $log->user->name }}
                                        </a>
                                    @else
                                        System
                                    @endif
                                </td>
                                <td>
                                    @if($log->action == 'created')
                                        <span class="badge bg-success">Created</span>
                                    @elseif($log->action == 'updated')
                                        <span class="badge bg-primary">Updated</span>
                                    @elseif($log->action == 'deleted')
                                        <span class="badge bg-danger">Deleted</span>
                                    @elseif($log->action == 'logged_in')
                                        <span class="badge bg-info">Logged In</span>
                                    @elseif($log->action == 'logged_out')
                                        <span class="badge bg-warning">Logged Out</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>{{ $log->model_type ?? 'N/A' }}</td>
                                <td>{{ $log->model_id ?? 'N/A' }}</td>
                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No activity logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $activityLogs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Activity Over Time
                </div>
                <div class="card-body">
                    <canvas id="activityTimeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Actions Distribution
                </div>
                <div class="card-body">
                    <canvas id="actionsDistributionChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Logs Modal -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearLogsModalLabel">Confirm Clear Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to clear all activity logs?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone. Consider exporting logs before clearing.
                    </div>
                    <form id="clearLogsForm" action="{{ route('admin.activity-logs.clear') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="clear_before" class="form-label">Clear logs before:</label>
                            <input type="date" class="form-control" id="clear_before" name="clear_before">
                            <div class="form-text">Leave blank to clear all logs.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="clearLogsForm" class="btn btn-danger">Clear Logs</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activity Over Time Chart
            const timeCtx = document.getElementById('activityTimeChart').getContext('2d');
            
            const activityData = @json($activityOverTime);
            
            new Chart(timeCtx, {
                type: 'line',
                data: {
                    labels: activityData.map(item => item.date),
                    datasets: [{
                        label: 'Number of Activities',
                        data: activityData.map(item => item.count),
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        tension: 0.1
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
                    }
                }
            });

            // Actions Distribution Chart
            const actionsCtx = document.getElementById('actionsDistributionChart').getContext('2d');
            
            const actionCounts = {
                'Created': {{ $actionDistribution['created'] ?? 0 }},
                'Updated': {{ $actionDistribution['updated'] ?? 0 }},
                'Deleted': {{ $actionDistribution['deleted'] ?? 0 }},
                'Logged In': {{ $actionDistribution['logged_in'] ?? 0 }},
                'Logged Out': {{ $actionDistribution['logged_out'] ?? 0 }},
                'Other': {{ $actionDistribution['other'] ?? 0 }}
            };
            
            const actionColors = {
                'Created': '#28a745',
                'Updated': '#007bff',
                'Deleted': '#dc3545',
                'Logged In': '#17a2b8',
                'Logged Out': '#ffc107',
                'Other': '#6c757d'
            };
            
            new Chart(actionsCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(actionCounts),
                    datasets: [{
                        data: Object.values(actionCounts),
                        backgroundColor: Object.keys(actionCounts).map(action => actionColors[action]),
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

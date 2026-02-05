@extends('layouts.app')

@section('title', __('app.admin_dashboard') . ' - ' . __('app.app_name'))

@section('page_title', __('app.admin_dashboard'))

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
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
        <a class="nav-link" href="{{ route('admin.projects.index') }}">
            <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
            <i class="fas fa-clipboard-list"></i> {{ __('app.activity_logs') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.resources.index') }}">
            <i class="fas fa-boxes"></i> {{ __('app.resources') }}
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
                            <div class="stat-value">{{ $userCount }}</div>
                            <div class="stat-label">{{ __('app.total_users') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
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
                            <div class="stat-value">{{ $projectCount }}</div>
                            <div class="stat-label">{{ __('app.total_projects') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-project-diagram"></i>
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
            <div class="card stat-card danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-value">{{ $completedTaskCount }}</div>
                            <div class="stat-label">{{ __('app.completed_tasks') }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
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
                    <i class="fas fa-chart-pie me-2"></i> {{ __('app.user_distribution') }}
                </div>
                <div class="card-body">
                    <canvas id="userRoleChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i> {{ __('app.recent_activities') }}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentActivities as $activity)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ optional($activity->user)->name ?? 'System' }}</strong> {{ $activity->action }}
                                        <div class="text-muted small">{{ $activity->description }}</div>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">{{ __('app.no_recent_activities') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('userRoleChart').getContext('2d');
            
            // Extract data from the PHP variable
            const roleLabels = {!! json_encode(array_keys($usersByRole->toArray())) !!};
            const roleCounts = {!! json_encode(array_values($usersByRole->toArray())) !!};
            
            // Define colors for each role
            const colors = [
                '#3498db', // admin
                '#2ecc71', // project_owner
                '#f39c12', // engineer
                '#e74c3c'  // contractor
            ];
            
            // Calculate percentages
            const total = roleCounts.reduce((acc, count) => acc + count, 0);
            const percentages = roleCounts.map(count => ((count / total) * 100).toFixed(1));
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: roleLabels.map((role, index) => {
                        const formattedRole = role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ');
                        return `${formattedRole}: ${percentages[index]}%`;
                    }),
                    datasets: [{
                        data: roleCounts,
                        backgroundColor: colors.slice(0, roleLabels.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const percentage = percentages[context.dataIndex];
                                    return `${label.split(':')[0]}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

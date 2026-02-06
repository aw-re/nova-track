@extends('layouts.app')

@section('title', __('app.admin_dashboard') . ' - ' . __('app.app_name'))

@section('page_title', __('app.admin_dashboard'))

@section('sidebar')
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.dashboard') }}"
            class="list-group-item list-group-item-action bg-white text-primary fw-bold border-start border-4 border-primary">
            <i class="fas fa-tachometer-alt me-2"></i> {{ __('app.dashboard') }}
        </a>
        <a href="{{ route('admin.users.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-users me-2"></i> {{ __('app.users') }}
        </a>
        <a href="{{ route('admin.roles.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-user-tag me-2"></i> {{ __('app.roles') }}
        </a>
        <a href="{{ route('admin.projects.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-project-diagram me-2"></i> {{ __('app.projects') }}
        </a>
        <a href="{{ route('admin.tasks.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-tasks me-2"></i> {{ __('app.tasks') }}
        </a>
        <a href="{{ route('admin.activity-logs.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-clipboard-list me-2"></i> {{ __('app.activity_logs') }}
        </a>
        <a href="{{ route('admin.resources.index') }}"
            class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
            <i class="fas fa-boxes me-2"></i> {{ __('app.resources') }}
        </a>
    </div>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-stats-card type="primary" value="{{ $userCount }}" label="{{ __('app.total_users') }}" icon="fas fa-users" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="success" value="{{ $projectCount }}" label="{{ __('app.total_projects') }}"
                icon="fas fa-project-diagram" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="warning" value="{{ $taskCount }}" label="{{ __('app.total_tasks') }}" icon="fas fa-tasks" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="danger" value="{{ $completedTaskCount }}" label="{{ __('app.completed_tasks') }}"
                icon="fas fa-check-circle" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-app-card title="{{ __('app.user_distribution') }}" icon="fas fa-chart-pie">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="userRoleChart"></canvas>
                </div>
            </x-app-card>
        </div>
        <div class="col-lg-6">
            <x-app-card title="{{ __('app.recent_activities') }}" icon="fas fa-history">
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-start">
                                <div class="avatar-sm bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center"
                                    style="width: 35px; height: 35px;">
                                    <i class="fas fa-bolt small"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ optional($activity->user)->name ?? 'System' }}</h6>
                                    <p class="mb-0 small text-muted">{{ $activity->description }}</p>
                                </div>
                            </div>
                            <span
                                class="small text-muted bg-light px-2 py-1 rounded">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">{{ __('app.no_recent_activities') }}</div>
                    @endforelse
                </div>
            </x-app-card>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('userRoleChart');
            if (ctx) {
                const roleLabels = {!! json_encode(array_keys($usersByRole->toArray())) !!};
                const roleCounts = {!! json_encode(array_values($usersByRole->toArray())) !!};
                const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444'];

                // Format labels
                const formattedLabels = roleLabels.map(role =>
                    role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ')
                );

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: formattedLabels,
                        datasets: [{
                            data: roleCounts,
                            backgroundColor: colors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        });
    </script>
@endsection
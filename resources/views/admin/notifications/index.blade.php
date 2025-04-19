@extends('layouts.app')

@section('title', 'Notifications - CPMS')

@section('page_title', 'Notifications')

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
        <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
            <i class="fas fa-clipboard-list"></i> Activity Logs
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.notifications.index') }}">
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
            <span><i class="fas fa-bell me-2"></i> System Notifications</span>
            <div>
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </form>
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearNotificationsModal">
                    <i class="fas fa-trash"></i> Clear All
                </button>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                        All <span class="badge bg-secondary">{{ $allNotifications->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread" type="button" role="tab" aria-controls="unread" aria-selected="false">
                        Unread <span class="badge bg-primary">{{ $unreadNotifications->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="read-tab" data-bs-toggle="tab" data-bs-target="#read" type="button" role="tab" aria-controls="read" aria-selected="false">
                        Read <span class="badge bg-secondary">{{ $readNotifications->count() }}</span>
                    </button>
                </li>
            </ul>
            <div class="tab-content p-3" id="notificationTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    @if($allNotifications->count() > 0)
                        <div class="list-group">
                            @foreach($allNotifications as $notification)
                                <div class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'list-group-item-primary' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            @if(!$notification->read_at)
                                                <span class="badge bg-primary">New</span>
                                            @endif
                                            {{ $notification->data['title'] }}
                                        </h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>
                                            @if($notification->data['type'] == 'project')
                                                <i class="fas fa-project-diagram me-1"></i> Project
                                            @elseif($notification->data['type'] == 'task')
                                                <i class="fas fa-tasks me-1"></i> Task
                                            @elseif($notification->data['type'] == 'report')
                                                <i class="fas fa-file-alt me-1"></i> Report
                                            @elseif($notification->data['type'] == 'resource')
                                                <i class="fas fa-tools me-1"></i> Resource
                                            @elseif($notification->data['type'] == 'user')
                                                <i class="fas fa-user me-1"></i> User
                                            @else
                                                <i class="fas fa-info-circle me-1"></i> System
                                            @endif
                                            {{ $notification->data['sender'] ?? 'System' }}
                                        </small>
                                        <div>
                                            @if($notification->data['action_url'])
                                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                            
                                            @if(!$notification->read_at)
                                                <form action="{{ route('admin.notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Mark as Read
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $allNotifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No notifications found.
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">
                    @if($unreadNotifications->count() > 0)
                        <div class="list-group">
                            @foreach($unreadNotifications as $notification)
                                <div class="list-group-item list-group-item-action list-group-item-primary">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            <span class="badge bg-primary">New</span>
                                            {{ $notification->data['title'] }}
                                        </h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>
                                            @if($notification->data['type'] == 'project')
                                                <i class="fas fa-project-diagram me-1"></i> Project
                                            @elseif($notification->data['type'] == 'task')
                                                <i class="fas fa-tasks me-1"></i> Task
                                            @elseif($notification->data['type'] == 'report')
                                                <i class="fas fa-file-alt me-1"></i> Report
                                            @elseif($notification->data['type'] == 'resource')
                                                <i class="fas fa-tools me-1"></i> Resource
                                            @elseif($notification->data['type'] == 'user')
                                                <i class="fas fa-user me-1"></i> User
                                            @else
                                                <i class="fas fa-info-circle me-1"></i> System
                                            @endif
                                            {{ $notification->data['sender'] ?? 'System' }}
                                        </small>
                                        <div>
                                            @if($notification->data['action_url'])
                                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                            
                                            <form action="{{ route('admin.notifications.mark-as-read', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Mark as Read
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $unreadNotifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No unread notifications found.
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="read" role="tabpanel" aria-labelledby="read-tab">
                    @if($readNotifications->count() > 0)
                        <div class="list-group">
                            @foreach($readNotifications as $notification)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $notification->data['title'] }}</h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>
                                            @if($notification->data['type'] == 'project')
                                                <i class="fas fa-project-diagram me-1"></i> Project
                                            @elseif($notification->data['type'] == 'task')
                                                <i class="fas fa-tasks me-1"></i> Task
                                            @elseif($notification->data['type'] == 'report')
                                                <i class="fas fa-file-alt me-1"></i> Report
                                            @elseif($notification->data['type'] == 'resource')
                                                <i class="fas fa-tools me-1"></i> Resource
                                            @elseif($notification->data['type'] == 'user')
                                                <i class="fas fa-user me-1"></i> User
                                            @else
                                                <i class="fas fa-info-circle me-1"></i> System
                                            @endif
                                            {{ $notification->data['sender'] ?? 'System' }}
                                        </small>
                                        <div>
                                            @if($notification->data['action_url'])
                                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                            
                                            <form action="{{ route('admin.notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $readNotifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No read notifications found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i> Notifications by Type
                </div>
                <div class="card-body">
                    <canvas id="notificationTypeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i> Notifications Over Time
                </div>
                <div class="card-body">
                    <canvas id="notificationTimeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Clear Notifications Modal -->
    <div class="modal fade" id="clearNotificationsModal" tabindex="-1" aria-labelledby="clearNotificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clearNotificationsModalLabel">Confirm Clear Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to clear all notifications?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone.
                    </div>
                    <form id="clearNotificationsForm" action="{{ route('admin.notifications.clear-all') }}" method="POST">
                        @csrf
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="clear_read_only" name="clear_read_only">
                            <label class="form-check-label" for="clear_read_only">
                                Clear only read notifications
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="clearNotificationsForm" class="btn btn-danger">Clear Notifications</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notifications by Type Chart
            const typeCtx = document.getElementById('notificationTypeChart').getContext('2d');
            
            const notificationTypes = {
                'Project': {{ $notificationsByType['project'] ?? 0 }},
                'Task': {{ $notificationsByType['task'] ?? 0 }},
                'Report': {{ $notificationsByType['report'] ?? 0 }},
                'Resource': {{ $notificationsByType['resource'] ?? 0 }},
                'User': {{ $notificationsByType['user'] ?? 0 }},
                'System': {{ $notificationsByType['system'] ?? 0 }}
            };
            
            const typeColors = {
                'Project': '#3498db',
                'Task': '#2ecc71',
                'Report': '#f39c12',
                'Resource': '#9b59b6',
                'User': '#e74c3c',
                'System': '#34495e'
            };
            
            new Chart(typeCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(notificationTypes),
                    datasets: [{
                        label: 'Number of Notifications',
                        data: Object.values(notificationTypes),
                        backgroundColor: Object.keys(notificationTypes).map(type => typeColors[type]),
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

            // Notifications Over Time Chart
            const timeCtx = document.getElementById('notificationTimeChart').getContext('2d');
            
            const notificationData = @json($notificationsOverTime);
            
            new Chart(timeCtx, {
                type: 'line',
                data: {
                    labels: notificationData.map(item => item.date),
                    datasets: [{
                        label: 'Number of Notifications',
                        data: notificationData.map(item => item.count),
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
        });
    </script>
@endsection

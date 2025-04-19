@extends('layouts.app')

@section('title', 'Reports Overview - CPMS')

@section('page_title', 'Reports Overview')

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
        <a class="nav-link active" href="{{ route('admin.reports.index') }}">
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-file-alt me-2"></i> All Reports</span>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search by title" name="search" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="project_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="progress" {{ request('type') == 'progress' ? 'selected' : '' }}>Progress</option>
                                <option value="incident" {{ request('type') == 'incident' ? 'selected' : '' }}>Incident</option>
                                <option value="inspection" {{ request('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="quality" {{ request('type') == 'quality' ? 'selected' : '' }}>Quality</option>
                                <option value="financial" {{ request('type') == 'financial' ? 'selected' : '' }}>Financial</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
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
                            <th>Title</th>
                            <th>Project</th>
                            <th>Type</th>
                            <th>Submitted By</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->title }}</td>
                                <td>{{ $report->project ? $report->project->name : 'N/A' }}</td>
                                <td>
                                    @if($report->type == 'progress')
                                        <span class="badge bg-info">Progress</span>
                                    @elseif($report->type == 'incident')
                                        <span class="badge bg-danger">Incident</span>
                                    @elseif($report->type == 'inspection')
                                        <span class="badge bg-warning">Inspection</span>
                                    @elseif($report->type == 'quality')
                                        <span class="badge bg-success">Quality</span>
                                    @elseif($report->type == 'financial')
                                        <span class="badge bg-primary">Financial</span>
                                    @endif
                                </td>
                                <td>{{ $report->submittedBy ? $report->submittedBy->name : 'N/A' }}</td>
                                <td>
                                    @if($report->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($report->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($report->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($report->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $report->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $report->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Approve Modal -->
                                    <div class="modal fade" id="approveModal{{ $report->id }}" tabindex="-1" aria-labelledby="approveModalLabel{{ $report->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approveModalLabel{{ $report->id }}">Approve Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.reports.approve', $report) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to approve this report?</p>
                                                        <div class="mb-3">
                                                            <label for="feedback" class="form-label">Feedback (Optional)</label>
                                                            <textarea class="form-control" id="feedback" name="feedback" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Approve</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $report->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $report->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $report->id }}">Reject Report</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.reports.reject', $report) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reject this report?</p>
                                                        <div class="mb-3">
                                                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Reports by Type
                </div>
                <div class="card-body">
                    <canvas id="reportTypeChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i> Reports by Status
                </div>
                <div class="card-body">
                    <canvas id="reportStatusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reports by Type Chart
            const typeCtx = document.getElementById('reportTypeChart').getContext('2d');
            
            const reportTypes = {
                'Progress': {{ $reportsByType['progress'] ?? 0 }},
                'Incident': {{ $reportsByType['incident'] ?? 0 }},
                'Inspection': {{ $reportsByType['inspection'] ?? 0 }},
                'Quality': {{ $reportsByType['quality'] ?? 0 }},
                'Financial': {{ $reportsByType['financial'] ?? 0 }}
            };
            
            const typeColors = {
                'Progress': '#17a2b8',
                'Incident': '#dc3545',
                'Inspection': '#ffc107',
                'Quality': '#28a745',
                'Financial': '#007bff'
            };
            
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(reportTypes),
                    datasets: [{
                        data: Object.values(reportTypes),
                        backgroundColor: Object.keys(reportTypes).map(type => typeColors[type]),
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

            // Reports by Status Chart
            const statusCtx = document.getElementById('reportStatusChart').getContext('2d');
            
            const reportStatus = {
                'Pending': {{ $reportsByStatus['pending'] ?? 0 }},
                'Approved': {{ $reportsByStatus['approved'] ?? 0 }},
                'Rejected': {{ $reportsByStatus['rejected'] ?? 0 }}
            };
            
            const statusColors = {
                'Pending': '#ffc107',
                'Approved': '#28a745',
                'Rejected': '#dc3545'
            };
            
            new Chart(statusCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(reportStatus),
                    datasets: [{
                        label: 'Number of Reports',
                        data: Object.values(reportStatus),
                        backgroundColor: Object.keys(reportStatus).map(status => statusColors[status]),
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

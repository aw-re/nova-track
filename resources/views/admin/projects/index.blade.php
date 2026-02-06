@extends('layouts.app')

@section('title', __('app.projects_overview') . ' - ' . __('app.app_name'))

@section('page_title', __('app.projects_overview'))



@section('content')
    <x-app-card title="{{ __('app.all_projects') }}" icon="fas fa-project-diagram">
        <x-slot name="actions">
            <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                <i class="fas fa-plus me-1"></i> {{ __('app.add_new_project') }}
            </a>
        </x-slot>

        <!-- Filters -->
        <div class="bg-light p-3 rounded mb-4">
            <form action="{{ route('admin.projects.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0"
                            placeholder="{{ __('app.search_by_name') }}" name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('app.all_statuses') }}</option>
                        <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>
                            {{ __('app.status_planning') }}</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>
                            {{ __('app.status_in_progress') }}</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>
                            {{ __('app.status_on_hold') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            {{ __('app.status_completed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            {{ __('app.status_cancelled') }}</option>
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
                    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-sync-alt me-1"></i> {{ __('app.reset') }}
                    </a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0">ID</th>
                        <th class="border-0">Project Name</th>
                        <th class="border-0">Owner</th>
                        <th class="border-0">Status</th>
                        <th class="border-0" style="width: 20%;">Progress</th>
                        <th class="border-0">Dates</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="text-muted small">#{{ $project->id }}</td>
                            <td class="fw-bold">{{ $project->name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                                        style="width: 24px; height: 24px; font-size: 10px;">
                                        {{ substr($project->owner->name, 0, 1) }}
                                    </div>
                                    <span class="small">{{ $project->owner->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($project->status == 'planning')
                                    <span class="badge bg-info bg-opacity-10 text-info">Planning</span>
                                @elseif($project->status == 'in_progress')
                                    <span class="badge bg-primary bg-opacity-10 text-primary">In Progress</span>
                                @elseif($project->status == 'on_hold')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">On Hold</span>
                                @elseif($project->status == 'completed')
                                    <span class="badge bg-success bg-opacity-10 text-success">Completed</span>
                                @elseif($project->status == 'cancelled')
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $totalTasks = $project->tasks->count();
                                    $completedTasks = $project->tasks->where('status', 'completed')->count();
                                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                            style="width: {{ $progress }}%;"></div>
                                    </div>
                                    <span class="ms-2 small text-muted">{{ $progress }}%</span>
                                </div>
                            </td>
                            <td class="small text-muted">
                                <div><i class="far fa-calendar-alt me-1"></i>
                                    {{ $project->start_date ? $project->start_date->format('M d') : '-' }}</div>
                                <div><i class="far fa-flag me-1"></i>
                                    {{ $project->end_date ? $project->end_date->format('M d') : '-' }}</div>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.projects.show', $project) }}"
                                        class="btn btn-sm btn-light text-primary" data-bs-toggle="tooltip" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                        class="btn btn-sm btn-light text-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-light text-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $project->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $project->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-lg">
                                            <div class="modal-header border-bottom-0">
                                                <h5 class="modal-title fw-bold">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <div class="avatar-lg bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-exclamation-triangle fs-3"></i>
                                                </div>
                                                <p class="mb-2">Are you sure you want to delete project
                                                    <strong>{{ $project->name }}</strong>?</p>
                                                <p class="text-muted small mb-0">This action cannot be undone. All associated
                                                    tasks and files will be permanently removed.</p>
                                            </div>
                                            <div class="modal-footer border-top-0 justify-content-center pb-4">
                                                <button type="button" class="btn btn-light px-4"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.projects.destroy', $project) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger px-4 fw-bold">Delete
                                                        Project</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open fs-1 mb-3 opacity-25"></i>
                                    <p>No projects found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $projects->appends(request()->query())->links() }}
        </div>
    </x-app-card>

    <div class="row g-4">
        <div class="col-md-6">
            <x-app-card title="Projects by Status" icon="fas fa-chart-pie">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="projectStatusChart"></canvas>
                </div>
            </x-app-card>
        </div>
        <div class="col-md-6">
            <x-app-card title="Projects by Owner" icon="fas fa-chart-bar">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="projectOwnerChart"></canvas>
                </div>
            </x-app-card>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Projects by Status Chart
            const statusCtx = document.getElementById('projectStatusChart');
            if (statusCtx) {
                const statusCounts = {
                    'planning': {{ $projectsByStatus['planning'] ?? 0 }},
                    'in_progress': {{ $projectsByStatus['in_progress'] ?? 0 }},
                    'on_hold': {{ $projectsByStatus['on_hold'] ?? 0 }},
                    'completed': {{ $projectsByStatus['completed'] ?? 0 }},
                    'cancelled': {{ $projectsByStatus['cancelled'] ?? 0 }}
                    };

                const statusColors = ['#17a2b8', '#007bff', '#ffc107', '#28a745', '#dc3545'];
                const statusLabels = Object.keys(statusCounts).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '));

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: Object.values(statusCounts),
                            backgroundColor: statusColors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        cutout: '70%'
                    }
                });
            }

            // Projects by Owner Chart
            const ownerCtx = document.getElementById('projectOwnerChart');
            if (ownerCtx) {
                const ownerData = @json($projectsByOwner);

                new Chart(ownerCtx, {
                    type: 'bar',
                    data: {
                        labels: ownerData.map(item => item.name),
                        datasets: [{
                            label: 'Number of Projects',
                            data: ownerData.map(item => item.count),
                            backgroundColor: '#4f46e5',
                            borderRadius: 4,
                            barThickness: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { drawBorder: false }
                            },
                            x: {
                                grid: { display: false }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
@endsection
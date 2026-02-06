@extends('layouts.app')

@section('title', __('app.owner_dashboard') . ' - ' . __('app.app_name'))

@section('page_title', __('app.owner_dashboard'))



@section('content')
    <!-- Statistics Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-stats-card type="primary" value="{{ $projectCount }}" label="{{ __('app.my_projects') }}"
                icon="fas fa-project-diagram" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="success" value="{{ $taskCount }}" label="{{ __('app.total_tasks') }}" icon="fas fa-tasks" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="warning" value="{{ $pendingReportCount }}" label="{{ __('app.pending_reports') }}"
                icon="fas fa-file-alt" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="danger" value="{{ $pendingResourceRequestCount }}" label="{{ __('app.pending_requests') }}"
                icon="fas fa-tools" />
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Projects -->
        <div class="col-lg-6">
            <x-app-card title="{{ __('app.recent_projects') }}" icon="fas fa-project-diagram">
                <x-slot name="actions">
                    <a href="{{ route('owner.projects.index') }}" class="btn btn-sm btn-light text-primary">
                        <i class="fas fa-arrow-right"></i> {{ __('app.view_all') }}
                    </a>
                </x-slot>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">{{ __('app.name') }}</th>
                                <th class="border-0">{{ __('app.status') }}</th>
                                <th class="border-0">{{ __('app.progress') }}</th>
                                <th class="border-0 text-end">{{ __('app.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $project->name }}</td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td style="width: 30%">
                                        @php
                                            $total = $project->tasks->count();
                                            $done = $project->tasks->where('status', 'completed')->count();
                                            $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%">
                                            </div>
                                        </div>
                                        <div class="small text-muted mt-1">{{ $pct }}%</div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('owner.projects.show', $project) }}"
                                            class="btn btn-icon btn-sm btn-light">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">{{ __('app.no_projects_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>
        </div>

        <!-- Recent Tasks -->
        <div class="col-lg-6">
            <x-app-card title="Recent Tasks" icon="fas fa-tasks">
                <x-slot name="actions">
                    <a href="{{ route('owner.tasks.index') }}" class="btn btn-sm btn-light text-primary">
                        <i class="fas fa-arrow-right"></i> View All
                    </a>
                </x-slot>

                <div class="list-group list-group-flush">
                    @forelse($recentTasks as $task)
                        <div
                            class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center mb-2 rounded hover-bg-light">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-light text-primary me-3 rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 40px; height: 40px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $task->title }}</h6>
                                    <small class="text-muted">{{ $task->project->name }} • Due
                                        {{ $task->due_date ? date('M d', strtotime($task->due_date)) : 'N/A' }}</small>
                                </div>
                            </div>
                            <x-badge :status="$task->status" />
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No tasks found.</div>
                    @endforelse
                </div>
            </x-app-card>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'Contractor Dashboard - CPMS')

@section('page_title', 'Contractor Dashboard')



@section('content')
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
        <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
            <i class="fas fa-envelope me-2"></i> You have {{ $pendingInvitations->count() }} pending project invitation(s).
            <a href="{{ route('contractor.invitations.index') }}" class="fw-bold text-decoration-none ms-2">View invitations <i
                    class="fas fa-arrow-right"></i></a>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-stats-card type="primary" value="{{ $assignedTaskCount }}" label="Assigned Tasks" icon="fas fa-tasks" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="success" value="{{ $completedTaskCount }}" label="Completed Tasks"
                icon="fas fa-check-circle" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="warning" value="{{ $resourceRequestCount }}" label="Resource Requests"
                icon="fas fa-tools" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="danger" value="{{ $projectCount }}" label="Projects" icon="fas fa-project-diagram" />
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <x-app-card title="Task Overview" icon="fas fa-chart-bar">
                <div class="d-flex align-items-center justify-content-center" style="height: 200px;">
                    <!-- Simple Progress Placeholder until we add Charts -->
                    <div class="text-center">
                        <h3 class="fw-bold text-primary">{{ $completedTaskCount }}/{{ $assignedTaskCount }}</h3>
                        <p class="text-muted">Tasks Completed</p>
                        <div class="progress" style="width: 200px; height: 10px;">
                            @php $pct = $assignedTaskCount > 0 ? ($completedTaskCount / $assignedTaskCount) * 100 : 0; @endphp
                            <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            </x-app-card>
        </div>
        <div class="col-lg-6">
            <x-app-card title="My Tasks" icon="fas fa-list">
                <ul class="nav nav-pills mb-3" id="taskTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active py-1 px-3" data-bs-toggle="tab"
                            data-bs-target="#pen">Pending</button></li>
                    <li class="nav-item ms-2"><button class="nav-link py-1 px-3" data-bs-toggle="tab"
                            data-bs-target="#prog">In Progress</button></li>
                </ul>
                <div class="tab-content" style="max-height: 250px; overflow-y: auto;">
                    <div class="tab-pane fade show active" id="pen">
                        <ul class="list-group list-group-flush">
                            @forelse($pendingTasks as $task)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $task->title }}</div>
                                        <small class="text-muted">{{ $task->project->name }}</small>
                                    </div>
                                    <a href="{{ route('contractor.tasks.start', $task) }}"
                                        class="btn btn-sm btn-success rounded-pill px-3">Start</a>
                                </li>
                            @empty
                                <div class="text-center text-muted py-3">No pending tasks.</div>
                            @endforelse
                        </ul>
                    </div>
                    <div class="tab-pane fade" id="prog">
                        <ul class="list-group list-group-flush">
                            @forelse($inProgressTasks as $task)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold">{{ $task->title }}</div>
                                        <small class="text-muted">Due:
                                            {{ $task->due_date ? date('M d', strtotime($task->due_date)) : '-' }}</small>
                                    </div>
                                    <a href="{{ route('contractor.tasks.complete', $task) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3">Done</a>
                                </li>
                            @empty
                                <div class="text-center text-muted py-3">No active tasks.</div>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </x-app-card>
        </div>
    </div>
@endsection
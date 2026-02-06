@use('App\Enums\TaskStatusEnum')
@use('App\Enums\TaskPriorityEnum')
@extends('layouts.app')

@section('title', __('app.view') . ' ' . __('app.task') . ' - ' . __('app.app_name'))

@section('page_title', __('app.view') . ' ' . __('app.task'))

@section('sidebar')
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
        <i class="fas fa-tachometer-alt me-2"></i> {{ __('app.dashboard') }}
    </a>
    <a href="{{ route('admin.projects.index') }}" class="list-group-item list-group-item-action bg-transparent text-secondary fw-bold">
        <i class="fas fa-project-diagram me-2"></i> {{ __('app.projects') }}
    </a>
    <a href="{{ route('admin.tasks.index') }}" class="list-group-item list-group-item-action bg-white text-primary fw-bold border-start border-4 border-primary">
        <i class="fas fa-tasks me-2"></i> {{ __('app.tasks') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i> Task Details</h5>
                    <div class="d-flex gap-2">
                         <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
                        </a>
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-warning btn-sm text-white shadow-sm">
                            <i class="fas fa-edit"></i> {{ __('app.edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-9">
                            <h2 class="fw-bold mb-3">{{ $task->title }}</h2>
                            <div class="d-flex gap-2 mb-3">
                                <x-badge :status="$task->status" />
                                <x-badge :status="$task->priority" />
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="text-muted small text-uppercase ls-1">Task ID</div>
                            <div class="fw-bold fs-5 text-dark">#{{ $task->id }}</div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="text-muted small mb-1">Project</div>
                            <div class="fw-bold">
                                @if($task->project)
                                    <a href="{{ route('admin.projects.show', $task->project) }}" class="text-decoration-none text-dark">
                                        <i class="fas fa-project-diagram text-primary me-1"></i> {{ $task->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="text-muted small mb-1">Assigned To</div>
                            @if($task->assignedTo)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 10px;">
                                            {{ substr($task->assignedTo->name, 0, 1) }}
                                        </div>
                                        <span class="fw-bold">{{ $task->assignedTo->name }}</span>
                                    </div>
                            @else
                                <span class="badge bg-light text-muted border">Unassigned</span>
                            @endif
                        </div>
                         <div class="col-md-4 mb-3">
                            <div class="text-muted small mb-1">Assigned By</div>
                             @if($task->assignedBy)
                                    <div class="d-flex align-items-center">
                                       <span class="text-dark fw-bold">{{ $task->assignedBy->name }}</span>
                                    </div>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    <div class="mb-4">
                        <h6 class="fw-bold text-muted text-uppercase small ls-1 mb-3">Description</h6>
                        <div class="bg-light p-3 rounded-3 border">
                            @if($task->description)
                                <p class="mb-0 text-break" style="white-space: pre-line; color: #4a5568">{{ $task->description }}</p>
                            @else
                                <p class="mb-0 text-muted fst-italic">No description provided.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-3 mb-3">
                            <div class="text-muted small mb-1"><i class="far fa-calendar-alt me-1"></i> Start Date</div>
                            <div class="fw-bold">{{ $task->start_date ? $task->start_date->format('M d, Y') : '-' }}</div>
                        </div>
                         <div class="col-md-3 mb-3">
                            <div class="text-muted small mb-1"><i class="far fa-calendar-check me-1"></i> Due Date</div>
                             <div class="fw-bold {{ $task->due_date && $task->due_date < now() && $task->status !== TaskStatusEnum::COMPLETED ? 'text-danger' : '' }}">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '-' }}
                            </div>
                        </div>
                         <div class="col-md-3 mb-3">
                            <div class="text-muted small mb-1"><i class="far fa-clock me-1"></i> Est. Hours</div>
                            <div class="fw-bold">{{ $task->estimated_hours ?? '-' }} hrs</div>
                        </div>
                         <div class="col-md-3 mb-3">
                            <div class="text-muted small mb-1"><i class="fas fa-stopwatch me-1"></i> Actual Hours</div>
                            <div class="fw-bold">{{ $task->actual_hours ?? '-' }} hrs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
             <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Recent Updates</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($task->updates->sortByDesc('created_at')->take(5) as $update)
                            <li class="list-group-item px-3 py-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar-sm bg-light text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-history small"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <div class="small fw-bold">{{ $update->user->name ?? 'System' }}</div>
                                            <div class="small text-muted mb-1" style="font-size: 0.75rem">{{ $update->created_at->diffForHumans() }}</div>
                                        </div>
                                        <p class="mb-0 small text-dark mt-1">{{ $update->comment ?? 'Status changed' }}</p>
                                        @if($update->old_status && $update->new_status)
                                            <div class="mt-2">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.7rem">{{ $update->old_status }}</span>
                                                <i class="fas fa-arrow-right mx-1 small text-muted" style="font-size: 0.7rem"></i>
                                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.7rem">{{ $update->new_status }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">No updates yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@use('App\Enums\TaskStatusEnum')
@use('App\Enums\TaskPriorityEnum')
@extends('layouts.app')

@section('title', __('app.tasks') . ' - ' . __('app.app_name'))

@section('page_title', __('app.tasks'))



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('app.tasks') }}</h1>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-2"></i> {{ __('app.create') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.tasks.index') }}">
                        {{ __('app.view_all') }}
                    </a>
                </li>
                @foreach(TaskStatusEnum::cases() as $status)
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == $status->value ? 'active' : '' }}" 
                           href="{{ route('admin.tasks.index', ['status' => $status->value]) }}">
                           {{ $status->label() }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary text-uppercase small">
                        <tr>
                            <th class="ps-4">{{ __('app.name') }}</th>
                            <th>{{ __('app.projects') }}</th>
                            <th>Assigned To</th>
                            <th>{{ __('app.status') }}</th>
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th class="text-end pe-4">{{ __('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $task->title }}</td>
                                <td>
                                    @if($task->project)
                                        <a href="{{ route('admin.projects.show', $task->project) }}" class="text-decoration-none fw-bold text-secondary">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold border" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($task->assignedTo->name, 0, 1) }}
                                            </div>
                                            <span class="text-dark">{{ $task->assignedTo->name }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <x-badge :status="$task->status" />
                                </td>
                                <td>
                                    <x-badge :status="$task->priority" />
                                </td>
                                <td>
                                    @if($task->due_date)
                                        <div class="d-flex align-items-center {{ $task->due_date < now() && $task->status !== TaskStatusEnum::COMPLETED ? 'text-danger' : 'text-secondary' }}">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            <span class="fw-bold">{{ $task->due_date->format('M d, Y') }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-secondary"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.tasks.show', $task) }}">
                                                    <i class="fas fa-eye me-2 text-primary"></i> {{ __('app.view') }}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.tasks.edit', $task) }}">
                                                    <i class="fas fa-edit me-2 text-warning"></i> {{ __('app.edit') }}
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('{{ __('app.confirm_delete') }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash me-2"></i> {{ __('app.delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted opacity-50 mb-3">
                                        <i class="fas fa-tasks fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted">No tasks found</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $tasks->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
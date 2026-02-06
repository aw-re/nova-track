@use('App\Enums\TaskStatusEnum')
@use('App\Enums\TaskPriorityEnum')
@extends('layouts.app')

@section('title', __('app.edit') . ' ' . __('app.task') . ' - ' . __('app.app_name'))

@section('page_title', __('app.edit') . ' ' . __('app.task'))

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
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i> {{ __('app.edit') }}: {{ $task->title }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">{{ __('app.name') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="project_id" class="form-label fw-bold">{{ __('app.projects') }}</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                                <option value="" disabled>Select Project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                    @foreach(TaskPriorityEnum::cases() as $priority)
                                        <option value="{{ $priority->value }}" {{ old('priority', $task->priority instanceof \UnitEnum ? $task->priority->value : $task->priority) == $priority->value ? 'selected' : '' }}>
                                            {{ $priority->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">{{ __('app.status') }}</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    @foreach(TaskStatusEnum::cases() as $status)
                                        <option value="{{ $status->value }}" {{ old('status', $task->status instanceof \UnitEnum ? $task->status->value : $task->status) == $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assigned_to" class="form-label fw-bold">Assign To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role ?? 'User' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estimated_hours" class="form-label fw-bold">Estimated Hours</label>
                                <input type="number" step="0.5" class="form-control @error('estimated_hours') is-invalid @enderror" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $task->estimated_hours) }}">
                                @error('estimated_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-bold">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">{{ __('app.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('app.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
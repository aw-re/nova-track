@extends('layouts.app')

@section('title', 'Edit Task - CPMS')

@section('page_title', 'Edit Task')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.projects.index') }}">
            <i class="fas fa-project-diagram"></i> My Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('owner.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('owner.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Task: {{ $task->title }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('owner.tasks.update', $task) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                    <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                    <div class="mb-3">
    <label for="assigned_to" class="form-label">Assign To</label>
    <select class="form-select @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to">
        <option value="">Unassigned</option>
        <optgroup label="Engineers">
            @foreach($engineers as $engineer)
                <option value="{{ $engineer->id }}" {{ old('assigned_to', isset($task) ? $task->assigned_to : '') == $engineer->id ? 'selected' : '' }}>
                    {{ $engineer->name }}
                </option>
            @endforeach
        </optgroup>
        <optgroup label="Contractors">
            @foreach($contractors as $contractor)
                <option value="{{ $contractor->id }}" {{ old('assigned_to', isset($task) ? $task->assigned_to : '') == $contractor->id ? 'selected' : '' }}>
                    {{ $contractor->name }}
                </option>
            @endforeach
        </optgroup>
    </select>
    @error('assigned_to')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? date('Y-m-d', strtotime($task->due_date)) : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="backlog" {{ old('status', $task->status) == 'backlog' ? 'selected' : '' }}>Backlog</option>
                                <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="estimated_hours" class="form-label">Estimated Hours</label>
                    <input type="number" step="0.5" min="0" class="form-control @error('estimated_hours') is-invalid @enderror" id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours', $task->estimated_hours) }}">
                    @error('estimated_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Filter users based on selected project
        document.getElementById('project_id').addEventListener('change', function() {
            const projectId = this.value;
            if (projectId) {
                // This would typically be an AJAX call to get project members
                // For now, we'll just use the existing users dropdown
                console.log('Project selected: ' + projectId);
            }
        });
    </script>
@endsection

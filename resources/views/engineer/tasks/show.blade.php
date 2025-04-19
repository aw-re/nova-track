@extends('layouts.app')

@section('title', 'View Task - CPMS')

@section('page_title', 'Task Details')

@section('sidebar')
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('engineer.tasks.index') }}">
            <i class="fas fa-tasks"></i> Tasks
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.resource-requests.index') }}">
            <i class="fas fa-tools"></i> Resource Requests
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('engineer.files.index') }}">
            <i class="fas fa-file"></i> Files
        </a>
    </li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $task->title }}</h1>
        <div>
            @if($task->assigned_to == auth()->id() && in_array($task->status, ['todo', 'in_progress', 'review']))
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-check-circle"></i> Update Status
                </button>
            @endif
            <a href="{{ route('engineer.projects.show', $task->project) }}" class="btn btn-primary ms-2">
                <i class="fas fa-project-diagram"></i> View Project
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project:</div>
                        <div class="col-md-8">
                            <a href="{{ route('engineer.projects.show', $task->project) }}">{{ $task->project->name }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($task->status == 'backlog')
                                <span class="badge bg-secondary">Backlog</span>
                            @elseif($task->status == 'todo')
                                <span class="badge bg-info">To Do</span>
                            @elseif($task->status == 'in_progress')
                                <span class="badge bg-primary">In Progress</span>
                            @elseif($task->status == 'review')
                                <span class="badge bg-warning">Review</span>
                            @elseif($task->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Priority:</div>
                        <div class="col-md-8">
                            @if($task->priority == 'low')
                                <span class="badge bg-success">Low</span>
                            @elseif($task->priority == 'medium')
                                <span class="badge bg-warning">Medium</span>
                            @elseif($task->priority == 'high')
                                <span class="badge bg-danger">High</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Assigned To:</div>
                        <div class="col-md-8">{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created By:</div>
                        <div class="col-md-8">{{ $task->createdBy->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($task->created_at)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Due Date:</div>
                        <div class="col-md-8">{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</div>
                    </div>
                    @if($task->status == 'completed')
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Completed Date:</div>
                            <div class="col-md-8">{{ $task->completed_at ? date('M d, Y', strtotime($task->completed_at)) : date('M d, Y', strtotime($task->updated_at)) }}</div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Estimated Hours:</div>
                        <div class="col-md-8">{{ $task->estimated_hours ? $task->estimated_hours . ' hours' : 'Not estimated' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Actual Hours:</div>
                        <div class="col-md-8">{{ $task->actual_hours ? $task->actual_hours . ' hours' : 'Not recorded' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $task->description }}</div>
                    </div>
                </div>
            </div>

            @if($task->assigned_to == auth()->id() && in_array($task->status, ['todo', 'in_progress', 'review']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Log Work</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('engineer.tasks.log-work', $task) }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hours_spent" class="form-label">Hours Spent <span class="text-danger">*</span></label>
                                        <input type="number" step="0.5" min="0" class="form-control @error('hours_spent') is-invalid @enderror" id="hours_spent" name="hours_spent" required>
                                        @error('hours_spent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="work_date" class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('work_date') is-invalid @enderror" id="work_date" name="work_date" value="{{ date('Y-m-d') }}" required>
                                        @error('work_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="work_description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('work_description') is-invalid @enderror" id="work_description" name="work_description" rows="3" required></textarea>
                                @error('work_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Log Work</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Work Log</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Hours</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($task->workLogs as $log)
                                    <tr>
                                        <td>{{ date('M d, Y', strtotime($log->work_date)) }}</td>
                                        <td>{{ $log->user->name }}</td>
                                        <td>{{ $log->hours_spent }}</td>
                                        <td>{{ $log->description }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No work logs recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Attachments</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($task->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i> {{ $file->name }}
                                    <div class="text-muted small">{{ $file->size_formatted }}</div>
                                </div>
                                <a href="{{ route('engineer.files.download', $file) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">No attachments found for this task.</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <form action="{{ route('engineer.files.upload-to-task', $task) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Upload File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Comments</h5>
                </div>
                <div class="card-body">
                    <div class="comments-list mb-3">
                        @forelse($task->comments as $comment)
                            <div class="comment mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $comment->user->name }}</strong>
                                                <span class="text-muted small ms-2">{{ date('M d, Y h:i A', strtotime($comment->created_at)) }}</span>
                                            </div>
                                        </div>
                                        <p class="mb-0 mt-1">{{ $comment->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">No comments yet.</p>
                        @endforelse
                    </div>
                    <form action="{{ route('engineer.tasks.comment', $task) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="comment" class="form-label">Add Comment</label>
                            <textarea class="form-control" id="comment" name="content" rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    @if($task->assigned_to == auth()->id() && in_array($task->status, ['todo', 'in_progress', 'review']))
        <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateStatusModalLabel">Update Task Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('engineer.tasks.update-status', $task) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    @if($task->status == 'todo')
                                        <option value="in_progress">In Progress</option>
                                    @elseif($task->status == 'in_progress')
                                        <option value="review">Review</option>
                                        <option value="completed">Completed</option>
                                    @elseif($task->status == 'review')
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                            </div>
                            @if(!$task->actual_hours)
                                <div class="mb-3">
                                    <label for="actual_hours" class="form-label">Total Hours Spent <span class="text-danger">*</span></label>
                                    <input type="number" step="0.5" min="0" class="form-control" id="actual_hours" name="actual_hours" required>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Any task-specific JavaScript can go here
    </script>
@endsection

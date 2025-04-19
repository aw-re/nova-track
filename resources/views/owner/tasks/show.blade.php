@extends('layouts.app')

@section('title', 'View Task - CPMS')

@section('page_title', 'Task Details')

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $task->title }}</h1>
        <div>
            <a href="{{ route('owner.tasks.edit', $task) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Task
            </a>
            <a href="{{ route('owner.projects.show', $task->project) }}" class="btn btn-primary ms-2">
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
                            <a href="{{ route('owner.projects.show', $task->project) }}">{{ $task->project->name }}</a>
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
                        <div class="col-md-4 fw-bold">Assigned By:</div>
                        <div class="col-md-8">{{ $task->assignedBy ? $task->assignedBy->name : 'System' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Due Date:</div>
                        <div class="col-md-8">{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Estimated Hours:</div>
                        <div class="col-md-8">{{ $task->estimated_hours ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($task->created_at)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Last Updated:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($task->updated_at)) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $task->description }}</div>
                    </div><br>
                    <div class="col-md-4 fw-bold">Comments:</div>
                    <br>
                    @foreach($task->comments as $comment)
            <div class="mb-3 border-bottom pb-3">
                <strong>{{ $comment->user->name }}</strong>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                <p>{{ $comment->content }}</p>
            </div>
        @endforeach

            </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Task Updates</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUpdateModal">
                        <i class="fas fa-plus"></i> Add Update
                    </button>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($task->updates as $update)
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-text">{{ date('M d', strtotime($update->created_at)) }}</div>
                                    <div class="timeline-item-marker-indicator bg-primary"></div>
                                </div>
                                <div class="timeline-item-content">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong>{{ $update->user->name }}</strong>
                                            <span class="text-muted small ms-2">{{ date('h:i A', strtotime($update->created_at)) }}</span>
                                        </div>
                                        @if($update->status_change)
                                            <span class="badge bg-info">Status Changed</span>
                                        @endif
                                    </div>
                                    <p>{{ $update->comment }}</p>
                                    @if($update->status_change)
                                        <div class="text-muted small">
                                            Status changed from 
                                            <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $update->old_status)) }}</span> 
                                            to 
                                            <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $update->new_status)) }}</span>
                                        </div>
                                    @endif
                                    @if($update->hours_spent)
                                        <div class="text-muted small">Hours spent: {{ $update->hours_spent }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-center">No updates found for this task.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Task Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($task->status != 'completed')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeTaskModal">
                                <i class="fas fa-check"></i> Mark as Completed
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignTaskModal">
                            <i class="fas fa-user-plus"></i> Assign Task
                        </button>
                        
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                            <i class="fas fa-exchange-alt"></i> Change Status
                        </button>
                        
                        <form action="{{ route('owner.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Related Files</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($task->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i> {{ $file->name }}
                                    <div class="text-muted small">Uploaded by {{ $file->uploadedBy->name }} on {{ date('M d, Y', strtotime($file->created_at)) }}</div>
                                </div>
                                <a href="{{ route('owner.files.download', $file) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">No files attached to this task.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="{{ route('owner.files.create', ['task_id' => $task->id]) }}" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-upload"></i> Upload File
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Update Modal -->
    <!-- Add Update Modal -->
<div class="modal fade" id="addUpdateModal" tabindex="-1" aria-labelledby="addUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUpdateModalLabel">Add Task Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('owner.tasks.updates.store', $task) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="hours_spent" class="form-label">Hours Spent</label>
                        <input type="number" step="0.5" min="0" class="form-control" id="hours_spent" name="hours_spent">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="status_change" name="status_change" value="1">
                            <label class="form-check-label" for="status_change">
                                Change Task Status
                            </label>
                        </div>
                    </div>
                    <div class="mb-3 status-select d-none">
                        <label for="new_status" class="form-label">New Status</label>
                        <select class="form-select" id="new_status" name="new_status">
                            <option value="backlog" {{ $task->status == 'backlog' ? 'selected' : '' }}>Backlog</option>
                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Complete Task Modal -->
    <!-- Complete Task Modal -->
<div class="modal fade" id="completeTaskModal" tabindex="-1" aria-labelledby="completeTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completeTaskModalLabel">Complete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('owner.tasks.update-status', $task) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <div class="modal-body">
                    <p>Are you sure you want to mark this task as completed?</p>
                    <div class="mb-3">
                        <label for="completion_comment" class="form-label">Completion Comment</label>
                        <textarea class="form-control" id="completion_comment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Assign Task Modal -->
    <!-- Assign Task Modal -->
<!-- Assign Task Modal -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTaskModalLabel">Assign Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('owner.tasks.assign', $task) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To <span class="text-danger">*</span></label>
                        <select class="form-select" id="assigned_to" name="assigned_to" required>
                            <option value="">Select User</option>
                            <optgroup label="Engineers">
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}" {{ $task->assigned_to == $engineer->id ? 'selected' : '' }}>
                                        {{ $engineer->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Contractors">
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor->id }}" {{ $task->assigned_to == $contractor->id ? 'selected' : '' }}>
                                        {{ $contractor->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_comment" class="form-label">Assignment Comment</label>
                        <textarea class="form-control" id="assignment_comment" name="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeStatusModalLabel">Change Task Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="backlog" {{ $task->status == 'backlog' ? 'selected' : '' }}>Backlog</option>
                                <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status_comment" class="form-label">Status Change Comment</label>
                            <textarea class="form-control" id="status_comment" name="comment" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Toggle status select visibility
        document.getElementById('status_change').addEventListener('change', function() {
            const statusSelect = document.querySelector('.status-select');
            if (this.checked) {
                statusSelect.classList.remove('d-none');
            } else {
                statusSelect.classList.add('d-none');
            }
        });
    </script>
@endsection

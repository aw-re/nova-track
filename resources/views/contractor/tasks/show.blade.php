@extends('layouts.app')

@section('title', 'View Task - CPMS')

@section('page_title', 'Task Details')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $task->title }}</h1>
        <div>
            <a href="{{ route('contractor.projects.show', $task->project) }}" class="btn btn-primary">
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
                        <div class="col-md-4 fw-bold">Task Title:</div>
                        <div class="col-md-8">{{ $task->title }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project:</div>
                        <div class="col-md-8">
                            <a href="{{ route('contractor.projects.show', $task->project) }}">{{ $task->project->name }}</a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($task->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($task->status == 'in_progress')
                                <span class="badge bg-primary">In Progress</span>
                            @elseif($task->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($task->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
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
                        <div class="col-md-4 fw-bold">Assigned By:</div>
                        <div class="col-md-8">{{ $task->assignedBy->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Assigned To:</div>
                        <div class="col-md-8">{{ $task->assignedTo->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Start Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($task->start_date)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Due Date:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($task->due_date)) }}</div>
                    </div>
                    @if($task->status == 'completed' && $task->completed_at)
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Completed On:</div>
                            <div class="col-md-8">{{ date('M d, Y h:i A', strtotime($task->completed_at)) }}</div>
                        </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Progress:</div>
                        <div class="col-md-8">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $task->progress }}%;" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $task->progress }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Task Description</h5>
                </div>
                <div class="card-body">
                    <div class="task-description">
                        {!! nl2br(e($task->description)) !!}
                    </div>
                </div>
            </div>

            @if($task->status != 'completed' && $task->assigned_to == auth()->id())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Update Task Status</h5>
                    </div>
                    <div class="card-body">
                            @csrf
                            @method('PATCH')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="progress" class="form-label">Progress (%)</label>
                                    <input type="number" class="form-control @error('progress') is-invalid @enderror" id="progress" name="progress" min="0" max="100" value="{{ $task->progress }}" required>
                                    @error('progress')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Status Update Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Task Files</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($task->files as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i> {{ $file->name }}
                                    <div class="text-muted small">{{ $file->size_formatted }}</div>
                                </div>
                                <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">No files attached to this task.</li>
                        @endforelse
                    </ul>
                    @if($task->status != 'completed' && $task->assigned_to == auth()->id())
                        <div class="mt-3">
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
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Task History</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title">Task Created</h3>
                                <p>{{ date('M d, Y', strtotime($task->created_at)) }}</p>
                                <p class="text-muted">By {{ $task->assignedBy->name }}</p>
                            </div>
                        </li>
                            <li class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                </div>
                            </li>
                        @if($task->status == 'completed' && $task->completed_at)
                            <li class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h3 class="timeline-title">Task Completed</h3>
                                    <p>{{ date('M d, Y', strtotime($task->completed_at)) }}</p>
                                    <p class="text-muted">By {{ $task->completedBy ? $task->completedBy->name : $task->assignedTo->name }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Comments</h5>
                </div>
                <div class="card-body">
                    <div class="comments-list mb-3">
                            <div class="comment mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center">No comments yet.</p>
                    </div>
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
@endsection

@section('scripts')
    <script>
        // Any task-specific JavaScript can go here
        
        // Auto-update progress to 100% when status is set to completed
        document.getElementById('status')?.addEventListener('change', function() {
            if (this.value === 'completed') {
                document.getElementById('progress').value = 100;
            }
        });
    </script>
@endsection
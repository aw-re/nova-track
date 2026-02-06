@extends('layouts.app')

@section('title', 'View Project - CPMS')

@section('page_title', 'Project Details')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $project->name }}</h1>
        <div>
            <a href="{{ route('owner.projects.edit', $project) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Project
            </a>
            <a href="{{ route('owner.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-primary ms-2">
                <i class="fas fa-plus"></i> Add Task
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @if($project->status == 'planning')
                                <span class="badge bg-info">Planning</span>
                            @elseif($project->status == 'in_progress')
                                <span class="badge bg-primary">In Progress</span>
                            @elseif($project->status == 'on_hold')
                                <span class="badge bg-warning">On Hold</span>
                            @elseif($project->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($project->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Start Date:</div>
                        <div class="col-md-8">{{ $project->start_date ? date('M d, Y', strtotime($project->start_date)) : 'Not set' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">End Date:</div>
                        <div class="col-md-8">{{ $project->end_date ? date('M d, Y', strtotime($project->end_date)) : 'Not set' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Location:</div>
                        <div class="col-md-8">{{ $project->location ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Budget:</div>
                        <div class="col-md-8">${{ number_format($project->budget, 2) ?? 'Not specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($project->created_at)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Last Updated:</div>
                        <div class="col-md-8">{{ date('M d, Y', strtotime($project->updated_at)) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Description:</div>
                        <div class="col-md-8">{{ $project->description }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks</h5>
                    <a href="{{ route('owner.tasks.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Task
                    </a>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">Pending</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab" aria-controls="in-progress" aria-selected="false">In Progress</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Completed</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="taskTabsContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->tasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>
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
                                                </td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $pendingTasks = $project->tasks->whereIn('status', ['backlog', 'todo']);
                                        @endphp
                                        @forelse($pendingTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>
                                                    @if($task->status == 'backlog')
                                                        <span class="badge bg-secondary">Backlog</span>
                                                    @elseif($task->status == 'todo')
                                                        <span class="badge bg-info">To Do</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No pending tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="in-progress" role="tabpanel" aria-labelledby="in-progress-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assigned To</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $inProgressTasks = $project->tasks->whereIn('status', ['in_progress', 'review']);
                                        @endphp
                                        @forelse($inProgressTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>
                                                    @if($task->status == 'in_progress')
                                                        <span class="badge bg-primary">In Progress</span>
                                                    @elseif($task->status == 'review')
                                                        <span class="badge bg-warning">Review</span>
                                                    @endif
                                                </td>
                                                <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                                <td>
                                                    <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No in-progress tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assigned To</th>
                                            <th>Completed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $completedTasks = $project->tasks->where('status', 'completed');
                                        @endphp
                                        @forelse($completedTasks as $task)
                                            <tr>
                                                <td>{{ $task->title }}</td>
                                                <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Unassigned' }}</td>
                                                <td>{{ $task->completed_at ? date('M d, Y', strtotime($task->completed_at)) : date('M d, Y', strtotime($task->updated_at)) }}</td>
                                                <td>
                                                    <a href="{{ route('owner.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No completed tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Project Members</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#inviteMembersModal">
                        <i class="fas fa-user-plus"></i> Invite
                    </button>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->projectMembers as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $member->user->name }}</strong>
                                    <div class="text-muted small">{{ $member->user->email }}</div>
                                    <div class="text-muted small">
                                        @foreach($member->user->roles as $role)
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <form action="{{ route('owner.projects.members.remove', ['project' => $project->id, 'user' => $member->user->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this member?')">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item">No members found for this project.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Progress</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalTasks = $project->tasks->count();
                        $completedTasks = $project->tasks->where('status', 'completed')->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp
                    <div class="text-center mb-3">
                        <h1 class="display-4">{{ $progress }}%</h1>
                        <p class="text-muted">{{ $completedTasks }} of {{ $totalTasks }} tasks completed</p>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Files</h5>
                    <a href="{{ route('owner.files.index', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-file"></i> All Files
                    </a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->files->take(5) as $file)
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
                            <li class="list-group-item">No files found for this project.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Invite Members Modal -->
    <div class="modal fade" id="inviteMembersModal" tabindex="-1" aria-labelledby="inviteMembersModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteMembersModalLabel">Invite Members</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('owner.projects.invite', $project) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="engineers" class="form-label">Select Engineers</label>
                            <select class="form-select" id="engineers" name="engineers[]" multiple>
                                @foreach($engineers as $engineer)
                                    <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contractors" class="form-label">Select Contractors</label>
                            <select class="form-select" id="contractors" name="contractors[]" multiple>
                                @foreach($contractors as $contractor)
                                    <option value="{{ $contractor->id }}">{{ $contractor->name }} ({{ $contractor->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any project-specific JavaScript can go here
    </script>
@endsection
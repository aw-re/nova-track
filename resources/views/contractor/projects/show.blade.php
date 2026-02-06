@extends('layouts.app')

@section('title', 'View Project - CPMS')

@section('page_title', 'Project Details')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $project->name }}</h1>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project Name:</div>
                        <div class="col-md-8">{{ $project->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Project Owner:</div>
                        <div class="col-md-8">{{ $project->owner->name }}</div>
                    </div>
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
                        <div class="col-md-8">{{ date('M d, Y', strtotime($project->start_date)) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">End Date:</div>
                        <div class="col-md-8">{{ $project->end_date ? date('M d, Y', strtotime($project->end_date)) : 'Not set' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Budget:</div>
                        <div class="col-md-8">${{ number_format($project->budget, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Progress:</div>
                        <div class="col-md-8">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $project->progress }}%;" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $project->progress }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Description</h5>
                </div>
                <div class="card-body">
                    <div class="project-description">
                        {!! nl2br(e($project->description)) !!}
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tasks</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($project->tasks->where('assigned_to', auth()->id()) as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>
                                            @if($task->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($task->status == 'in_progress')
                                                <span class="badge bg-primary">In Progress</span>
                                            @elseif($task->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($task->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->priority == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning">Medium</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-danger">High</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($task->due_date)) }}</td>
                                        <td>
                                            <a href="{{ route('contractor.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No tasks assigned to you for this project.</td>
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
                    <h5 class="card-title">Project Team</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $project->owner->name }}</strong>
                                    <div class="text-muted">Project Owner</div>
                                </div>
                            </div>
                        </li>
                        @foreach($project->members as $member)
                            @if($member->role == 'engineer')
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $member->user ? $member->user->name : 'Unknown Engineer' }}</strong>
                                            <div class="text-muted">Engineer</div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                        @foreach($project->members as $member)
                            @if($member->role == 'contractor' && $member->user_id != auth()->id())
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $member->user ? $member->user->name : 'Unknown Contractor' }}</strong>
                                            <div class="text-muted">Contractor</div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Files</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($project->files as $file)
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
                            <li class="list-group-item">No files found for this project.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title">Project Created</h3>
                                <p>{{ date('M d, Y', strtotime($project->created_at)) }}</p>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title">Project Started</h3>
                                <p>{{ date('M d, Y', strtotime($project->start_date)) }}</p>
                            </div>
                        </li>
                        @if($project->status == 'completed' && $project->end_date)
                            <li class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h3 class="timeline-title">Project Completed</h3>
                                    <p>{{ date('M d, Y', strtotime($project->end_date)) }}</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any project-specific JavaScript can go here
    </script>
@endsection
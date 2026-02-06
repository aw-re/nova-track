@extends('layouts.app')

@section('title', 'Project Details - CPMS')

@section('page_title', $project->name)



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3">{{ $project->name }}</h1>
            <p class="text-muted">{{ $project->location ?? 'No location specified' }}</p>
        </div>
        <div>
            @if(auth()->user()->isAdmin() || auth()->id() == $project->owner_id)
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit"></i> Edit Project
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Delete Project
                </button>
                
                <!-- Delete Modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete the project "{{ $project->name }}"? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="projectTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab" aria-controls="tasks" aria-selected="false">Tasks</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab" aria-controls="resources" aria-selected="false">Resources</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab" aria-controls="files" aria-selected="false">Files</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">Reports</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="projectTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Project Details</h5>
                                    <table class="table">
                                        <tr>
                                            <th>Owner:</th>
                                            <td>{{ $project->owner->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
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
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Start Date:</th>
                                            <td>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <th>End Date:</th>
                                            <td>{{ $project->end_date ? $project->end_date->format('M d, Y') : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Budget:</th>
                                            <td>${{ number_format($project->budget, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td>{{ $project->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Project Progress</h5>
                                    @php
                                        $totalTasks = $project->tasks->count();
                                        $completedTasks = $project->tasks->where('status', 'completed')->count();
                                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                    @endphp
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
                                    </div>
                                    
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="h4">{{ $totalTasks }}</div>
                                            <div class="small text-muted">Total Tasks</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4">{{ $completedTasks }}</div>
                                            <div class="small text-muted">Completed</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4">{{ $totalTasks - $completedTasks }}</div>
                                            <div class="small text-muted">Remaining</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h5>Description</h5>
                            <p>{{ $project->description ?? 'No description provided.' }}</p>
                        </div>
                        
                        <!-- Tasks Tab -->
                        <div class="tab-pane fade" id="tasks" role="tabpanel" aria-labelledby="tasks-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Project Tasks</h5>
                                @if(auth()->user()->isAdmin() || auth()->user()->isProjectOwner() || auth()->user()->isEngineer())
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus-circle"></i> Add Task
                                    </a>
                                @endif
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assigned To</th>
                                            <th>Priority</th>
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
                                                    @if($task->priority == 'low')
                                                        <span class="badge bg-success">Low</span>
                                                    @elseif($task->priority == 'medium')
                                                        <span class="badge bg-info">Medium</span>
                                                    @elseif($task->priority == 'high')
                                                        <span class="badge bg-warning">High</span>
                                                    @elseif($task->priority == 'urgent')
                                                        <span class="badge bg-danger">Urgent</span>
                                                    @endif
                                                </td>
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
                                                <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'Not set' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->isAdmin() || auth()->user()->isProjectOwner() || auth()->user()->isEngineer() || auth()->id() == $task->assigned_to)
                                                            <a href="#" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No tasks found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Resources Tab -->
                        <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Resource Requests</h5>
                                @if(auth()->user()->isAdmin() || auth()->user()->isProjectOwner() || auth()->user()->isContractor())
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus-circle"></i> New Request
                                    </a>
                                @endif
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Resource</th>
                                            <th>Quantity</th>
                                            <th>Requested By</th>
                                            <th>Required Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->resourceRequests as $request)
                                            <tr>
                                                <td>{{ $request->resource->name }}</td>
                                                <td>{{ $request->quantity }} {{ $request->resource->unit }}</td>
                                                <td>{{ $request->requestedBy->name }}</td>
                                                <td>{{ $request->required_date->format('M d, Y') }}</td>
                                                <td>
                                                    @if($request->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($request->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($request->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @elseif($request->status == 'delivered')
                                                        <span class="badge bg-info">Delivered</span>
                                                    @elseif($request->status == 'cancelled')
                                                        <span class="badge bg-secondary">Cancelled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->isAdmin() || auth()->user()->isProjectOwner())
                                                            @if($request->status == 'pending')
                                                                <a href="#" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                                <a href="#" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No resource requests found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Files Tab -->
                        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Project Files</h5>
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-upload"></i> Upload File
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Size</th>
                                            <th>Uploaded By</th>
                                            <th>Upload Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->files as $file)
                                            <tr>
                                                <td>{{ $file->file_name }}</td>
                                                <td>{{ $file->file_type }}</td>
                                                <td>{{ number_format($file->file_size / 1024, 2) }} KB</td>
                                                <td>{{ $file->uploadedBy->name }}</td>
                                                <td>{{ $file->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @if(auth()->user()->isAdmin() || auth()->id() == $file->uploaded_by)
                                                            <a href="#" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No files found for this project.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Reports Tab -->
                        <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Project Reports</h5>
                                @if(auth()->user()->isAdmin() || auth()->user()->isProjectOwner() || auth()->user()->isEngineer() || auth()->user()->isContractor())
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus-circle"></i> Create Report
                                    </a>
                                @endif
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Created By</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($project->reports as $report)
                                            <tr>
                                                <td>{{ $report->title }}</td>
                                                <td>{{ ucfirst($report->type) }}</td>
                                                <td>{{ $report->createdBy->name }}</td>
                                                <td>
                                                    @if($report->status == 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($report->status == 'submitted')
                                                        <span class="badge bg-primary">Submitted</span>
                                                    @elseif($report->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($report->status == 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="#" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->isAdmin() || auth()->id() == $report->created_by)
                                                            <a href="#" class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                        @if((auth()->user()->isAdmin() || auth()->user()->isProjectOwner()) && $report->status == 'submitted')
                                                            <a href="#" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No reports found for this project.</td>
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
            <!-- Team Members Card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Team Members</h5>
                    @if(auth()->user()->isAdmin() || auth()->id() == $project->owner_id)
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Invite
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $project->owner->name }}</strong>
                                <div class="text-muted small">Project Owner</div>
                            </div>
                            <span class="badge bg-primary rounded-pill">Owner</span>
                        </li>
                        @foreach($project->projectMembers as $member)
                            @if($member->user_id != $project->owner_id)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $member->user->name }}</strong>
                                        <div class="text-muted small">{{ $member->user->email }}</div>
                                    </div>
                                    <span class="badge bg-info rounded-pill">{{ $member->role->name }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <!-- Recent Activities Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentActivities as $activity)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $activity->user ? $activity->user->name : 'System' }}</strong> {{ $activity->action }}
                                        <div class="text-muted small">{{ $activity->description }}</div>
                                    </div>
                                    <div class="text-muted small">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No recent activities found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            
            <!-- Project Timeline Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Project Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Project Created</h6>
                                <p class="timeline-date">{{ $project->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        @if($project->start_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Project Started</h6>
                                    <p class="timeline-date">{{ $project->start_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @foreach($project->tasks->where('completed_at', '!=', null)->sortBy('completed_at')->take(3) as $task)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Task Completed: {{ $task->title }}</h6>
                                    <p class="timeline-date">{{ $task->completed_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($project->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Project Completed</h6>
                                    <p class="timeline-date">{{ $project->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
    }
    .timeline-content {
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }
    .timeline-title {
        margin-bottom: 5px;
    }
    .timeline-date {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0;
    }
</style>
@endsection
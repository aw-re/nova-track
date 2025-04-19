@extends('layouts.app')

@section('title', 'Tasks - CPMS')

@section('page_title', 'Tasks')

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
        <h1 class="h3">Tasks</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="taskTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Tasks</button>
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
        </div>
        <div class="card-body">
            <div class="tab-content" id="taskTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedTasks ?? [] as $task)
                                    <tr>
                                        <td>{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
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
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->status ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->priority == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning">Medium</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-danger">High</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->priority ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                        <td>
                                            <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No tasks found.</td>
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
                                    <th>Project</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingTasks = $tasks->whereIn('status', ['backlog', 'todo']);
                                @endphp
                                @forelse($pendingTasks ?? [] as $task)
                                    <tr>
                                        <td>{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            @if($task->priority == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning">Medium</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-danger">High</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->priority ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                        <td>
                                            <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No pending tasks found.</td>
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
                                    <th>Project</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $inProgressTasks = $tasks->where('status', 'in_progress');
                                @endphp
                                @forelse($inProgressTasks ?? [] as $task)
                                    <tr>
                                        <td>{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            @if($task->priority == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning">Medium</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-danger">High</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->priority ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? date('M d, Y', strtotime($task->due_date)) : 'No due date' }}</td>
                                        <td>
                                            <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No in-progress tasks found.</td>
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
                                    <th>Project</th>
                                    <th>Priority</th>
                                    <th>Completed Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $completedTasks = $tasks->where('status', 'completed');
                                @endphp
                                @forelse($completedTasks ?? [] as $task)
                                    <tr>
                                        <td>{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            @if($task->priority == 'low')
                                                <span class="badge bg-success">Low</span>
                                            @elseif($task->priority == 'medium')
                                                <span class="badge bg-warning">Medium</span>
                                            @elseif($task->priority == 'high')
                                                <span class="badge bg-danger">High</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($task->priority ?? 'Unknown') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->completed_at ? date('M d, Y', strtotime($task->completed_at)) : ($task->updated_at ? date('M d, Y', strtotime($task->updated_at)) : 'N/A') }}</td>
                                        <td>
                                            <a href="{{ route('engineer.tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No completed tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if(isset($paginatedTasks) && $paginatedTasks->hasPages())
                <div class="mt-4">
                    {{ $paginatedTasks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any task-specific JavaScript can go here
    </script>
@endsection

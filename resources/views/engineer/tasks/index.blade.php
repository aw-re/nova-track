@use('App\Enums\TaskStatusEnum')
@use('App\Enums\TaskPriorityEnum')
@extends('layouts.app')

@section('title', __('app.tasks') . ' - ' . __('app.app_name'))

@section('page_title', __('app.tasks'))



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ __('app.tasks') }}</h1>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="taskTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">{{ __('app.view_all') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">{{ __('enums.task_status.todo') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="in-progress-tab" data-bs-toggle="tab" data-bs-target="#in-progress" type="button" role="tab" aria-controls="in-progress" aria-selected="false">{{ __('enums.task_status.in_progress') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">{{ __('enums.task_status.completed') }}</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="taskTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.projects') }}</th>
                                    <th>{{ __('app.status') }}</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedTasks ?? [] as $task)
                                    <tr>
                                        <td class="fw-bold">{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            <x-badge :status="$task->status" />
                                        </td>
                                        <td>
                                            <x-badge :status="$task->priority" />
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
                                        <td colspan="6" class="text-center py-4 text-muted">No tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.projects') }}</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendingTasks = $tasks->filter(fn($t) => in_array($t->status, [TaskStatusEnum::BACKLOG, TaskStatusEnum::TODO]));
                                @endphp
                                @forelse($pendingTasks ?? [] as $task)
                                    <tr>
                                        <td class="fw-bold">{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            <x-badge :status="$task->priority" />
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
                                        <td colspan="5" class="text-center py-4 text-muted">No pending tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="in-progress" role="tabpanel" aria-labelledby="in-progress-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.projects') }}</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $inProgressTasks = $tasks->filter(fn($t) => $t->status === TaskStatusEnum::IN_PROGRESS);
                                @endphp
                                @forelse($inProgressTasks ?? [] as $task)
                                    <tr>
                                        <td class="fw-bold">{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            <x-badge :status="$task->priority" />
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
                                        <td colspan="5" class="text-center py-4 text-muted">No in-progress tasks found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('app.name') }}</th>
                                    <th>{{ __('app.projects') }}</th>
                                    <th>Priority</th>
                                    <th>Completed Date</th>
                                    <th>{{ __('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $completedTasks = $tasks->filter(fn($t) => $t->status === TaskStatusEnum::COMPLETED);
                                @endphp
                                @forelse($completedTasks ?? [] as $task)
                                    <tr>
                                        <td class="fw-bold">{{ $task->title ?? 'N/A' }}</td>
                                        <td>{{ isset($task->project) && is_object($task->project) ? $task->project->name : 'N/A' }}</td>
                                        <td>
                                            <x-badge :status="$task->priority" />
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
                                        <td colspan="5" class="text-center py-4 text-muted">No completed tasks found.</td>
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
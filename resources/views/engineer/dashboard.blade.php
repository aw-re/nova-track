@extends('layouts.app')

@section('title', 'Engineer Dashboard - CPMS')

@section('page_title', 'Engineer Dashboard')



@section('content')
    @if(isset($pendingInvitations) && $pendingInvitations->count() > 0)
        <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
            <i class="fas fa-envelope me-2"></i> You have {{ $pendingInvitations->count() }} pending project invitation(s).
            <a href="{{ route('engineer.invitations.index') }}" class="fw-bold text-decoration-none ms-2">View invitations <i
                    class="fas fa-arrow-right"></i></a>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-stats-card type="primary" value="{{ $assignedProjectCount }}" label="Assigned Projects"
                icon="fas fa-project-diagram" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="success" value="{{ $assignedTaskCount }}" label="Assigned Tasks" icon="fas fa-tasks" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="warning" value="{{ $createdTaskCount }}" label="Created Tasks" icon="fas fa-plus-circle" />
        </div>
        <div class="col-md-3">
            <x-stats-card type="danger" value="{{ $submittedReportCount }}" label="Submitted Reports"
                icon="fas fa-file-alt" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <x-app-card title="My Tasks" icon="fas fa-tasks">
                <x-slot name="actions">
                    <a href="{{ route('engineer.tasks.index') }}" class="btn btn-sm btn-light text-primary fw-bold">View
                        All</a>
                </x-slot>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Title</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedTasks as $task)
                                <tr>
                                    <td class="fw-bold">{{ $task->title }}</td>
                                    <td>
                                        <x-badge :status="$task->status" />
                                    </td>
                                    <td class="text-muted small">
                                        {{ $task->due_date ? date('M d', strtotime($task->due_date)) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No tasks found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>
        </div>
        <div class="col-lg-6">
            <x-app-card title="Assigned Projects" icon="fas fa-project-diagram">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Name</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedProjects as $project)
                                <tr>
                                    <td class="fw-bold">{{ $project->name }}</td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('engineer.projects.show', $project) }}"
                                            class="btn btn-sm btn-white border shadow-sm"><i class="fas fa-arrow-right"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No projects found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-app-card>
        </div>
    </div>
@endsection
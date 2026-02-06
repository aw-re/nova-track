@extends('layouts.app')

@section('title', 'My Projects - CPMS')

@section('page_title', 'My Projects')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">My Projects</h1>
        <a href="{{ route('owner.projects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Project
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Members</th>
                            <th>Tasks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ Str::limit($project->description, 50) }}</td>
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
                                <td>{{ $project->start_date ? date('M d, Y', strtotime($project->start_date)) : 'Not set' }}</td>
                                <td>{{ $project->end_date ? date('M d, Y', strtotime($project->end_date)) : 'Not set' }}</td>
                                <td>{{ $project->project_members_count ?? 0 }}</td>
                                <td>{{ $project->tasks_count ?? 0 }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('owner.projects.show', $project) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('owner.projects.edit', $project) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $project->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $project->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $project->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $project->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the project "{{ $project->name }}"? This action cannot be undone.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('owner.projects.destroy', $project) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($projects->hasPages())
                <div class="mt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any project-specific JavaScript can go here
    </script>
@endsection
@extends('layouts.app')

@section('title', 'Files - CPMS')

@section('page_title', 'Files')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Files</h1>
        <a href="{{ route('owner.files.create') }}" class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload New File
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="fileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Files</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="project-tab" data-bs-toggle="tab" data-bs-target="#project" type="button" role="tab" aria-controls="project" aria-selected="false">Project Files</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="task-tab" data-bs-toggle="tab" data-bs-target="#task" type="button" role="tab" aria-controls="task" aria-selected="false">Task Files</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#report" type="button" role="tab" aria-controls="report" aria-selected="false">Report Files</button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="fileTabsContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Related To</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            @if($file->project_id)
                                                <a href="{{ route('owner.projects.show', $file->project_id) }}">{{ $file->project->name }}</a>
                                            @elseif($file->task_id)
                                                <a href="{{ route('owner.tasks.show', $file->task_id) }}">{{ $file->task->title }}</a>
                                            @elseif($file->report_id)
                                                <a href="{{ route('owner.reports.show', $file->report_id) }}">{{ $file->report->title }}</a>
                                            @else
                                                General
                                            @endif
                                        </td>
                                        <td>{{ $file->uploadedBy->name }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.files.download', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('owner.files.show', $file) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $file->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $file->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $file->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $file->id }}">Confirm Delete</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete the file "{{ $file->name }}"? This action cannot be undone.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('owner.files.destroy', $file) }}" method="POST">
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
                                        <td colspan="7" class="text-center">No files found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="project" role="tabpanel" aria-labelledby="project-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Project</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $projectFiles = $files->filter(function($file) {
                                        return $file->project_id !== null;
                                    });
                                @endphp
                                @forelse($projectFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            <a href="{{ route('owner.projects.show', $file->project_id) }}">{{ $file->project->name }}</a>
                                        </td>
                                        <td>{{ $file->uploadedBy->name }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.files.download', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('owner.files.show', $file) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No project files found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="task" role="tabpanel" aria-labelledby="task-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Task</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $taskFiles = $files->filter(function($file) {
                                        return $file->task_id !== null;
                                    });
                                @endphp
                                @forelse($taskFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            <a href="{{ route('owner.tasks.show', $file->task_id) }}">{{ $file->task->title }}</a>
                                        </td>
                                        <td>{{ $file->uploadedBy->name }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.files.download', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('owner.files.show', $file) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No task files found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Report</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $reportFiles = $files->filter(function($file) {
                                        return $file->report_id !== null;
                                    });
                                @endphp
                                @forelse($reportFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            <a href="{{ route('owner.reports.show', $file->report_id) }}">{{ $file->report->title }}</a>
                                        </td>
                                        <td>{{ $file->uploadedBy->name }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('owner.files.download', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('owner.files.show', $file) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No report files found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($files->hasPages())
                <div class="mt-4">
                    {{ $files->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any file-specific JavaScript can go here
    </script>
@endsection
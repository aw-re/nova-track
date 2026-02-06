@extends('layouts.app')

@section('title', 'Files - CPMS')

@section('page_title', 'Files')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Files</h1>
        <a href="{{ route('contractor.files.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Upload New File
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="fileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">All Files</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="my-tab" data-bs-toggle="tab" data-bs-target="#my" type="button" role="tab" aria-controls="my" aria-selected="false">My Uploads</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="project-tab" data-bs-toggle="tab" data-bs-target="#project" type="button" role="tab" aria-controls="project" aria-selected="false">Project Files</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="task-tab" data-bs-toggle="tab" data-bs-target="#task" type="button" role="tab" aria-controls="task" aria-selected="false">Task Files</button>
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
                                            @if($file->project_id && isset($file->project) && is_object($file->project))
                                                <a href="{{ route('contractor.projects.show', $file->project_id) }}">Project: {{ $file->project->name }}</a>
                                            @elseif($file->task_id && isset($file->task) && is_object($file->task))
                                                <a href="{{ route('contractor.tasks.show', $file->task_id) }}">Task: {{ $file->task->title }}</a>
                                            @else
                                                General File
                                            @endif
                                        </td>
                                        <td>{{ isset($file->user) && is_object($file->user) ? $file->user->name : 'Unknown' }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contractor.files.show', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
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
                <div class="tab-pane fade" id="my" role="tabpanel" aria-labelledby="my-tab">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Related To</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $myFiles = $files->where('user_id', auth()->id());
                                @endphp
                                @forelse($myFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            @if($file->project_id && isset($file->project) && is_object($file->project))
                                                <a href="{{ route('contractor.projects.show', $file->project_id) }}">Project: {{ $file->project->name }}</a>
                                            @elseif($file->task_id && isset($file->task) && is_object($file->task))
                                                <a href="{{ route('contractor.tasks.show', $file->task_id) }}">Task: {{ $file->task->title }}</a>
                                            @else
                                                General File
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contractor.files.show', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($file->user_id == auth()->id())
                                                    <form action="{{ route('contractor.files.destroy', $file) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">You haven't uploaded any files yet.</td>
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
                                    $projectFiles = $files->whereNotNull('project_id');
                                @endphp
                                @forelse($projectFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            @if(isset($file->project) && is_object($file->project))
                                                <a href="{{ route('contractor.projects.show', $file->project_id) }}">{{ $file->project->name }}</a>
                                            @else
                                                Project ID: {{ $file->project_id }}
                                            @endif
                                        </td>
                                        <td>{{ isset($file->user) && is_object($file->user) ? $file->user->name : 'Unknown' }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contractor.files.show', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
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
                                    $taskFiles = $files->whereNotNull('task_id');
                                @endphp
                                @forelse($taskFiles as $file)
                                    <tr>
                                        <td>{{ $file->name }}</td>
                                        <td>{{ strtoupper($file->extension) }}</td>
                                        <td>{{ $file->size_formatted }}</td>
                                        <td>
                                            @if(isset($file->task) && is_object($file->task))
                                                <a href="{{ route('contractor.tasks.show', $file->task_id) }}">{{ $file->task->title }}</a>
                                            @else
                                                Task ID: {{ $file->task_id }}
                                            @endif
                                        </td>
                                        <td>{{ isset($file->user) && is_object($file->user) ? $file->user->name : 'Unknown' }}</td>
                                        <td>{{ date('M d, Y', strtotime($file->created_at)) }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('contractor.files.show', $file) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('contractor.files.download', $file) }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-download"></i>
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
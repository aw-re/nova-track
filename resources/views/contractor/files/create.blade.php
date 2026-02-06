@extends('layouts.app')

@section('title', 'Upload File - CPMS')

@section('page_title', 'Upload New File')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Upload New File</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">File Upload Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('contractor.files.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="file" class="form-label">Select File <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maximum file size: 10MB</div>
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">File Name (Optional)</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Leave blank to use original filename">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"></textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="related_to" class="form-label">Related To (Optional)</label>
                    <select class="form-select @error('related_to') is-invalid @enderror" id="related_to" name="related_to">
                        <option value="">None (General File)</option>
                        <option value="project">Project</option>
                        <option value="task">Task</option>
                    </select>
                    @error('related_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="project_selection" class="mb-3 d-none">
                    <label for="project_id" class="form-label">Select Project <span class="text-danger">*</span></label>
                    <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="task_selection" class="mb-3 d-none">
                    <label for="task_id" class="form-label">Select Task <span class="text-danger">*</span></label>
                    <select class="form-select @error('task_id') is-invalid @enderror" id="task_id" name="task_id">
                        <option value="">Select Task</option>
                        @foreach($tasks as $task)
                            <option value="{{ $task->id }}">{{ $task->title }}</option>
                        @endforeach
                    </select>
                    @error('task_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('contractor.files.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Upload File</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Show/hide related item selection based on related_to dropdown
        document.getElementById('related_to').addEventListener('change', function() {
            // Hide all selection divs first
            document.getElementById('project_selection').classList.add('d-none');
            document.getElementById('task_selection').classList.add('d-none');
            
            // Show the appropriate selection div based on the selected value
            const relatedTo = this.value;
            if (relatedTo === 'project') {
                document.getElementById('project_selection').classList.remove('d-none');
            } else if (relatedTo === 'task') {
                document.getElementById('task_selection').classList.remove('d-none');
            }
        });
        
        // Filter tasks based on selected project
        document.getElementById('project_id').addEventListener('change', function() {
            const projectId = this.value;
            if (projectId) {
                // This would typically be an AJAX call to get tasks for the selected project
                // For now, we'll just use the existing tasks dropdown
                console.log('Project selected: ' + projectId);
            }
        });
    </script>
@endsection
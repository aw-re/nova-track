@extends('layouts.app')

@section('title', 'Create Report - CPMS')

@section('page_title', 'Create New Report')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Report</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Report Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('engineer.reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                    <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="type" class="form-label">Report Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="progress" {{ old('type') == 'progress' ? 'selected' : '' }}>Progress Report</option>
                        <option value="incident" {{ old('type') == 'incident' ? 'selected' : '' }}>Incident Report</option>
                        <option value="inspection" {{ old('type') == 'inspection' ? 'selected' : '' }}>Inspection Report</option>
                        <option value="quality" {{ old('type') == 'quality' ? 'selected' : '' }}>Quality Control Report</option>
                        <option value="safety" {{ old('type') == 'safety' ? 'selected' : '' }}>Safety Report</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="other_type_div" class="mb-3 {{ old('type') == 'other' ? '' : 'd-none' }}">
                    <label for="other_type" class="form-label">Specify Other Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('other_type') is-invalid @enderror" id="other_type" name="other_type" value="{{ old('other_type') }}">
                    @error('other_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="report_date" class="form-label">Report Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date" value="{{ old('report_date', date('Y-m-d')) }}" required>
                    @error('report_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Report Content <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="attachments" class="form-label">Attachments</label>
                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                    @error('attachments')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">You can select multiple files. Maximum file size: 10MB each.</div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('engineer.reports.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Show/hide other type field based on type selection
        document.getElementById('type').addEventListener('change', function() {
            const otherTypeDiv = document.getElementById('other_type_div');
            if (this.value === 'other') {
                otherTypeDiv.classList.remove('d-none');
                document.getElementById('other_type').setAttribute('required', 'required');
            } else {
                otherTypeDiv.classList.add('d-none');
                document.getElementById('other_type').removeAttribute('required');
            }
        });
    </script>
@endsection
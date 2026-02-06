@extends('layouts.app')

@section('title', 'Create Resource Request - CPMS')

@section('page_title', 'Create New Resource Request')



@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create New Resource Request</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Resource Request Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('contractor.resource-requests.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label for="resource_name" class="form-label">Resource Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('resource_name') is-invalid @enderror" id="resource_name" name="resource_name" value="{{ old('resource_name') }}" required>
                    @error('resource_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="resource_type" class="form-label">Resource Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('resource_type') is-invalid @enderror" id="resource_type" name="resource_type" required>
                        <option value="">Select Type</option>
                        <option value="material" {{ old('resource_type') == 'material' ? 'selected' : '' }}>Material</option>
                        <option value="equipment" {{ old('resource_type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="labor" {{ old('resource_type') == 'labor' ? 'selected' : '' }}>Labor</option>
                    </select>
                    @error('resource_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" required>
                    <div class="form-text">Examples: pieces, kg, tons, hours, days, etc.</div>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="required_by" class="form-label">Required By Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('required_by') is-invalid @enderror" id="required_by" name="required_by" value="{{ old('required_by') }}" required>
                    @error('required_by')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="supporting_document" class="form-label">Supporting Document</label>
                    <input type="file" class="form-control @error('supporting_document') is-invalid @enderror" id="supporting_document" name="supporting_document">
                    <div class="form-text">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Maximum file size: 5MB.</div>
                    @error('supporting_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('contractor.resource-requests.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Any resource request-specific JavaScript can go here
    </script>
@endsection
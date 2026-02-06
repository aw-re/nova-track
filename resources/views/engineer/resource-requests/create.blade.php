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
            <form action="{{ route('engineer.resource-requests.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label for="title" class="form-label">Request Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="type" class="form-label">Resource Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="material" {{ old('type') == 'material' ? 'selected' : '' }}>Material</option>
                        <option value="equipment" {{ old('type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="labor" {{ old('type') == 'labor' ? 'selected' : '' }}>Labor</option>
                        <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
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
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
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
                    <label for="estimated_cost" class="form-label">Estimated Cost</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}">
                    </div>
                    @error('estimated_cost')
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
                    <label for="specifications" class="form-label">Specifications</label>
                    <textarea class="form-control @error('specifications') is-invalid @enderror" id="specifications" name="specifications" rows="3">{{ old('specifications') }}</textarea>
                    <div class="form-text">Detailed specifications, brand preferences, models, etc.</div>
                    @error('specifications')
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
                    <a href="{{ route('engineer.resource-requests.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
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
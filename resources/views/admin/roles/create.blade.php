@extends('layouts.app')

@section('title', 'Create Role - CPMS')

@section('page_title', 'Create New Role')



@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2"></i> Create New Role
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    <div class="form-text">Enter a unique role name (e.g., manager, supervisor). Use lowercase letters and underscores only.</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    <div class="form-text">Provide a clear description of this role's responsibilities and permissions.</div>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_users" name="permissions[]" value="manage_users" {{ in_array('manage_users', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_users">Manage Users</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_projects" name="permissions[]" value="manage_projects" {{ in_array('manage_projects', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_projects">Manage Projects</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_tasks" name="permissions[]" value="manage_tasks" {{ in_array('manage_tasks', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_tasks">Manage Tasks</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_resources" name="permissions[]" value="manage_resources" {{ in_array('manage_resources', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_resources">Manage Resources</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_reports" name="permissions[]" value="manage_reports" {{ in_array('manage_reports', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_reports">Manage Reports</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="view_analytics" name="permissions[]" value="view_analytics" {{ in_array('view_analytics', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="view_analytics">View Analytics</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="manage_settings" name="permissions[]" value="manage_settings" {{ in_array('manage_settings', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="manage_settings">Manage Settings</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="approve_requests" name="permissions[]" value="approve_requests" {{ in_array('approve_requests', old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="approve_requests">Approve Requests</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-text">Select the permissions that will be granted to users with this role.</div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'System Settings - CPMS')

@section('page_title', 'System Settings')



@section('content')
    <div class="card">
        <div class="card-header">
            <i class="fas fa-cog me-2"></i> System Settings
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>General Settings</h5>
                        <hr>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? 'Construction Project Management System' }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <input type="text" class="form-control" id="site_description" name="site_description" value="{{ $settings['site_description'] ?? 'Manage construction projects efficiently' }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ $settings['contact_email'] ?? 'admin@example.com' }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="contact_phone" class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+1 (555) 123-4567' }}">
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Email Settings</h5>
                        <hr>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_email_notifications" name="enable_email_notifications" {{ ($settings['enable_email_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_email_notifications">Enable Email Notifications</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_welcome_email" name="enable_welcome_email" {{ ($settings['enable_welcome_email'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_welcome_email">Send Welcome Email to New Users</label>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="email_footer" class="form-label">Email Footer Text</label>
                        <textarea class="form-control" id="email_footer" name="email_footer" rows="3">{{ $settings['email_footer'] ?? 'Thank you for using our Construction Project Management System.' }}</textarea>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>Security Settings</h5>
                        <hr>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="{{ $settings['session_timeout'] ?? 120 }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="{{ $settings['max_login_attempts'] ?? 5 }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enable_two_factor" name="enable_two_factor" {{ ($settings['enable_two_factor'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="enable_two_factor">Enable Two-Factor Authentication</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="force_password_change" name="force_password_change" {{ ($settings['force_password_change'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="force_password_change">Force Password Change Every 90 Days</label>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5>File Upload Settings</h5>
                        <hr>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="max_file_size" class="form-label">Max File Size (MB)</label>
                        <input type="number" class="form-control" id="max_file_size" name="max_file_size" value="{{ $settings['max_file_size'] ?? 10 }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="allowed_file_types" class="form-label">Allowed File Types</label>
                        <input type="text" class="form-control" id="allowed_file_types" name="allowed_file_types" value="{{ $settings['allowed_file_types'] ?? 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip' }}">
                        <div class="form-text">Comma-separated list of file extensions</div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Settings
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'User Settings - CPMS')

@section('page_title', 'User Settings')

@section('sidebar')
    @if(auth()->user()->hasRole('admin'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.roles.index') }}">
                <i class="fas fa-user-tag"></i> Roles
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.projects.index') }}">
                <i class="fas fa-project-diagram"></i> Projects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
                <i class="fas fa-clipboard-list"></i> Activity Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.resources.index') }}">
                <i class="fas fa-boxes"></i> Resources
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    @elseif(auth()->user()->hasRole('project_owner'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.projects.index') }}">
                <i class="fas fa-project-diagram"></i> My Projects
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.tasks.index') }}">
                <i class="fas fa-tasks"></i> Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.reports.index') }}">
                <i class="fas fa-file-alt"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.resource-requests.index') }}">
                <i class="fas fa-tools"></i> Resource Requests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('owner.files.index') }}">
                <i class="fas fa-file"></i> Files
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    @elseif(auth()->user()->hasRole('engineer'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.tasks.index') }}">
                <i class="fas fa-tasks"></i> Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.reports.index') }}">
                <i class="fas fa-file-alt"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.resource-requests.index') }}">
                <i class="fas fa-tools"></i> Resource Requests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.files.index') }}">
                <i class="fas fa-file"></i> Files
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('engineer.invitations.index') }}">
                <i class="fas fa-envelope"></i> Invitations
                @if(isset($invitationCount) && $invitationCount > 0)
                    <span class="badge bg-danger">{{ $invitationCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    @elseif(auth()->user()->hasRole('contractor'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contractor.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contractor.tasks.index') }}">
                <i class="fas fa-tasks"></i> Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contractor.resource-requests.index') }}">
                <i class="fas fa-tools"></i> Resource Requests
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contractor.files.index') }}">
                <i class="fas fa-file"></i> Files
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('contractor.invitations.index') }}">
                <i class="fas fa-envelope"></i> Invitations
                @if(isset($invitationCount) && $invitationCount > 0)
                    <span class="badge bg-danger">{{ $invitationCount }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('settings.index') }}">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
    @endif
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="settings-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                            <i class="fas fa-user me-2"></i>Profile
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="settings-tabs-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <form action="{{ route('settings.update-profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-4">
                                <div class="col-md-3 text-center">
                                    <div class="profile-photo-container mb-3">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="img-thumbnail rounded-circle profile-photo">
                                        @else
                                            <div class="profile-photo-placeholder rounded-circle">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile_photo" class="form-label">Profile Photo</label>
                                        <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                                        <div class="form-text">JPG or PNG, max 2MB</div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="company" class="form-label">Company</label>
                                            <input type="text" class="form-control @error('company') is-invalid @enderror" id="company" name="company" value="{{ old('company', $user->company) }}">
                                            @error('company')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Password Tab -->
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="{{ route('settings.update-password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key me-2"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview profile photo before upload
        const profilePhotoInput = document.getElementById('profile_photo');
        const profilePhotoContainer = document.querySelector('.profile-photo-container');
        
        if (profilePhotoInput) {
            profilePhotoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        let img = profilePhotoContainer.querySelector('img');
                        
                        if (!img) {
                            profilePhotoContainer.innerHTML = '';
                            img = document.createElement('img');
                            img.classList.add('img-thumbnail', 'rounded-circle', 'profile-photo');
                            profilePhotoContainer.appendChild(img);
                        }
                        
                        img.src = e.target.result;
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endsection

@section('styles')
<style>
    .profile-photo-container {
        width: 150px;
        height: 150px;
        margin: 0 auto;
        position: relative;
    }
    
    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    
    .profile-photo-placeholder i {
        font-size: 4rem;
        color: #adb5bd;
    }
</style>
@endsection

@extends('layouts.app')

@section('title', 'Roles Management - CPMS')

@section('page_title', 'Roles Management')

@section('sidebar')
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
        <a class="nav-link active" href="{{ route('admin.roles.index') }}">
            <i class="fas fa-user-tag"></i> Roles
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.projects.index') }}">
            <i class="fas fa-project-diagram"></i> Projects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.resources.index') }}">
            <i class="fas fa-tools"></i> Resources
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.reports.index') }}">
            <i class="fas fa-file-alt"></i> Reports
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.activity-logs.index') }}">
            <i class="fas fa-clipboard-list"></i> Activity Logs
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.notifications.index') }}">
            <i class="fas fa-bell"></i> Notifications
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.settings.index') }}">
            <i class="fas fa-cog"></i> Settings
        </a>
    </li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-user-tag me-2"></i> System Roles</span>
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Role
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Users Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ ucfirst($role->name) }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $role->users_count }}</span>
                                </td>
                                <td>{{ $role->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $role->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $role->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $role->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete the role <strong>{{ ucfirst($role->name) }}</strong>?</p>
                                                    @if($role->users_count > 0)
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Warning: This role is assigned to {{ $role->users_count }} user(s). Deleting it will affect these users.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
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
                                <td colspan="6" class="text-center">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <i class="fas fa-chart-pie me-2"></i> Role Distribution
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <canvas id="roleDistributionChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('roleDistributionChart').getContext('2d');
            
            // Extract data from the roles
            const roleNames = @json($roles->pluck('name'));
            const userCounts = @json($roles->pluck('users_count'));
            
            // Define colors for each role
            const colors = [
                '#3498db', // admin
                '#2ecc71', // project_owner
                '#f39c12', // engineer
                '#e74c3c'  // contractor
            ];
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: roleNames.map(role => role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ')),
                    datasets: [{
                        data: userCounts,
                        backgroundColor: colors.slice(0, roleNames.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
    </script>
@endsection

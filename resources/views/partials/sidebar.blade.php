{{-- Admin Sidebar --}}
@if(auth()->user()->hasRole('admin'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
        <i class="fas fa-users"></i> Users
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
        <i class="fas fa-user-tag"></i> Roles
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">
        <i class="fas fa-project-diagram"></i> Projects
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
        <i class="fas fa-clipboard-list"></i> Activity Logs
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}" href="{{ route('admin.resources.index') }}">
        <i class="fas fa-boxes"></i> Resources
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</li>
@endif

{{-- Project Owner Sidebar --}}
@if(auth()->user()->hasRole('project_owner'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.projects.*') ? 'active' : '' }}" href="{{ route('owner.projects.index') }}">
        <i class="fas fa-project-diagram"></i> My Projects
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.tasks.*') ? 'active' : '' }}" href="{{ route('owner.tasks.index') }}">
        <i class="fas fa-tasks"></i> Tasks
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}" href="{{ route('owner.reports.index') }}">
        <i class="fas fa-file-alt"></i> Reports
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.resource-requests.*') ? 'active' : '' }}" href="{{ route('owner.resource-requests.index') }}">
        <i class="fas fa-tools"></i> Resource Requests
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('owner.files.*') ? 'active' : '' }}" href="{{ route('owner.files.index') }}">
        <i class="fas fa-file"></i> Files
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</li>
@endif

{{-- Engineer Sidebar --}}
@if(auth()->user()->hasRole('engineer'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.dashboard') ? 'active' : '' }}" href="{{ route('engineer.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.tasks.*') ? 'active' : '' }}" href="{{ route('engineer.tasks.index') }}">
        <i class="fas fa-tasks"></i> Tasks
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.reports.*') ? 'active' : '' }}" href="{{ route('engineer.reports.index') }}">
        <i class="fas fa-file-alt"></i> Reports
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.resource-requests.*') ? 'active' : '' }}" href="{{ route('engineer.resource-requests.index') }}">
        <i class="fas fa-tools"></i> Resource Requests
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.files.*') ? 'active' : '' }}" href="{{ route('engineer.files.index') }}">
        <i class="fas fa-file"></i> Files
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('engineer.invitations.*') ? 'active' : '' }}" href="{{ route('engineer.invitations.index') }}">
        <i class="fas fa-envelope"></i> Invitations
        @if(isset($invitationCount) && $invitationCount > 0)
            <span class="badge bg-danger">{{ $invitationCount }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</li>
@endif

{{-- Contractor Sidebar --}}
@if(auth()->user()->hasRole('contractor'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('contractor.dashboard') ? 'active' : '' }}" href="{{ route('contractor.dashboard') }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('contractor.tasks.*') ? 'active' : '' }}" href="{{ route('contractor.tasks.index') }}">
        <i class="fas fa-tasks"></i> Tasks
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('contractor.resource-requests.*') ? 'active' : '' }}" href="{{ route('contractor.resource-requests.index') }}">
        <i class="fas fa-tools"></i> Resource Requests
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('contractor.files.*') ? 'active' : '' }}" href="{{ route('contractor.files.index') }}">
        <i class="fas fa-file"></i> Files
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('contractor.invitations.*') ? 'active' : '' }}" href="{{ route('contractor.invitations.index') }}">
        <i class="fas fa-envelope"></i> Invitations
        @if(isset($invitationCount) && $invitationCount > 0)
            <span class="badge bg-danger">{{ $invitationCount }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</li>
@endif

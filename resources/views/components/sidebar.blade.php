<div class="sidebar border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 border-bottom border-white-10">
        <a href="{{ url('/') }}" class="text-decoration-none">
            <img src="{{ asset('images/logo.png') }}" alt="{{ __('app.app_name') }}" class="img-fluid"
                style="max-height: 60px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        </a>
    </div>

    <div class="list-group list-group-flush my-3 d-flex flex-column h-100">
        <!-- Dashboard Link (Common) -->
        @php
            $dashboardRoute = '#';
            if (auth()->user()->isAdmin())
                $dashboardRoute = route('admin.dashboard');
            elseif (auth()->user()->isProjectOwner())
                $dashboardRoute = route('owner.dashboard');
            elseif (auth()->user()->isEngineer())
                $dashboardRoute = route('engineer.dashboard');
            elseif (auth()->user()->isContractor())
                $dashboardRoute = route('contractor.dashboard');
        @endphp

        <a href="{{ $dashboardRoute }}" class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('app.dashboard') }}
        </a>

        <!-- Admin Links -->
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> {{ __('app.users') }}
            </a>
            <a href="{{ route('admin.roles.index') }}"
                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-tag"></i> {{ __('app.roles') }}
            </a>
            <a href="{{ route('admin.projects.index') }}"
                class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
            </a>
            <a href="{{ route('admin.tasks.index') }}"
                class="nav-link {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> {{ __('app.tasks') }}
            </a>
            <a href="{{ route('admin.resources.index') }}"
                class="nav-link {{ request()->routeIs('admin.resources.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> {{ __('app.resources') }}
            </a>
            <a href="{{ route('admin.reports.index') }}"
                class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> {{ __('app.reports') }}
            </a>
            <a href="{{ route('admin.files.index') }}"
                class="nav-link {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
                <i class="fas fa-file"></i> {{ __('app.files') }}
            </a>
            <a href="{{ route('admin.activity-logs.index') }}"
                class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> {{ __('app.activity_logs') }}
            </a>
            <a href="{{ route('admin.settings.index') }}"
                class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> {{ __('app.settings') }}
            </a>
        @endif

        <!-- Engineer Links -->
        @if(auth()->user()->isEngineer())
            <a href="{{ route('engineer.tasks.index') }}"
                class="nav-link {{ request()->routeIs('engineer.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> {{ __('app.tasks') }}
            </a>
            <a href="{{ route('engineer.projects.index') }}"
                class="nav-link {{ request()->routeIs('engineer.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
            </a>
            <a href="{{ route('engineer.reports.index') }}"
                class="nav-link {{ request()->routeIs('engineer.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> {{ __('app.reports') }}
            </a>
            <a href="{{ route('engineer.resource-requests.index') }}"
                class="nav-link {{ request()->routeIs('engineer.resource-requests.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> {{ __('app.resource_requests') }}
            </a>
            <a href="{{ route('engineer.files.index') }}"
                class="nav-link {{ request()->routeIs('engineer.files.*') ? 'active' : '' }}">
                <i class="fas fa-file"></i> {{ __('app.files') }}
            </a>
            <a href="{{ route('engineer.invitations.index') }}"
                class="nav-link {{ request()->routeIs('engineer.invitations.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> {{ __('app.invitations') }}
            </a>
        @endif

        <!-- Project Owner Links -->
        @if(auth()->user()->isProjectOwner())
            <a href="{{ route('owner.projects.index') }}"
                class="nav-link {{ request()->routeIs('owner.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
            </a>
            <a href="{{ route('owner.tasks.index') }}"
                class="nav-link {{ request()->routeIs('owner.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> {{ __('app.tasks') }}
            </a>
            <a href="{{ route('owner.reports.index') }}"
                class="nav-link {{ request()->routeIs('owner.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> {{ __('app.reports') }}
            </a>
            <a href="{{ route('owner.resource-requests.index') }}"
                class="nav-link {{ request()->routeIs('owner.resource-requests.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> {{ __('app.resource_requests') }}
            </a>
            <a href="{{ route('owner.files.index') }}"
                class="nav-link {{ request()->routeIs('owner.files.*') ? 'active' : '' }}">
                <i class="fas fa-file"></i> {{ __('app.files') }}
            </a>
        @endif

        <!-- Contractor Links -->
        @if(auth()->user()->isContractor())
            <a href="{{ route('contractor.tasks.index') }}"
                class="nav-link {{ request()->routeIs('contractor.tasks.*') ? 'active' : '' }}">
                <i class="fas fa-tasks"></i> {{ __('app.tasks') }}
            </a>
            <a href="{{ route('contractor.projects.index') }}"
                class="nav-link {{ request()->routeIs('contractor.projects.*') ? 'active' : '' }}">
                <i class="fas fa-project-diagram"></i> {{ __('app.projects') }}
            </a>
            <a href="{{ route('contractor.resource-requests.index') }}"
                class="nav-link {{ request()->routeIs('contractor.resource-requests.*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> {{ __('app.resource_requests') }}
            </a>
            <a href="{{ route('contractor.files.index') }}"
                class="nav-link {{ request()->routeIs('contractor.files.*') ? 'active' : '' }}">
                <i class="fas fa-file"></i> {{ __('app.files') }}
            </a>
            <a href="{{ route('contractor.invitations.index') }}"
                class="nav-link {{ request()->routeIs('contractor.invitations.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> {{ __('app.invitations') }}
            </a>
        @endif

        <!-- Contextual Links (if any passed via slot) -->
        {{ $slot }}

        <!-- Spacer to push Logout to bottom -->
        <div class="mt-auto mb-4 px-3">
            <form action="{{ route('logout') }}" method="POST" class="d-grid">
                @csrf
                <button type="submit"
                    class="btn btn-danger btn-sm text-start ps-3 d-flex align-items-center justify-content-center">
                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('app.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
<div class="sidebar border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold border-bottom border-white-10">
        <i class="fas fa-cube me-2"></i>{{ __('app.app_name') }}
    </div>
    <div class="list-group list-group-flush my-3">
        {{ $slot }}

        <!-- Logout (Mobile/Sidebar) -->
        <form action="{{ route('logout') }}" method="POST" class="d-grid gap-2 mx-3 mt-4">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm text-start ps-3">
                <i class="fas fa-sign-out-alt me-2"></i> {{ __('app.logout') }}
            </button>
        </form>
    </div>
</div>
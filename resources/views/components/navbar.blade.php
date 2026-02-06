<nav class="navbar navbar-expand-lg navbar-light bg-transparent py-3 px-4">
    <div class="d-flex align-items-center w-100 justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-bars fs-4 me-3 text-primary d-block d-md-none" id="menu-toggle"></i>
            <h2 class="fs-4 fw-bold mb-0 text-dark">{{ $title ?? '' }}</h2>
        </div>

        <div class="d-flex align-items-center gap-3">
            <!-- Language Switcher -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle text-dark fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-globe me-1 text-primary"></i>
                    {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">
                            🇺🇸 English
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">
                            🇸🇦 العربية
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User Profile -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                    data-bs-toggle="dropdown">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                        style="width: 35px; height: 35px;">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <span class="d-none d-lg-inline text-dark fw-bold">{{ Auth::user()->name ?? 'User' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2 text-muted"></i>
                            {{ __('app.profile') }}</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="fas fa-sign-out-alt me-2"></i> {{ __('app.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
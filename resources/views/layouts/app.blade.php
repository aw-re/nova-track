<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('app.app_name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 -->
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    @endif

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/novatrack.css') }}">
    @yield('styles')
</head>

<body class="bg-light">

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar border-end" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 text-white fs-4 fw-bold border-bottom border-white-10">
                <i class="fas fa-cube me-2"></i>{{ __('app.app_name') }}
            </div>
            <div class="list-group list-group-flush my-3">
                @yield('sidebar')

                <!-- Logout (Mobile/Sidebar) -->
                <form action="{{ route('logout') }}" method="POST" class="d-grid gap-2 mx-3 mt-4">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm text-start ps-3">
                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('app.logout') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <nav class="navbar navbar-expand-lg navbar-light bg-transparent py-3 px-4">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bars fs-4 me-3 text-primary d-block d-md-none" id="menu-toggle"></i>
                        <h2 class="fs-4 fw-bold mb-0 text-dark">@yield('page_title')</h2>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <!-- Language Switcher -->
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-dark fw-bold" href="#" role="button"
                                data-bs-toggle="dropdown">
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
                                <span
                                    class="d-none d-lg-inline text-dark fw-bold">{{ Auth::user()->name ?? 'User' }}</span>
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

            <div class="container-fluid px-4 pb-5">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var el = document.getElementById("wrapper");
        var toggleButton = document.getElementById("menu-toggle");

        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    </script>
    @yield('scripts')
</body>

</html>
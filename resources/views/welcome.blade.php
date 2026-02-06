<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.welcome_title') }} - {{ __('app.app_name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cairo:wght@400;600;700&display=swap"
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

    <style>
        .hero-section {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            color: white;
            padding: 120px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .feature-icon-wrapper {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon-wrapper {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1) rotate(5deg);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top glass-effect">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <i class="fas fa-cube text-primary fs-3"></i>
                <span class="brand-text">{{ __('app.app_name') }}</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1 text-primary"></i>
                            {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">🇺🇸 English</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">🇸🇦 العربية</a>
                            </li>
                        </ul>
                    </li>
                    @auth
                                    <li class="nav-item">
                                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') :
                        (auth()->user()->isProjectOwner() ? route('owner.dashboard') :
                            (auth()->user()->isEngineer() ? route('engineer.dashboard') :
                                route('contractor.dashboard'))) }}" class="btn btn-primary rounded-pill px-4">
                                            <i class="fas fa-tachometer-alt me-2"></i> {{ __('app.dashboard') }}
                                        </a>
                                    </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}"
                                class="btn btn-outline-primary rounded-pill px-4 me-2">{{ __('app.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}"
                                class="btn btn-primary rounded-pill px-4">{{ __('app.register') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container position-relative z-1">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <span
                            class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3">
                            🚀 {{ __('app.welcome_subtitle') ?? 'Next Generation Project Management' }}
                        </span>
                    </div>
                    <h1 class="display-3 fw-bold mb-4 leading-tight">{{ __('app.app_name') }}</h1>
                    <p class="lead text-white-50 mb-5 fs-4">{{ __('app.welcome_subheading') }}</p>
                    <div class="d-flex justify-content-center gap-3">
                        @auth
                            <a href="{{ route('admin.dashboard') }}"
                                class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow-lg">
                                {{ __('app.welcome_cta') }} <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-lg">
                                {{ __('app.get_started') }} <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold">
                                {{ __('app.learn_more') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold display-6 mb-3 text-dark">{{ __('app.streamline_heading') }}</h2>
                    <p class="text-muted fs-5">{{ __('app.streamline_subheading') }}</p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center hover-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('app.feature_project_management') }}</h4>
                        <p class="text-muted">{{ __('app.feature_project_management_desc') }}</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center hover-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('app.feature_task_management') }}</h4>
                        <p class="text-muted">{{ __('app.feature_task_management_desc') }}</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center hover-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="fw-bold mb-3">{{ __('app.feature_team_collaboration') }}</h4>
                        <p class="text-muted">{{ __('app.feature_team_collaboration_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-light">
        <div class="container py-5">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6">
                    <div class="display-4 fw-bold text-primary mb-2">500+</div>
                    <div class="text-muted fw-bold text-uppercase small ls-1">Projects</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="display-4 fw-bold text-secondary mb-2">1.2k</div>
                    <div class="text-muted fw-bold text-uppercase small ls-1">Users</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="display-4 fw-bold text-success mb-2">98%</div>
                    <div class="text-muted fw-bold text-uppercase small ls-1">Completion Rate</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="display-4 fw-bold text-warning mb-2">24/7</div>
                    <div class="text-muted fw-bold text-uppercase small ls-1">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5 bg-white">
        <div class="container py-5">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="fw-bold display-6 mb-3 text-dark">{{ __('app.meet_our_team') ?? 'Meet Our Team' }}</h2>
                    <p class="text-muted fs-5">
                        {{ __('app.team_description') ?? 'The experts behind NovaTrack success' }}</p>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Team Member 1 -->
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-3">
                            <div class="mb-3 position-relative mx-auto"
                                style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/AL-REYASHI.jpg') }}" alt="AL-REYASHI"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h6 class="fw-bold mb-1">Al-Reyashi</h6>
                            <small class="text-primary fw-bold text-uppercase" style="font-size: 0.7rem;">Team
                                Lead</small>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-3">
                            <div class="mb-3 position-relative mx-auto"
                                style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/Al-Mansour.jpg') }}" alt="Al-Mansour"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h6 class="fw-bold mb-1">Al-Mansour</h6>
                            <small class="text-primary fw-bold text-uppercase"
                                style="font-size: 0.7rem;">Developer</small>
                        </div>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-3">
                            <div class="mb-3 position-relative mx-auto"
                                style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/Al-Najhi.jpg') }}" alt="Al-Najhi"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h6 class="fw-bold mb-1">Al-Najhi</h6>
                            <small class="text-primary fw-bold text-uppercase"
                                style="font-size: 0.7rem;">Developer</small>
                        </div>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-3">
                            <div class="mb-3 position-relative mx-auto"
                                style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/Handhal.jpg') }}" alt="Handhal"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h6 class="fw-bold mb-1">Handhal</h6>
                            <small class="text-primary fw-bold text-uppercase"
                                style="font-size: 0.7rem;">Developer</small>
                        </div>
                    </div>
                </div>

                <!-- Team Member 5 -->
                <div class="col-md-4 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center hover-card">
                        <div class="card-body p-3">
                            <div class="mb-3 position-relative mx-auto"
                                style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%;">
                                <img src="{{ asset('images/Naji.jpg') }}" alt="Naji"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h6 class="fw-bold mb-1">Naji</h6>
                            <small class="text-primary fw-bold text-uppercase"
                                style="font-size: 0.7rem;">Developer</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-cube text-primary fs-3"></i>
                        <span class="fs-4 fw-bold">{{ __('app.app_name') }}</span>
                    </div>
                    <p class="text-white-50">Professional construction project management software designed for modern
                        engineering teams.</p>
                </div>
                <div class="col-lg-2 offset-lg-6">
                    <h6 class="fw-bold mb-3">Language</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('language.switch', 'en') }}"
                                class="text-white-50 text-decoration-none hover-white">English</a></li>
                        <li><a href="{{ route('language.switch', 'ar') }}"
                                class="text-white-50 text-decoration-none hover-white">العربية</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-white opacity-10 my-4">
            <div class="text-center text-white-50">
                <small>{!! __('app.copyright', ['year' => date('Y')]) !!}</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
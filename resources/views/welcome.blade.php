<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.welcome_title') }}</title>
    @if(app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.rtl.min.css">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .hero {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        .hero h1 {
            font-weight: 700;
            font-size: 2.5rem;
        }
        .feature-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: transform 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            padding: 10px 25px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .btn-outline-light {
            padding: 10px 25px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">{{ __('app.app_name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe me-1"></i> {{ app()->getLocale() == 'ar' ? __('app.arabic') : __('app.english') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}"><i class="fas fa-language me-2"></i> {{ __('app.english') }}</a></li>
                            <li><a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}"><i class="fas fa-language me-2"></i> {{ __('app.arabic') }}</a></li>
                        </ul>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : 
                                (auth()->user()->isProjectOwner() ? route('owner.dashboard') : 
                                (auth()->user()->isEngineer() ? route('engineer.dashboard') : 
                                route('contractor.dashboard'))) }}">
                                {{ __('app.dashboard') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">{{ __('app.logout') }}</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('app.login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('app.register') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container text-center">
            <h1>{{ __('app.app_name') }}</h1>
            <p class="lead mb-4">{{ __('app.welcome_subheading') }}</p>
            <div>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : 
                        (auth()->user()->isProjectOwner() ? route('owner.dashboard') : 
                        (auth()->user()->isEngineer() ? route('engineer.dashboard') : 
                        route('contractor.dashboard'))) }}" class="btn btn-primary me-2">
                        {{ __('app.welcome_cta') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">{{ __('app.login') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light">{{ __('app.register') }}</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="row text-center mb-5">
            <div class="col-md-12">
                <h2>{{ __('app.streamline_heading') }}</h2>
                <p class="lead">{{ __('app.streamline_subheading') }}</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h4>{{ __('app.feature_project_management') }}</h4>
                        <p>{{ __('app.feature_project_management_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>{{ __('app.feature_task_management') }}</h4>
                        <p>{{ __('app.feature_task_management_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>{{ __('app.feature_team_collaboration') }}</h4>
                        <p>{{ __('app.feature_team_collaboration_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>{{ __('app.feature_documentation') }}</h4>
                        <p>{{ __('app.feature_documentation_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>{{ __('app.feature_progress_tracking') }}</h4>
                        <p>{{ __('app.feature_progress_tracking_desc') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>{{ __('app.feature_ratings') }}</h4>
                        <p>{{ __('app.feature_ratings_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5">
        @include('team')
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>{!! __('app.copyright', ['year' => date('Y')]) !!}</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

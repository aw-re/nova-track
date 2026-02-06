<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.register') }} - {{ __('app.app_name') }}</title>

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
        body {
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
    </style>
</head>

<body>
    <div class="language-switcher position-absolute top-0 end-0 m-4">
        <div class="dropdown">
            <button class="btn btn-white shadow-sm dropdown-toggle rounded-pill px-3" type="button"
                data-bs-toggle="dropdown">
                <i class="fas fa-globe me-1 text-primary"></i> {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
            </button>
            <ul class="dropdown-menu border-0 shadow-lg dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">🇺🇸 English</a></li>
                <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">🇸🇦 العربية</a></li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-cube text-primary fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark">{{ __('app.create_account') ?? 'Create Account' }}</h3>
                    <p class="text-muted">{{ __('app.register_prompt') ?? 'Join our community today' }}</p>
                </div>

                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-3 mb-4">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="name"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.full_name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="far fa-user text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="name"
                                        name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.email_address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="far fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0 ps-0" id="email"
                                        name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="mb-3">
                                <label for="phone"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.phone_number') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-phone text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0 ps-0" id="phone"
                                        name="phone" value="{{ old('phone') }}" placeholder="+1234567890">
                                </div>
                            </div>

                            <!-- Role Selection -->
                            <div class="mb-3">
                                <label for="role_id"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.register_as') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-user-tag text-muted"></i></span>
                                    <select class="form-select bg-light border-start-0 ps-0" id="role_id" name="role_id"
                                        required>
                                        <option value="" selected disabled>{{ __('app.select_your_role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-0"
                                        id="password" name="password" required placeholder="••••••••">
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation"
                                    class="form-label fw-bold small text-muted text-uppercase ls-1">{{ __('app.confirm_password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-lock text-muted"></i></span>
                                    <input type="password" class="form-control bg-light border-start-0 ps-0"
                                        id="password_confirmation" name="password_confirmation" required
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                    {{ __('app.register') }} <i class="fas fa-user-plus ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        {{ __('app.already_have_account') }}
                        <a href="{{ route('login') }}"
                            class="text-primary fw-bold text-decoration-none">{{ __('app.login_here') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
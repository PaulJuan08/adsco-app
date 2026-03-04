<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ADSCO LMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
</head>
<body>
    <div class="auth-particle auth-particle-1"></div>
    <div class="auth-particle auth-particle-2"></div>
    <div class="auth-particle auth-particle-3"></div>

    <nav class="auth-navbar">
        <div class="container">
            <a href="/" class="brand">
                <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="brand-logo">
                <span class="brand-text">ADS<span class="accent">CO</span></span>
            </a>
            <div class="nav-links">
                <a href="/" class="nav-link"><i class="fas fa-home"></i><span>Home</span></a>
                <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
                <a href="{{ route('register') }}" class="nav-link"><i class="fas fa-user-plus"></i><span>Register</span></a>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Forgot Password</h1>
                <p class="subtitle">We'll send you a reset link</p>
            </div>

            <div class="auth-card-body">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; text-align: center;">
                    Enter your registered email address and we will send you a link to reset your password.
                </p>

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your registered email"
                                required
                                autofocus>
                        </div>
                        @error('email')
                            <span class="form-text text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send Reset Link</span>
                    </button>
                </form>

                <div class="divider">
                    <span>Remembered your password?</span>
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline btn-block">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Login</span>
                </a>
            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <p>
            <i class="far fa-copyright"></i> {{ date('Y') }} Agusan Del Sur College. All rights reserved.
        </p>
    </footer>

    <script>
        document.getElementById('forgotForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending...</span>';
            btn.disabled = true;
        });
    </script>
</body>
</html>

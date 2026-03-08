<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ADSCO LMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
</head>
<body>
    <div class="auth-particle auth-particle-1"></div>
    <div class="auth-particle auth-particle-2"></div>
    <div class="auth-particle auth-particle-3"></div>

    @include('partials.navbar', ['activePage' => 'login'])

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Reset Password</h1>
                <p class="subtitle">Set your new password</p>
            </div>

            <div class="auth-card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

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
                                value="{{ old('email', $email) }}"
                                placeholder="Your email address"
                                required>
                        </div>
                        @error('email')
                            <span class="form-text text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            New Password <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                placeholder="At least 8 characters"
                                required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="form-text text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            Confirm New Password <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Repeat your new password"
                                required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                        <span id="passwordMatchMsg" style="font-size: 0.8rem; display: none;"></span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
                        <i class="fas fa-key"></i>
                        <span>Reset Password</span>
                    </button>
                </form>

                <div class="divider">
                    <span>Back to login?</span>
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
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon  = document.getElementById(iconId);
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Live password match check
        const pw  = document.getElementById('password');
        const pw2 = document.getElementById('password_confirmation');
        const msg = document.getElementById('passwordMatchMsg');

        pw2.addEventListener('input', function() {
            if (!this.value) { msg.style.display = 'none'; return; }
            if (this.value === pw.value) {
                msg.textContent = '✓ Passwords match';
                msg.style.color = '#16a34a';
            } else {
                msg.textContent = '✗ Passwords do not match';
                msg.style.color = '#dc2626';
            }
            msg.style.display = 'block';
        });

        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            if (pw.value !== pw2.value) {
                e.preventDefault();
                msg.textContent = '✗ Passwords do not match';
                msg.style.color = '#dc2626';
                msg.style.display = 'block';
                return;
            }
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Resetting...</span>';
            btn.disabled = true;
        });
    </script>
</body>
</html>

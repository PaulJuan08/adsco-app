<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ADSCO Learning Management System - Login to access your courses and materials">
    <title>Login - ADSCO LMS</title>
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Logo -->
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">

    <!-- <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lf-InMsAAAAALLl-UT7ohlaUuRFIMQLqxhD15I8"></script> -->
    
    <style>
        /* Registration Success Message Styling */
        .registration-success {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .registration-success i {
            font-size: 1.5rem;
            margin-right: 0.75rem;
        }
        .registration-success strong {
            font-size: 1.1rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        .registration-success p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .steps-indicator {
            display: flex;
            margin-top: 1rem;
            gap: 0.5rem;
        }
        .step {
            flex: 1;
            height: 4px;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
            position: relative;
        }
        .step.active {
            background: white;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }
        .step.completed {
            background: #10b981;
        }
    </style>
</head>
<body>
    <!-- Floating particles for background decoration -->
    <div class="auth-particle auth-particle-1"></div>
    <div class="auth-particle auth-particle-2"></div>
    <div class="auth-particle auth-particle-3"></div>

    <!-- Navigation -->
    <nav class="auth-navbar">
        <div class="container">
            <a href="/" class="brand">
                <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="brand-logo">
                <span class="brand-text">ADS<span class="accent">CO</span></span>
            </a>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links" id="navLinks">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('login') }}" class="nav-link active">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="auth-container">
        <div class="auth-card">
            <!-- Card Header -->
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Welcome Back</h1>
                <p class="subtitle">Learning Management System</p>
            </div>

            <!-- Card Body -->
            <div class="auth-card-body">
                <!-- Registration Success Message (shown after registration) -->
                @if(session('registration_success'))
                    <div class="registration-success">
                        <div style="display: flex; align-items: center; margin-bottom: 0.5rem;">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Account Created Successfully!</strong>
                                <p>{{ session('registration_success') }}</p>
                            </div>
                        </div>
                        <div class="steps-indicator">
                            <div class="step completed" title="Account Created"></div>
                            <div class="step active" title="Email Verification"></div>
                            <div class="step" title="Admin Approval"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.8rem;">
                            <span>✓ Account Created</span>
                            <span>⟳ Email Verification</span>
                            <span>⏳ Admin Approval</span>
                        </div>
                        <p style="margin-top: 0.75rem; font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> 
                            Please check your email to verify your account. After verification, an administrator will approve your account.
                        </p>
                    </div>
                @endif

                <!-- Success Message (other success messages) -->
                @if(session('success') && !session('registration_success'))
                    <div class="alert alert-success alert-dismissible">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    @if($errors->has('approval'))
                        <!-- Pending Approval Warning -->
                        <div class="alert alert-warning alert-dismissible" style="border-left: 4px solid #f59e0b;">
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <i class="fas fa-hourglass-half" style="font-size: 1.5rem; color: #f59e0b;"></i>
                                <div>
                                    <strong style="display: block; margin-bottom: 0.25rem; font-size: 1rem;">Account Pending Approval</strong>
                                    <p style="margin: 0; color: #92400e;">{{ $errors->first('approval') }}</p>
                                    <p style="margin-top: 0.5rem; margin-bottom: 0; font-size: 0.875rem; color: #92400e;">
                                        <i class="fas fa-info-circle"></i> 
                                        You will receive an email once your account has been approved.
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @elseif($errors->has('login') || $errors->has('password') || $errors->has('email'))
                        <!-- Wrong Credentials Error (KEEPING THIS) -->
                        <div class="alert alert-danger alert-dismissible">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-exclamation-circle" style="font-size: 1.5rem; color: #dc2626;"></i>
                                <div>
                                    <strong style="display: block; margin-bottom: 0.25rem;">Invalid Credentials</strong>
                                    @if($errors->has('login'))
                                        <p style="margin: 0;">{{ $errors->first('login') }}</p>
                                    @elseif($errors->has('password'))
                                        <p style="margin: 0;">{{ $errors->first('password') }}</p>
                                    @elseif($errors->has('email'))
                                        <p style="margin: 0;">{{ $errors->first('email') }}</p>
                                    @else
                                        <p style="margin: 0;">The email or password you entered is incorrect.</p>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @else
                        <!-- Generic Error -->
                        <div class="alert alert-danger alert-dismissible">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>
                                <strong>Login failed.</strong> {{ $errors->first() }}
                            </span>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                @endif

                <!-- Email Verification Warning -->
                @if(session('warning'))
                    @if(session('warning') != 'pending_approval')
                        <div class="alert alert-warning alert-dismissible">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>{{ session('warning') }}</span>
                            <button type="button" class="btn-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endif
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                    @csrf

                    <!-- Email or ID Field -->
                    <div class="form-group">
                        <label for="login" class="form-label">
                            Email or ID <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-user input-icon"></i>
                            <input 
                                type="text" 
                                class="form-control @error('login') is-invalid @enderror" 
                                id="login" 
                                name="login" 
                                value="{{ old('login') }}" 
                                placeholder="Enter your email or student/employee ID"
                                required 
                                autofocus>
                        </div>
                        @error('login')
                            <span class="form-text text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            Password <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password"
                                placeholder="Enter your password"
                                required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="form-text text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password (Commented out) -->
                    <!-- <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="auth-link" style="font-size: 0.9rem;">
                            Forgot Password?
                        </a>
                    </div> -->

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="divider">
                    <span>Don't have an account?</span>
                </div>

                <!-- Register Link -->
                <a href="{{ route('register') }}" class="btn btn-outline btn-block">
                    <i class="fas fa-user-plus"></i>
                    <span>Create New Account</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="auth-footer">
        <p>
            <i class="far fa-copyright"></i> {{ date('Y') }} Agusan Del Sur College. All rights reserved.
        </p>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const navLinks = document.getElementById('navLinks');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('show');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            });
        }

        // Password toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form submission with loading state
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner"></span><span>Logging in...</span>';
            });
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                alert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // function onClick(e) {
        //     e.preventDefault();
        //     grecaptcha.enterprise.ready(async () => {
        //     const token = await grecaptcha.enterprise.execute('6Lf-InMsAAAAALLl-UT7ohlaUuRFIMQLqxhD15I8', {action: 'LOGIN'});
        //     });
        // }
    </script>
</body>
</html>
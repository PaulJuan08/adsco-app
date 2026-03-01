<!-- resources/views/auth/verify-email.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - ADSCO LMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
    <style>
        .verification-icon {
            font-size: 4rem;
            color: var(--color-adsco-primary, #4f46e5);
            margin-bottom: 1rem;
        }
        .email-highlight {
            background: #eef2ff;
            color: #4f46e5;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1rem;
        }
        .steps {
            text-align: left;
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
        }
        .step-number {
            width: 24px;
            height: 24px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
        }
        .step-number.completed {
            background: #48bb78;
            color: white;
        }
        .step-number.active {
            background: #f59e0b;
            color: white;
        }
        .step-number.pending {
            background: #e5e7eb;
            color: #9ca3af;
        }
        .step-text {
            font-size: 0.875rem;
            color: #374151;
        }
        .step-text strong {
            color: #4f46e5;
        }
        .pending-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
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
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
            <div class="nav-links" id="navLinks">
                <a href="/" class="nav-link"><i class="fas fa-home"></i><span>Home</span></a>
                <a href="{{ route('login') }}" class="nav-link"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
                <a href="{{ route('register') }}" class="nav-link active"><i class="fas fa-user-plus"></i><span>Register</span></a>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo" class="logo">
                </div>
                <h1>Verify Your Email</h1>
                <p class="subtitle">Almost there! Just one more step.</p>
            </div>

            <div class="auth-card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <i class="fas fa-exclamation-triangle"></i><span>{{ session('warning') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <div class="text-center">
                    <div class="pending-badge">
                        <i class="fas fa-hourglass-half"></i> Pending Email Verification
                    </div>
                    
                    <div class="verification-icon">
                        <i class="fas fa-envelope-circle-check"></i>
                    </div>
                    
                    <p style="margin-bottom: 1.5rem; color: #4b5563;">
                        We've sent a verification link to:
                    </p>
                    
                    <div class="email-highlight" style="display: inline-block; margin-bottom: 1.5rem;">
                        {{ Auth::user()->email }}
                    </div>

                    <div class="steps">
                        <div class="step-item">
                            <div class="step-number completed">1</div>
                            <div class="step-text">
                                <strong>Step 1:</strong> Account created successfully
                                <i class="fas fa-check-circle" style="color: #48bb78; margin-left: 0.5rem;"></i>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number active">2</div>
                            <div class="step-text">
                                <strong>Step 2:</strong> Verify your email address
                                <i class="fas fa-hourglass-half" style="color: #f59e0b; margin-left: 0.5rem;"></i>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number pending">3</div>
                            <div class="step-text">
                                <strong>Step 3:</strong> Admin approves your account
                            </div>
                        </div>
                    </div>

                    <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin: 1.5rem 0; text-align: left;">
                        <p style="margin: 0; color: #0369a1; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> 
                            After email verification, an administrator will review and approve your account. 
                            You will receive another email once your account is approved.
                        </p>
                    </div>

                    <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">

                    <form method="POST" action="{{ route('verification.resend') }}" style="margin-bottom: 0.5rem;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Resend Verification Email
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-block">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>

                <div class="divider"><span>Need help?</span></div>
                <div class="text-center">
                    <p style="font-size: 0.875rem; color: #6b7280;">
                        <i class="fas fa-envelope"></i> Check your spam folder if you don't see the email.<br>
                        Contact support at <a href="mailto:support@adsco.edu.ph">support@adsco.edu.ph</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="auth-footer">
        <p><i class="far fa-copyright"></i> {{ date('Y') }} Agusan Del Sur College. All rights reserved.</p>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('show');
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        // Auto-dismiss alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(a => {
                a.style.opacity = '0';
                setTimeout(() => a.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html>
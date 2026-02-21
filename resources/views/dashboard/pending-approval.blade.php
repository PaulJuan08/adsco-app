<!-- resources/views/dashboard/pending-approval.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approval - ADSCO LMS</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('assets/img/adsco-logo.png') }}" type="image/png">
    <style>
        .pending-icon {
            font-size: 4rem;
            color: #f59e0b;
            margin-bottom: 1rem;
        }
        .status-steps {
            background: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border: 1px solid #e5e7eb;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .step:last-child {
            border-bottom: none;
        }
        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .step.completed .step-number {
            background: #48bb78;
            color: white;
        }
        .step.active .step-number {
            background: #f59e0b;
            color: white;
        }
        .step.pending .step-number {
            background: #e5e7eb;
            color: #9ca3af;
        }
        .step.completed .step-text { color: #065f46; }
        .step.active .step-text { color: #92400e; font-weight: 600; }
        .step.pending .step-text { color: #6b7280; }
        .user-info {
            background: #eef2ff;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: center;
        }
        .user-email {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4f46e5;
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
                <h1>Account Pending Approval</h1>
                <p class="subtitle">Almost there! One final step.</p>
            </div>

            <div class="auth-card-body">
                @if(session('warning'))
                <div class="alert alert-warning alert-dismissible">
                    <i class="fas fa-exclamation-triangle"></i><span>{{ session('warning') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
                </div>
                @endif

                <div class="text-center">
                    <div class="pending-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>

                    <div class="user-info">
                        <div style="font-size: 0.875rem; color: #4b5563; margin-bottom: 0.25rem;">Welcome,</div>
                        <div style="font-size: 1.25rem; font-weight: 600; color: #1f2937;">{{ $user->f_name }} {{ $user->l_name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </div>

                    <p style="color: #4b5563; margin-bottom: 1.5rem;">
                        Your account has been successfully verified. It is now awaiting administrator approval.
                    </p>

                    <div class="status-steps">
                        <div class="step completed">
                            <div class="step-number">1</div>
                            <div class="step-text">
                                <strong>Step 1:</strong> Email verified successfully
                                <i class="fas fa-check-circle" style="color: #48bb78; margin-left: 0.5rem;"></i>
                            </div>
                        </div>
                        <div class="step active">
                            <div class="step-number">2</div>
                            <div class="step-text">
                                <strong>Step 2:</strong> Pending administrator approval
                                <i class="fas fa-hourglass-half" style="color: #f59e0b; margin-left: 0.5rem;"></i>
                            </div>
                        </div>
                        <div class="step pending">
                            <div class="step-number">3</div>
                            <div class="step-text">
                                <strong>Step 3:</strong> Access your dashboard
                            </div>
                        </div>
                    </div>

                    <div style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin: 1.5rem 0; text-align: left;">
                        <p style="margin: 0; color: #0369a1; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i> 
                            You will receive an email notification once your account is approved. This usually takes 24-48 hours.
                        </p>
                    </div>

                    <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">

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
                        Contact the administrator at <a href="mailto:admin@adsco.edu.ph">admin@adsco.edu.ph</a>
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
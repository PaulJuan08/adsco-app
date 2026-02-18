<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - ADSCO LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --school-navy: #002147;
            --heritage-gold: #D4AF37;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verification-container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 33, 71, 0.1);
        }
        
        .card-header {
            background-color: var(--school-navy);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px;
        }
        
        .btn-primary {
            background-color: var(--school-navy);
            border-color: var(--school-navy);
        }
        
        .btn-primary:hover {
            background-color: #001835;
            border-color: #001835;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="bi bi-envelope-check me-2"></i>Verify Your Email Address</h4>
            </div>
            <div class="card-body p-5">
                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        A new verification link has been sent to your email address.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <div class="text-center mb-4">
                    <i class="bi bi-envelope display-1 text-primary mb-3"></i>
                    <h5>Email Verification Required</h5>
                    <p class="text-muted">
                        Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
                        If you didn't receive the email, we will gladly send you another.
                    </p>
                </div>
                
                <div class="d-grid gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-envelope-arrow-up me-2"></i>Resend Verification Email
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-left me-2"></i>Logout
                        </button>
                    </form>
                </div>
                
                <div class="mt-4 pt-3 border-top text-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        After verification, your account will be reviewed by an administrator.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer">
    
    <style>
        /* CSS Variables */
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --primary-dark: #3730a3;
            
            --secondary: #6b7280;
            
            --success: #10b981;
            --success-light: #d1fae5;
            
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            
            --danger: #ef4444;
            --danger-light: #fee2e2;
            
            --info: #06b6d4;
            --info-light: #cffafe;
            
            --purple: #8b5cf6;
            --purple-light: #f3e8ff;
            
            --dark: #111827;
            --light: #f9fafb;
            --border: #e5e7eb;
            --card-bg: #ffffff;
            
            --sidebar-bg: #1f2937;
            --sidebar-dark: #111827;
            
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
            
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }
        
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-700);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Layout Container */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-dark) 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Sidebar Header */
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }
        
        .logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
        }
        
        .logo-text {
            font-size: 1.375rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }
        
        /* Sidebar Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1rem;
            border-radius: var(--radius-sm);
            color: #d1d5db;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary);
            transform: scaleY(0);
            transition: transform 0.2s ease;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }
        
        .nav-item.active {
            background: rgba(79, 70, 229, 0.15);
            color: white;
        }
        
        .nav-item.active::before {
            transform: scaleY(1);
        }
        
        .nav-item i {
            width: 22px;
            text-align: center;
            font-size: 1.125rem;
            flex-shrink: 0;
        }
        
        .nav-item span {
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }
        
        .user-profile:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        
        .user-avatar-small {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.125rem;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-details {
            flex: 1;
            min-width: 0;
            transition: opacity 0.3s ease;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9375rem;
            color: white;
            margin-bottom: 0.125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .user-role {
            font-size: 0.8125rem;
            color: #9ca3af;
        }
        
        .logout-btn {
            width: 100%;
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #fecaca;
            transform: translateX(0);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            transition: all 0.3s ease;
        }
        
        /* Mobile Sidebar Toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1.25rem;
            left: 1.25rem;
            z-index: 1001;
            width: 44px;
            height: 44px;
            background: var(--primary);
            border: none;
            border-radius: var(--radius-sm);
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            transition: all 0.2s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        /* Toast Notification System */
        .toast-container {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            pointer-events: none;
            max-width: 420px;
        }
        
        .toast {
            background: white;
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 320px;
            pointer-events: all;
            animation: slideIn 0.3s ease-out;
            border-left: 4px solid;
            position: relative;
            overflow: hidden;
        }
        
        .toast::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            animation: progress 5s linear;
            opacity: 0.3;
        }
        
        .toast.success {
            border-left-color: var(--success);
        }
        
        .toast.error {
            border-left-color: var(--danger);
        }
        
        .toast.warning {
            border-left-color: var(--warning);
        }
        
        .toast.info {
            border-left-color: var(--info);
        }
        
        .toast-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.375rem;
            flex-shrink: 0;
        }
        
        .toast.success .toast-icon {
            background: var(--success-light);
            color: var(--success);
        }
        
        .toast.error .toast-icon {
            background: var(--danger-light);
            color: var(--danger);
        }
        
        .toast.warning .toast-icon {
            background: var(--warning-light);
            color: var(--warning);
        }
        
        .toast.info .toast-icon {
            background: var(--info-light);
            color: var(--info);
        }
        
        .toast-content {
            flex: 1;
            min-width: 0;
        }
        
        .toast-title {
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }
        
        .toast-message {
            font-size: 0.875rem;
            color: var(--gray-600);
            line-height: 1.5;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1.25rem;
            line-height: 1;
            transition: color 0.2s;
            flex-shrink: 0;
            margin-top: -0.125rem;
        }
        
        .toast-close:hover {
            color: var(--gray-600);
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        .toast.removing {
            animation: slideOut 0.3s ease-out forwards;
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        /* Utility Classes */
        .d-none {
            display: none !important;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            :root {
                --sidebar-width: var(--sidebar-collapsed-width);
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
            
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                padding-top: 5rem;
            }
            
            .logo-text,
            .nav-item span,
            .user-details {
                display: none;
            }
            
            .sidebar.mobile-open .logo-text,
            .sidebar.mobile-open .nav-item span,
            .sidebar.mobile-open .user-details {
                display: block;
            }
            
            .sidebar.mobile-open {
                width: 280px;
            }
            
            .toast-container {
                left: 1rem;
                right: 1rem;
                top: 5rem;
                max-width: none;
            }
            
            .toast {
                min-width: unset;
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 0.75rem;
                padding-top: 4.5rem;
            }
            
            .sidebar-toggle {
                top: 1rem;
                left: 1rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .sidebar,
            .sidebar-toggle,
            .toast-container {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Mobile Sidebar Toggle -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>
    
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">A</div>
                <div class="logo-text">ADSCO</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.courses.index') }}" class="nav-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('admin.topics.index') }}" class="nav-item {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Topics</span>
                </a>
                <a href="{{ route('admin.quizzes.index') }}" class="nav-item {{ request()->routeIs('admin.quizzes.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Quizzes</span>
                </a>
            </nav>  
            
            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar-small">
                        {{ strtoupper(substr(Auth::user()->f_name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="user-details">
                        @php
                            $roleMapping = [
                                1 => 'Admin',
                                2 => 'Registrar',
                                3 => 'Teacher',
                                4 => 'Student'
                            ];
                            
                            $user = Auth::user();
                            $roleText = $user ? ($roleMapping[$user->role] ?? 'User') : 'Guest';
                        @endphp
                        
                        <div class="user-name">{{ $user ? $user->f_name : 'Guest' }}</div>
                        <div class="user-role">{{ $roleText }}</div>
                    </div>
                </div>
                
                <button class="nav-item logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    
    <!-- JavaScript -->
    <script>
        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        }
        
        // Close sidebar when clicking on nav items on mobile
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                const navItems = document.querySelectorAll('.nav-item:not(.logout-btn)');
                navItems.forEach(item => {
                    item.addEventListener('click', function() {
                        setTimeout(() => {
                            toggleSidebar();
                        }, 200);
                    });
                });
            }
        });
        
        // Toast Notification System
        const ToastNotification = {
            container: null,
            
            init() {
                this.container = document.getElementById('toast-container');
            },
            
            show(type, title, message, duration = 5000) {
                if (!this.container) this.init();
                
                const toast = document.createElement('div');
                toast.className = `toast ${type}`;
                
                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-times-circle',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle'
                };
                
                toast.innerHTML = `
                    <div class="toast-icon">
                        <i class="fas ${icons[type]}"></i>
                    </div>
                    <div class="toast-content">
                        <div class="toast-title">${this.escapeHtml(title)}</div>
                        <div class="toast-message">${this.escapeHtml(message)}</div>
                    </div>
                    <button class="toast-close" onclick="ToastNotification.close(this.parentElement)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
                this.container.appendChild(toast);
                
                // Auto remove after duration
                if (duration > 0) {
                    setTimeout(() => {
                        this.close(toast);
                    }, duration);
                }
                
                return toast;
            },
            
            close(toast) {
                toast.classList.add('removing');
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.parentElement.removeChild(toast);
                    }
                }, 300);
            },
            
            success(title, message, duration) {
                return this.show('success', title, message, duration);
            },
            
            error(title, message, duration) {
                return this.show('error', title, message, duration);
            },
            
            warning(title, message, duration) {
                return this.show('warning', title, message, duration);
            },
            
            info(title, message, duration) {
                return this.show('info', title, message, duration);
            },
            
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        };
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            ToastNotification.init();
            
            // Check for Laravel session messages
            @if(session('success'))
                ToastNotification.success('Success!', '{{ session('success') }}');
            @endif
            
            @if(session('error'))
                ToastNotification.error('Error!', '{{ session('error') }}');
            @endif
            
            @if(session('warning'))
                ToastNotification.warning('Warning!', '{{ session('warning') }}');
            @endif
            
            @if(session('info'))
                ToastNotification.info('Info', '{{ session('info') }}');
            @endif
            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    ToastNotification.error('Validation Error', '{{ $error }}');
                @endforeach
            @endif
        });
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                }
            }, 250);
        });
    </script>
    
    @stack('scripts')
</body>
</html>
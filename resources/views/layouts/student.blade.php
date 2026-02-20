<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard')</title>
    
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
    
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    <style>
        /* Sidebar-specific styles that extend dashboard.css */
        .layout-with-sidebar {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            box-shadow: var(--shadow-xl);
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
        
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-logo {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar-title {
            font-size: 1.375rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .sidebar-nav-item {
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
        }
        
        .sidebar-nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--success);
            transform: scaleY(0);
            transition: transform 0.2s ease;
        }
        
        .sidebar-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar-nav-item.active {
            background: rgba(16, 185, 129, 0.15);
            color: white;
        }
        
        .sidebar-nav-item.active::before {
            transform: scaleY(1);
        }
        
        .sidebar-nav-item i {
            width: 22px;
            text-align: center;
            font-size: 1.125rem;
            flex-shrink: 0;
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }
        
        .sidebar-user-profile {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-sm);
        }
        
        .sidebar-user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.125rem;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-user-details {
            flex: 1;
            min-width: 0;
        }
        
        .sidebar-user-name {
            font-weight: 600;
            font-size: 0.9375rem;
            color: white;
            margin-bottom: 0.125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar-user-role {
            font-size: 0.8125rem;
            color: #9ca3af;
        }
        
        .sidebar-user-college {
            font-size: 0.75rem;
            color: #10b981;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .sidebar-user-college i {
            font-size: 0.7rem;
        }
        
        .sidebar-user-program {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-top: 0.125rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .sidebar-user-program i {
            font-size: 0.65rem;
        }
        
        .sidebar-logout-btn {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: none;
            cursor: pointer;
            font-family: inherit;
            width: 100%;
        }
        
        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #fecaca;
            transform: translateX(0);
        }
        
        .content-wrapper {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s ease;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0;
            }
        }
        
        .d-none {
            display: none !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="layout-with-sidebar">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo">
                </div>
                <div class="sidebar-title">STUDENT</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('student.courses.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>
                <a href="{{ route('student.quizzes.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.quizzes') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Quizzes</span>
                </a>
                <a href="{{ route('student.colleges.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.colleges.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i>
                    <span>Colleges</span>
                </a>
                <a href="{{ route('student.programs.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.programs.*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Programs</span>
                </a>
            </nav>  
            
            <div class="sidebar-footer">
                <div class="sidebar-user-profile">
                    <div class="sidebar-user-avatar">
                        {{ strtoupper(substr(Auth::user()->f_name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="sidebar-user-details">
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
                        
                        <div class="sidebar-user-name">{{ $user ? $user->f_name . ' ' . $user->l_name : 'Guest' }}</div>
                        <div class="sidebar-user-role">{{ $roleText }}</div>
                        
                        @if($user && $user->isStudent())
                            @if($user->college)
                                <div class="sidebar-user-college">
                                    <i class="fas fa-university"></i>
                                    <span>{{ Str::limit($user->college->college_name, 25) }}</span>
                                </div>
                            @endif
                            
                            @if($user->program)
                                <div class="sidebar-user-program">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>{{ Str::limit($user->program->program_name, 25) }}</span>
                                </div>
                            @endif
                            
                            @if($user->college_year)
                                <div class="sidebar-user-program">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $user->college_year }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                
                <button class="sidebar-nav-item sidebar-logout-btn" onclick="document.getElementById('logout-form').submit()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </aside>
        
        <!-- Main Content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
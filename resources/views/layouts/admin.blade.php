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
    
    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    
    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    
    @stack('styles')
    
    <style>
        /* Dropdown styles with smooth animations */
        .sidebar-dropdown {
            width: 100%;
            margin-bottom: 0.25rem;
            position: relative;
        }
        
        .sidebar-dropdown-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            border-radius: 0.375rem;
            position: relative;
            z-index: 2;
        }
        
        .sidebar-dropdown-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar-dropdown-btn.active {
            background: rgba(102, 126, 234, 0.15);
            color: white;
            border-left: 3px solid #667eea;
        }
        
        .sidebar-dropdown-btn i:first-child {
            width: 20px;
            font-size: 1rem;
            color: currentColor;
        }
        
        .dropdown-arrow {
            margin-left: auto;
            font-size: 0.75rem;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: rgba(255, 255, 255, 0.5);
        }
        
        /* Smooth dropdown menu */
        .sidebar-dropdown-menu {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            margin-left: 1rem;
            padding-left: 0.5rem;
            border-left: 1px dashed rgba(255, 255, 255, 0.1);
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
            transform-origin: top;
            will-change: max-height;
        }
        
        .sidebar-dropdown:hover .sidebar-dropdown-menu {
            max-height: 200px; /* Adjust based on content */
            opacity: 1;
        }
        
        .sidebar-dropdown:hover .dropdown-arrow {
            transform: rotate(90deg);
        }
        
        .sidebar-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem 0.6rem 1.5rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.375rem;
            margin: 0.125rem 0;
            transform: translateY(0);
            opacity: 1;
        }
        
        .sidebar-dropdown-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }
        
        .sidebar-dropdown-item.active {
            background: rgba(102, 126, 234, 0.12);
            color: white;
            font-weight: 500;
        }
        
        .sidebar-dropdown-item i {
            width: 18px;
            font-size: 0.875rem;
            color: currentColor;
            transition: transform 0.2s ease;
        }
        
        .sidebar-dropdown-item:hover i {
            transform: scale(1.1);
        }
        
        /* Badge styles */
        .badge-count {
            background: #ef4444;
            color: white;
            border-radius: 999px;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.1rem 0.4rem;
            margin-left: auto;
            min-width: 18px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }
        
        /* Smooth transitions for sidebar nav items */
        .sidebar-nav-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-nav-item:hover {
            transform: translateX(4px);
        }
        
        /* Ensure smooth hover effects */
        .sidebar-dropdown-btn, 
        .sidebar-dropdown-item,
        .sidebar-nav-item {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        /* Keep dropdown open when menu is hovered */
        .sidebar-dropdown-menu:hover {
            max-height: 200px;
            opacity: 1;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar-dropdown-menu {
                margin-left: 0.5rem;
            }
            
            .sidebar-dropdown-item {
                padding-left: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="layout-with-sidebar">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('assets/img/adsco-logo.png') }}" alt="ADSCO Logo">
                </div>
                <div class="sidebar-title">ADMIN</div>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <!-- Departments Dropdown -->
                <div class="sidebar-dropdown">
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('admin.colleges.*') || request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Departments</span>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('admin.colleges.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.colleges.*') ? 'active' : '' }}">
                            <i class="fas fa-university"></i>
                            <span>Colleges</span>
                        </a>
                        <a href="{{ route('admin.programs.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.programs.*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Programs</span>
                        </a>
                    </div>
                </div>

                <!-- Learning Materials Dropdown -->
                <div class="sidebar-dropdown">
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        <span>Learning Materials</span>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('admin.courses.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>Courses</span>
                        </a>
                        <a href="{{ route('admin.topics.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.topics.*') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>Topics</span>
                        </a>
                    </div>
                </div>

                <!-- To-Do Dropdown -->
                <div class="sidebar-dropdown">
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('admin.todo.*') || request()->routeIs('admin.quizzes.*') || request()->routeIs('admin.assignments.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>To-Do</span>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('admin.todo.index', ['type' => 'quiz']) }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.todo.quiz*') ? 'active' : '' }}">
                            <i class="fas fa-brain"></i>
                            <span>Quizzes</span>
                        </a>
                        <a href="{{ route('admin.todo.index', ['type' => 'assignment']) }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.todo.assignment*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Assignments</span>
                        </a>
                        <a href="{{ route('admin.todo.progress') }}" class="sidebar-dropdown-item {{ request()->routeIs('admin.todo.progress*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Progress</span>
                        </a>
                    </div>
                </div>

                <!-- Enrollments -->
                <a href="{{ route('admin.enrollments.index') }}" class="sidebar-nav-item {{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Enrollments</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <!-- Profile link -->
                <a href="{{ route('admin.profile.show') }}" class="sidebar-user-profile-link">
                    <div class="sidebar-user-profile">
                        <div class="sidebar-user-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->f_name }}" class="avatar-image">
                            @else
                                {{ strtoupper(substr(Auth::user()->f_name ?? 'A', 0, 1)) }}
                            @endif
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
                            
                            <div class="sidebar-user-name">{{ $user ? $user->f_name : 'Guest' }}</div>
                            <div class="sidebar-user-role">{{ $roleText }}</div>
                        </div>
                    </div>
                </a>
                
                <button class="sidebar-nav-item sidebar-logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
        }

        // Smooth dropdown animations with hardware acceleration
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.sidebar-dropdown');
            
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.sidebar-dropdown-menu');
                const btn = dropdown.querySelector('.sidebar-dropdown-btn');
                const arrow = dropdown.querySelector('.dropdown-arrow');
                
                if (!menu) return;
                
                // Set initial max-height based on content
                const setMaxHeight = () => {
                    if (menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                };
                
                // Smooth hover with RAF for performance
                let hoverTimeout;
                let isHovering = false;
                
                dropdown.addEventListener('mouseenter', function() {
                    cancelAnimationFrame(hoverTimeout);
                    isHovering = true;
                    
                    requestAnimationFrame(() => {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                        menu.style.opacity = '1';
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                    });
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    isHovering = false;
                    
                    // Check if any child is active
                    const hasActiveChild = dropdown.querySelector('.sidebar-dropdown-item.active');
                    
                    if (!hasActiveChild) {
                        hoverTimeout = requestAnimationFrame(() => {
                            if (!isHovering) {
                                menu.style.maxHeight = '0';
                                menu.style.opacity = '0';
                                if (arrow) arrow.style.transform = 'rotate(0deg)';
                            }
                        });
                    }
                });
                
                // Keep open when hovering menu
                menu.addEventListener('mouseenter', function() {
                    cancelAnimationFrame(hoverTimeout);
                    isHovering = true;
                });
                
                menu.addEventListener('mouseleave', function() {
                    isHovering = false;
                    
                    const hasActiveChild = dropdown.querySelector('.sidebar-dropdown-item.active');
                    
                    if (!hasActiveChild) {
                        hoverTimeout = requestAnimationFrame(() => {
                            if (!isHovering) {
                                menu.style.maxHeight = '0';
                                menu.style.opacity = '0';
                                if (arrow) arrow.style.transform = 'rotate(0deg)';
                            }
                        });
                    }
                });
                
                // Adjust max-height on window resize
                window.addEventListener('resize', () => {
                    if (menu.style.maxHeight !== '0px') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                });
            });
            
            // Keep dropdowns open if they have active children
            const activeDropdowns = document.querySelectorAll('.sidebar-dropdown-item.active');
            activeDropdowns.forEach(item => {
                const dropdown = item.closest('.sidebar-dropdown');
                if (dropdown) {
                    const menu = dropdown.querySelector('.sidebar-dropdown-menu');
                    const arrow = dropdown.querySelector('.dropdown-arrow');
                    if (menu) {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                        menu.style.opacity = '1';
                    }
                    if (arrow) arrow.style.transform = 'rotate(90deg)';
                }
            });
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/adsco-logo.png') }}">

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
    
    <!-- Layout CSS (includes sidebar, dropdown, badge, footer styles) -->
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    
    @stack('styles')
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
                                $user     = Auth::user();
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

        document.addEventListener('DOMContentLoaded', function () {
            const dropdowns = document.querySelectorAll('.sidebar-dropdown');

            dropdowns.forEach(dropdown => {
                const menu  = dropdown.querySelector('.sidebar-dropdown-menu');
                const arrow = dropdown.querySelector('.dropdown-arrow');

                if (!menu) return;

                let hoverTimeout;
                let isHovering = false;

                const openMenu = () => {
                    cancelAnimationFrame(hoverTimeout);
                    isHovering = true;
                    requestAnimationFrame(() => {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                        menu.style.opacity   = '1';
                        if (arrow) arrow.style.transform = 'rotate(90deg)';
                    });
                };

                const closeMenu = () => {
                    isHovering = false;
                    const hasActiveChild = dropdown.querySelector('.sidebar-dropdown-item.active');
                    if (!hasActiveChild) {
                        hoverTimeout = requestAnimationFrame(() => {
                            if (!isHovering) {
                                menu.style.maxHeight = '0';
                                menu.style.opacity   = '0';
                                if (arrow) arrow.style.transform = 'rotate(0deg)';
                            }
                        });
                    }
                };

                dropdown.addEventListener('mouseenter', openMenu);
                dropdown.addEventListener('mouseleave', closeMenu);
                menu.addEventListener('mouseenter', () => { cancelAnimationFrame(hoverTimeout); isHovering = true; });
                menu.addEventListener('mouseleave', closeMenu);

                // Resize: keep open panels correctly sized
                window.addEventListener('resize', () => {
                    if (menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '') {
                        menu.style.maxHeight = menu.scrollHeight + 'px';
                    }
                });
            });

            // Keep dropdowns open when a child link is the active page
            document.querySelectorAll('.sidebar-dropdown-item.active').forEach(item => {
                const dropdown = item.closest('.sidebar-dropdown');
                if (!dropdown) return;
                const menu  = dropdown.querySelector('.sidebar-dropdown-menu');
                const arrow = dropdown.querySelector('.dropdown-arrow');
                if (menu) {
                    menu.style.maxHeight = menu.scrollHeight + 'px';
                    menu.style.opacity   = '1';
                }
                if (arrow) arrow.style.transform = 'rotate(90deg)';
            });
        });
    </script>
</body>
</html>
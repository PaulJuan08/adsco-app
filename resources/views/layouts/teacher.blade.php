<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard')</title>
    
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
                <div class="sidebar-title">TEACHER</div>
            </div>
            
            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Learning Materials Dropdown -->
                <div class="sidebar-dropdown">
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('teacher.courses.*') || request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        <span>Learning Materials</span>
                        <i class="fas fa-chevron-right dropdown-arrow"></i>
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('teacher.courses.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
                            <i class="fas fa-book"></i>
                            <span>Courses</span>
                        </a>
                        <a href="{{ route('teacher.topics.index') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>Topics</span>
                        </a>
                    </div>
                </div>

                <!-- To-Do Dropdown -->
                <div class="sidebar-dropdown">
                    @php
                        $teacherId = Auth::id();

                        $pendingQuizzes = \App\Models\Quiz::where('created_by', $teacherId)
                            ->where('is_published', 0)
                            ->count();

                        $pendingAssignments = \App\Models\Assignment::where('created_by', $teacherId)
                            ->where('is_published', 0)
                            ->count();

                        $pendingSubmissions = \App\Models\AssignmentSubmission::whereHas('assignment', function($q) use ($teacherId) {
                                $q->where('created_by', $teacherId);
                            })
                            ->where('status', 'submitted')
                            ->count();

                        $pendingCount = $pendingQuizzes + $pendingAssignments + $pendingSubmissions;
                    @endphp
                    <div class="sidebar-dropdown-btn {{ request()->routeIs('teacher.todo.*') || request()->routeIs('teacher.quizzes.*') || request()->routeIs('teacher.assignments.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>To-Do</span>
                        @if($pendingCount > 0)
                            <span class="badge-count">{{ $pendingCount }}</span>
                        @else
                            <i class="fas fa-chevron-right dropdown-arrow"></i>
                        @endif
                    </div>
                    <div class="sidebar-dropdown-menu">
                        <a href="{{ route('teacher.todo.index', ['type' => 'quiz']) }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.todo.quiz*') ? 'active' : '' }}">
                            <i class="fas fa-brain"></i>
                            <span>Quizzes</span>
                            @if($pendingQuizzes > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingQuizzes }}</span>
                            @endif
                        </a>
                        <a href="{{ route('teacher.todo.index', ['type' => 'assignment']) }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.todo.assignment*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            <span>Assignments</span>
                            @if($pendingAssignments > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingAssignments }}</span>
                            @endif
                        </a>
                        <a href="{{ route('teacher.todo.progress') }}" class="sidebar-dropdown-item {{ request()->routeIs('teacher.todo.progress*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Progress</span>
                            @if($pendingSubmissions > 0)
                                <span class="badge-count" style="font-size:0.6rem; padding:0.05rem 0.35rem; min-width:16px;">{{ $pendingSubmissions }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Enrollments -->
                <a href="{{ route('teacher.enrollments.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.enrollments*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Enrollments</span>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <!-- Profile link -->
                <a href="{{ route('teacher.profile.show') }}" class="sidebar-user-profile-link">
                    <div class="sidebar-user-profile">
                        <div class="sidebar-user-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->f_name }}" class="avatar-image">
                            @else
                                {{ strtoupper(substr(Auth::user()->f_name ?? 'T', 0, 1)) }}
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
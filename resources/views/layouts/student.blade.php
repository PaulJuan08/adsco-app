<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    
    <!-- Layout CSS -->
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
                <a href="{{ route('student.quizzes.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.quizzes.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Quizzes</span>
                </a>
                <a href="{{ route('student.assignments.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Assignments</span>
                </a>
                <a href="{{ route('student.progress.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.progress.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>My Progress</span>
                </a>
                <a href="{{ route('student.grades.index') }}" class="sidebar-nav-item {{ request()->routeIs('student.grades.*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span>Grades</span>
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
                <!-- Profile link -->
                <a href="{{ route('student.profile.show') }}" class="sidebar-user-profile-link">
                    <div class="sidebar-user-profile">
                        <div class="sidebar-user-avatar">
                            @php
                                $avatarClass = 'default';
                                if(isset(Auth::user()->sex)) {
                                    $avatarClass = Auth::user()->sex == 'male' ? 'male' : (Auth::user()->sex == 'female' ? 'female' : 'default');
                                }
                            @endphp
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="{{ Auth::user()->f_name }}" class="avatar-image">
                            @else
                                @if(Auth::user()->sex == 'male')
                                    <i class="fas fa-mars"></i>
                                @elseif(Auth::user()->sex == 'female')
                                    <i class="fas fa-venus"></i>
                                @else
                                    {{ strtoupper(substr(Auth::user()->f_name ?? 'S', 0, 1)) }}
                                @endif
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
        // Mobile sidebar toggle functionality
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('mobile-open');
        }
    </script>
</body>
</html>
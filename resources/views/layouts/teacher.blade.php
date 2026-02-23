<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard')</title>
    
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
        /* Smooth animations for sidebar */
        .sidebar-nav-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        .sidebar-nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
            color: white;
        }
        
        .sidebar-nav-item.active {
            background: rgba(102, 126, 234, 0.15);
            color: white;
            border-left: 3px solid #667eea;
        }
        
        .sidebar-nav-item i {
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            width: 20px;
            font-size: 1rem;
        }
        
        .sidebar-nav-item:hover i {
            transform: scale(1.1);
        }
        
        /* Badge animation */
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
            display: inline-block;
            transition: transform 0.2s ease;
        }
        
        .sidebar-nav-item:hover .badge-count {
            transform: scale(1.1);
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
        
        /* Profile link hover animation */
        .sidebar-user-profile-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: block;
            border-radius: 0.5rem;
        }
        
        .sidebar-user-profile-link:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }
        
        .sidebar-user-avatar {
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-user-profile-link:hover .sidebar-user-avatar {
            transform: scale(1.05);
        }
        
        /* Logout button animation */
        .sidebar-logout-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-radius: 0.375rem !important;
        }
        
        .sidebar-logout-btn:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #ef4444 !important;
            transform: translateX(4px);
        }
        
        .sidebar-logout-btn:hover i {
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        
        @keyframes shake {
            10%, 90% {
                transform: translateX(-1px);
            }
            20%, 80% {
                transform: translateX(2px);
            }
            30%, 50%, 70% {
                transform: translateX(-2px);
            }
            40%, 60% {
                transform: translateX(2px);
            }
        }
        
        /* Smooth transitions for all interactive elements */
        .sidebar-nav-item,
        .sidebar-user-profile-link,
        .sidebar-user-avatar,
        .badge-count,
        .sidebar-logout-btn i {
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar-nav-item:hover {
                transform: translateX(2px);
            }
            
            .sidebar-user-profile-link:hover {
                transform: translateX(2px);
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
                <div class="sidebar-title">TEACHER</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('teacher.courses.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>
                
                <a href="{{ route('teacher.topics.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Topics</span>
                </a>
                
                <a href="{{ route('teacher.todo.index') }}" class="sidebar-nav-item {{ request()->routeIs('teacher.todo.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>To-Do</span>
                    @php
                        $teacherId = Auth::id();
                        
                        // Get pending items count for badge
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
                    @if($pendingCount > 0)
                        <span class="badge-count">{{ $pendingCount }}</span>
                    @endif
                </a>
            </nav>  
            
            <div class="sidebar-footer">
                <!-- Profile link -->
                <a href="{{ route('teacher.profile.show') }}" class="sidebar-user-profile-link">
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
                                    {{ strtoupper(substr(Auth::user()->f_name ?? 'T', 0, 1)) }}
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
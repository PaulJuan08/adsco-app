<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Teacher Dashboard')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --secondary: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #111827;
            --light: #f9fafb;
            --border: #e5e7eb;
            --card-bg: #ffffff;
            --sidebar-bg: #1f2937;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #374151;
            line-height: 1.6;
        }
        
        /* Layout */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #111827 100%);
            color: white;
            padding: 2rem 1.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            background: linear-gradient(to right, #ffffff, #d1d5db);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            color: #d1d5db;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
        }
        
        .logout-btn {
            margin-top: auto;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            width: 100%;
            text-align: left;
        }
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
        }
        
        /* Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .greeting h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .greeting p {
            color: var(--secondary);
            font-size: 0.9rem;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .icon-users { background: #e0e7ff; color: var(--primary); }
        .icon-pending { background: #fef3c7; color: var(--warning); }
        .icon-courses { background: #dcfce7; color: var(--success); }
        .icon-logins { background: #e0f2fe; color: #0ea5e9; }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--secondary);
            font-size: 0.875rem;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }
        
        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .view-all {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        /* Tables & Lists */
        .course-item, .due-item, .announcement-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }
        
        .course-item:last-child, .due-item:last-child, .announcement-item:last-child {
            border-bottom: none;
        }
        
        .course-icon, .due-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .course-1 { background: #e0e7ff; color: var(--primary); }
        .course-2 { background: #fce7f3; color: #db2777; }
        .course-3 { background: #dcfce7; color: var(--success); }
        
        .due-icon {
            background: #fef3c7;
            color: var(--warning);
        }
        
        .course-info, .due-info, .announcement-info {
            flex: 1;
        }
        
        .course-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .course-desc {
            color: var(--secondary);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        .course-teacher {
            font-size: 0.875rem;
            color: var(--secondary);
        }
        
        .due-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .due-date {
            font-size: 0.875rem;
            color: var(--danger);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .announcement-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .announcement-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .announcement-text {
            color: var(--secondary);
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        /* Footer */
        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            text-align: center;
            color: var(--secondary);
            font-size: 0.875rem;
        }
        
        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-primary {
            background: #e0e7ff;
            color: var(--primary);
        }
        
        .badge-success {
            background: #dcfce7;
            color: #065f46;
        }
        
        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--secondary);
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border);
        }
        
        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }
        
        .user-avatar-small {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .user-details {
            flex: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: white;
        }
        
        .user-role {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        
        /* Make sidebar-nav container flexible */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        /* Adjust the sidebar container */
        .sidebar {
            display: flex;
            flex-direction: column;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 1rem 0.5rem;
            }
            
            .logo-text, .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">T</div>
                <div class="logo-text">TEACHER</div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('teacher.courses.index') }}" class="nav-item {{ request()->routeIs('teacher.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>My Courses</span>
                </a>
                <a href="{{ route('teacher.topics.index') }}" class="nav-item {{ request()->routeIs('teacher.topics.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Topics</span>
                </a>
                <a href="{{ route('teacher.quizzes.index') }}" class="nav-item {{ request()->routeIs('teacher.quizzes.*') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>Quizzes</span>
                </a>
            </nav>  
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar-small">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        @php
                            $roleMapping = [1 => 'Admin', 2 => 'Registrar', 3 => 'Teacher', 4 => 'Student'];
                        @endphp
                        <div class="user-name">{{ Auth::user()->f_name ?? 'Guest' }}</div>
                        <div class="user-role">{{ Auth::check() ? $roleMapping[Auth::user()->role] ?? 'User' : 'Guest' }}</div>
                    </div>
                </div>
                
                <button class="nav-item logout-btn" onclick="document.getElementById('logout-form').submit()">
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
    
    @stack('scripts')
</body>
</html>
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar-large">
                    {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">Welcome back, {{ auth()->user()->f_name }}</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-calendar-day"></i> {{ now()->format('l, F d, Y') }}
                        @if($pendingApprovals > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $pendingApprovals }} approval{{ $pendingApprovals > 1 ? 's' : '' }} pending</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($pendingApprovals > 0)
            <div class="header-alert">
                <div class="alert-badge">
                    <i class="fas fa-bell"></i>
                    <span class="badge-count">{{ $pendingApprovals }}</span>
                </div>
                <div class="alert-text">
                    <div class="alert-title">Action Required</div>
                    <div class="alert-subtitle">{{ $pendingApprovals }} user{{ $pendingApprovals > 1 ? 's' : '' }} awaiting approval</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ number_format($totalUsers) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-arrow-up"></i> All registered users
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}" class="stat-link">
                    View all users <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($pendingApprovals) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-exclamation-circle"></i> Require attention
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.users.index') }}?status=pending" class="stat-link">
                    Review now <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-check-circle"></i> All active courses
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.courses.index') }}" class="stat-link">
                    Manage courses <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="stat-card stat-info">
            <div class="stat-content">
                <div class="stat-info">
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics) }}</div>
                    <div class="stat-meta">
                        <i class="fas fa-book-open"></i> Learning materials
                    </div>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.topics.index') }}" class="stat-link">
                    Browse topics <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-user-clock card-icon"></i>
                        <h2 class="card-title-modern">Pending Approvals</h2>
                    </div>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="view-all-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body-modern">
                    @if($pendingUsers->isEmpty())
                        <div class="empty-state-modern">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="empty-title">All Clear!</h3>
                            <p class="empty-text">No pending approvals at the moment</p>
                            <p class="empty-subtext">All users are approved and active</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($pendingUsers->take(3) as $user)
                            <div class="list-item">
                                <div class="item-avatar">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div class="item-details">
                                    <div class="item-title">{{ $user->f_name }} {{ $user->l_name }}</div>
                                    <div class="item-subtitle">{{ $user->email }}</div>
                                    <div class="item-meta">
                                        @php
                                            $roleName = match($user->role) {
                                                1 => 'Admin',
                                                2 => 'Registrar',
                                                3 => 'Teacher',
                                                4 => 'Student',
                                                default => 'Unknown'
                                            };
                                        @endphp
                                        <span class="role-badge role-{{ strtolower($roleName) }}">
                                            <i class="fas fa-user"></i> {{ $roleName }}
                                        </span>
                                        <span class="time-badge">
                                            <i class="fas fa-clock"></i> {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-approve" onclick="return confirm('Are you sure you want to approve this user?')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Content Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-stream card-icon"></i>
                        <h2 class="card-title-modern">Recent Content</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    @php
                        $recentTopics = App\Models\Topic::latest()->take(3)->get();
                        $recentAssignments = App\Models\Assignment::latest()->take(3)->get();
                        $recentQuizzes = App\Models\Quiz::latest()->take(3)->get();
                    @endphp
                    
                    @if($recentTopics->isEmpty() && $recentAssignments->isEmpty() && $recentQuizzes->isEmpty())
                        <div class="empty-state-modern">
                            <div class="empty-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="empty-title">No Content Yet</h3>
                            <p class="empty-text">Start creating learning materials</p>
                            <p class="empty-subtext">Add topics, assignments, or quizzes to get started</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($recentTopics as $topic)
                            <div class="list-item content-item">
                                <div class="item-icon icon-topic">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-title">{{ $topic->title }}</div>
                                    <div class="item-subtitle">{{ Str::limit($topic->description, 60) }}</div>
                                    <div class="item-meta">
                                        <span class="type-badge type-topic">
                                            <i class="fas fa-book"></i> Topic
                                        </span>
                                        <span class="time-badge">
                                            <i class="fas fa-clock"></i> {{ $topic->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                                       class="btn-view btn-view-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentAssignments as $assignment)
                            <div class="list-item content-item">
                                <div class="item-icon icon-assignment">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-title">{{ $assignment->title }}</div>
                                    <div class="item-subtitle">
                                        @if(method_exists($assignment, 'course') && $assignment->course)
                                            {{ $assignment->course->title ?? 'Assignment' }}
                                        @else
                                            Assignment
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="type-badge type-assignment">
                                            <i class="fas fa-tasks"></i> Assignment
                                        </span>
                                        <span class="time-badge">
                                            <i class="fas fa-clock"></i> {{ $assignment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('admin.assignments.show', Crypt::encrypt($assignment->id)) }}" 
                                       class="btn-view btn-view-success">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentQuizzes as $quiz)
                            <div class="list-item content-item">
                                <div class="item-icon icon-quiz">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="item-details">
                                    <div class="item-title">{{ $quiz->title }}</div>
                                    <div class="item-subtitle">
                                        @if(method_exists($quiz, 'course') && $quiz->course)
                                            {{ $quiz->course->title ?? 'Quiz' }}
                                        @else
                                            Quiz
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="type-badge type-quiz">
                                            <i class="fas fa-question-circle"></i> Quiz
                                        </span>
                                        <span class="time-badge">
                                            <i class="fas fa-clock"></i> {{ $quiz->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                       class="btn-view btn-view-warning">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-bolt card-icon"></i>
                        <h2 class="card-title-modern">Quick Actions</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create Topic</div>
                                <div class="action-subtitle">Add new learning material</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.create') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create Quiz</div>
                                <div class="action-subtitle">Add new quiz/test</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.users.create') }}" class="action-card action-info">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Add New User</div>
                                <div class="action-subtitle">Register staff or student</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('admin.courses.create') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-book-medical"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Create Course</div>
                                <div class="action-subtitle">Setup new academic course</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Overview Card -->
            <div class="dashboard-card">
                <div class="card-header-modern">
                    <div class="card-title-group">
                        <i class="fas fa-chart-pie card-icon"></i>
                        <h2 class="card-title-modern">System Overview</h2>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="overview-list">
                        <div class="overview-item">
                            <div class="overview-icon icon-quiz">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ number_format($totalQuizzes) }}</div>
                                <div class="overview-label">Total Quizzes</div>
                            </div>
                            <div class="overview-trend trend-up">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        
                        <div class="overview-item">
                            <div class="overview-icon icon-enrollment">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ number_format($activeEnrollments) }}</div>
                                <div class="overview-label">Active Enrollments</div>
                            </div>
                            <div class="overview-trend trend-up">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                        </div>
                        
                        <div class="overview-item">
                            <div class="overview-icon icon-login">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="overview-details">
                                <div class="overview-value">{{ number_format($todayLogins) }}</div>
                                <div class="overview-label">Today's Logins</div>
                            </div>
                            <div class="overview-trend trend-neutral">
                                <i class="fas fa-minus"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-content">
            <p class="footer-text">© 2024 School Management System. All rights reserved.</p>
            <p class="footer-meta">
                <span><i class="fas fa-code-branch"></i> v1.0.0</span>
                <span class="separator">•</span>
                <span><i class="fas fa-calendar"></i> Updated {{ now()->format('M d, Y') }}</span>
            </p>
        </div>
    </footer>
@endsection

@push('styles')
<style>
    /* Color Variables */
    :root {
        --primary: #4f46e5;
        --primary-light: #eef2ff;
        --primary-dark: #3730a3;
        
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #059669;
        
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        
        --info: #06b6d4;
        --info-light: #cffafe;
        --info-dark: #0891b2;
        
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #dc2626;
        
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
    }
    
    /* Dashboard Header */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-lg);
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }
    
    .user-greeting {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    
    .user-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.3);
        flex-shrink: 0;
    }
    
    .greeting-text {
        color: white;
    }
    
    .welcome-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: white;
    }
    
    .welcome-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .separator {
        opacity: 0.5;
    }
    
    .pending-notice {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .header-alert {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .alert-badge {
        position: relative;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }
    
    .badge-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        border: 2px solid var(--primary);
    }
    
    .alert-text {
        color: white;
    }
    
    .alert-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .alert-subtitle {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-200);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-content {
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: var(--gray-600);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-meta {
        font-size: 0.875rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    
    .stat-primary .stat-icon-wrapper {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .stat-warning .stat-icon-wrapper {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .stat-success .stat-icon-wrapper {
        background: var(--success-light);
        color: var(--success);
    }
    
    .stat-info .stat-icon-wrapper {
        background: var(--info-light);
        color: var(--info);
    }
    
    .stat-footer {
        background: var(--gray-50);
        padding: 0.75rem 1.5rem;
        border-top: 1px solid var(--gray-200);
    }
    
    .stat-link {
        color: var(--gray-700);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .stat-link:hover {
        color: var(--primary);
        gap: 0.75rem;
    }
    
    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 1200px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Dashboard Cards */
    .dashboard-card {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }
    
    .card-header-modern {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .card-icon {
        font-size: 1.25rem;
        color: var(--primary);
    }
    
    .card-title-modern {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }
    
    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }
    
    .card-body-modern {
        padding: 1.5rem;
    }
    
    /* Empty States */
    .empty-state-modern {
        text-align: center;
        padding: 3rem 1.5rem;
    }
    
    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--gray-400);
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 0.5rem 0;
    }
    
    .empty-text {
        font-size: 1rem;
        color: var(--gray-600);
        margin: 0 0 0.25rem 0;
    }
    
    .empty-subtext {
        font-size: 0.875rem;
        color: var(--gray-500);
        margin: 0;
    }
    
    /* List Items */
    .items-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }
    
    .list-item:hover {
        background: white;
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
    }
    
    .item-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.125rem;
        flex-shrink: 0;
    }
    
    .item-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .icon-topic {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .icon-assignment {
        background: var(--success-light);
        color: var(--success);
    }
    
    .icon-quiz {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .item-details {
        flex: 1;
        min-width: 0;
    }
    
    .item-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 1rem;
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .item-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .role-badge,
    .type-badge,
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .role-admin {
        background: var(--danger-light);
        color: var(--danger-dark);
    }
    
    .role-registrar {
        background: var(--info-light);
        color: var(--info-dark);
    }
    
    .role-teacher {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .role-student {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .type-topic {
        background: var(--primary-light);
        color: var(--primary-dark);
    }
    
    .type-assignment {
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    .type-quiz {
        background: var(--warning-light);
        color: var(--warning-dark);
    }
    
    .time-badge {
        background: var(--gray-100);
        color: var(--gray-700);
    }
    
    .item-actions {
        flex-shrink: 0;
    }
    
    .btn-approve {
        background: var(--success);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .btn-approve:hover {
        background: var(--success-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-view {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid;
    }
    
    .btn-view:hover {
        transform: scale(1.1);
    }
    
    .btn-view-primary {
        background: var(--primary-light);
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-view-success {
        background: var(--success-light);
        color: var(--success);
        border-color: var(--success);
    }
    
    .btn-view-warning {
        background: var(--warning-light);
        color: var(--warning);
        border-color: var(--warning);
    }
    
    /* Quick Actions */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: all 0.2s ease;
        border: 2px solid;
    }
    
    .action-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow-md);
    }
    
    .action-primary {
        background: var(--primary-light);
        border-color: var(--primary);
    }
    
    .action-primary:hover {
        background: var(--primary);
    }
    
    .action-primary:hover .action-title,
    .action-primary:hover .action-subtitle,
    .action-primary:hover .action-icon,
    .action-primary:hover .action-arrow {
        color: white;
    }
    
    .action-success {
        background: var(--success-light);
        border-color: var(--success);
    }
    
    .action-success:hover {
        background: var(--success);
    }
    
    .action-success:hover .action-title,
    .action-success:hover .action-subtitle,
    .action-success:hover .action-icon,
    .action-success:hover .action-arrow {
        color: white;
    }
    
    .action-warning {
        background: var(--warning-light);
        border-color: var(--warning);
    }
    
    .action-warning:hover {
        background: var(--warning);
    }
    
    .action-warning:hover .action-title,
    .action-warning:hover .action-subtitle,
    .action-warning:hover .action-icon,
    .action-warning:hover .action-arrow {
        color: white;
    }
    
    .action-info {
        background: var(--info-light);
        border-color: var(--info);
    }
    
    .action-info:hover {
        background: var(--info);
    }
    
    .action-info:hover .action-title,
    .action-info:hover .action-subtitle,
    .action-info:hover .action-icon,
    .action-info:hover .action-arrow {
        color: white;
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-icon {
        color: var(--primary);
    }
    
    .action-success .action-icon {
        color: var(--success);
    }
    
    .action-warning .action-icon {
        color: var(--warning);
    }
    
    .action-info .action-icon {
        color: var(--info);
    }
    
    .action-content {
        flex: 1;
    }
    
    .action-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-title {
        color: var(--primary-dark);
    }
    
    .action-success .action-title {
        color: var(--success-dark);
    }
    
    .action-warning .action-title {
        color: var(--warning-dark);
    }
    
    .action-info .action-title {
        color: var(--info-dark);
    }
    
    .action-subtitle {
        font-size: 0.875rem;
        color: var(--gray-600);
        transition: all 0.2s ease;
    }
    
    .action-arrow {
        font-size: 1.125rem;
        transition: all 0.2s ease;
    }
    
    .action-primary .action-arrow {
        color: var(--primary);
    }
    
    .action-success .action-arrow {
        color: var(--success);
    }
    
    .action-warning .action-arrow {
        color: var(--warning);
    }
    
    .action-info .action-arrow {
        color: var(--info);
    }
    
    /* System Overview */
    .overview-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .overview-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
        transition: all 0.2s ease;
    }
    
    .overview-item:hover {
        background: white;
        box-shadow: var(--shadow);
    }
    
    .overview-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    
    .overview-icon.icon-quiz {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .overview-icon.icon-enrollment {
        background: var(--success-light);
        color: var(--success);
    }
    
    .overview-icon.icon-login {
        background: var(--info-light);
        color: var(--info);
    }
    
    .overview-details {
        flex: 1;
    }
    
    .overview-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .overview-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }
    
    .overview-trend {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }
    
    .trend-up {
        background: var(--success-light);
        color: var(--success);
    }
    
    .trend-down {
        background: var(--danger-light);
        color: var(--danger);
    }
    
    .trend-neutral {
        background: var(--gray-100);
        color: var(--gray-500);
    }
    
    /* Footer */
    .dashboard-footer {
        background: white;
        border-top: 1px solid var(--gray-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: var(--shadow-sm);
    }
    
    .footer-content {
        text-align: center;
    }
    
    .footer-text {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin: 0 0 0.5rem 0;
    }
    
    .footer-meta {
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .user-avatar-large {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.5rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-number {
            font-size: 1.875rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .footer-meta {
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .separator {
            display: none;
        }
    }
</style>
@endpush
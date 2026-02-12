@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
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
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('admin.users.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number">{{ number_format($totalUsers) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-link">
                View all users <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.users.index') }}?status=pending" class="stat-card stat-card-warning clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Approvals</div>
                    <div class="stat-number">{{ number_format($pendingApprovals) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                Review now <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.courses.index') }}" class="stat-card stat-card-success clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-link">
                Manage courses <i class="fas fa-arrow-right"></i>
            </div>
        </a>
        
        <a href="{{ route('admin.topics.index') }}" class="stat-card stat-card-info clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="stat-link">
                Browse topics <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-clock" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Pending Approvals
                    </h2>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($pendingUsers->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="empty-title">All Clear!</h3>
                            <p class="empty-text">No pending approvals at the moment. All users are approved and active.</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($pendingUsers->take(5) as $user)
                            <div class="list-item">
                                <div class="item-avatar">
                                    {{ strtoupper(substr($user->f_name, 0, 1)) }}
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                                    <div class="item-details">{{ $user->email }}</div>
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
                                        <span class="item-badge badge-{{ strtolower($roleName) }}">
                                            <i class="fas fa-user"></i> {{ $roleName }}
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Are you sure you want to approve this user?')">
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-stream" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Recent Content
                    </h2>
                </div>
                
                <div class="card-body">
                    @php
                        $recentTopics = App\Models\Topic::latest()->take(3)->get();
                        $recentAssignments = App\Models\Assignment::latest()->take(3)->get();
                        $recentQuizzes = App\Models\Quiz::latest()->take(3)->get();
                    @endphp
                    
                    @if($recentTopics->isEmpty() && $recentAssignments->isEmpty() && $recentQuizzes->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3 class="empty-title">No Content Yet</h3>
                            <p class="empty-text">Start creating learning materials. Add topics, assignments, or quizzes to get started.</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($recentTopics as $topic)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $topic->title }}</div>
                                    <div class="item-details">{{ Str::limit($topic->description, 60) }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            <i class="fas fa-book"></i> Topic
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $topic->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentAssignments as $assignment)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--success-light), var(--success));">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $assignment->title }}</div>
                                    <div class="item-details">
                                        @if(method_exists($assignment, 'course') && $assignment->course)
                                            {{ $assignment->course->title ?? 'Assignment' }}
                                        @else
                                            Assignment
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-success">
                                            <i class="fas fa-tasks"></i> Assignment
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $assignment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.assignments.show', Crypt::encrypt($assignment->id)) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($recentQuizzes as $quiz)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $quiz->title }}</div>
                                    <div class="item-details">
                                        @if(method_exists($quiz, 'course') && $quiz->course)
                                            {{ $quiz->course->title ?? 'Quiz' }}
                                        @else
                                            Quiz
                                        @endif
                                    </div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-warning">
                                            <i class="fas fa-question-circle"></i> Quiz
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-clock"></i> {{ $quiz->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i> View
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
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Actions
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-title">Create Topic</div>
                            <div class="action-subtitle">Add new learning material</div>
                        </a>
                        
                        <a href="{{ route('admin.quizzes.create') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-title">Create Quiz</div>
                            <div class="action-subtitle">Add new quiz/test</div>
                        </a>
                        
                        <a href="{{ route('admin.users.create') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-title">Add New User</div>
                            <div class="action-subtitle">Register staff or student</div>
                        </a>
                        
                        <a href="{{ route('admin.courses.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book-medical"></i>
                            </div>
                            <div class="action-title">Create Course</div>
                            <div class="action-subtitle">Setup new academic course</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Overview Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        System Overview
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <a href="{{ route('admin.quizzes.index') }}" class="list-item clickable-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Quizzes</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format($totalQuizzes) }}</div>
                        </a>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Today's Logins</div>
                            </div>
                            <div class="stat-number" style="font-size: 1.5rem;">{{ number_format($todayLogins) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Version 1.0.0 • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('styles')
<style>
/* Additional admin-specific badge styles */
.badge-admin {
    background: var(--danger-light);
    color: var(--danger-dark);
}

.badge-registrar {
    background: var(--info-light);
    color: var(--info-dark);
}

.badge-teacher {
    background: var(--success-light);
    color: var(--success-dark);
}

.badge-student {
    background: var(--primary-light);
    color: var(--primary-dark);
}

/* Compact Stats Cards */
.stats-grid-compact {
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.stats-grid-compact .stat-card {
    padding: 1rem;
    min-height: auto;
}

.stats-grid-compact .stat-header {
    margin-bottom: 0.75rem;
}

.stats-grid-compact .stat-label {
    font-size: var(--font-size-sm);
    margin-bottom: 0.25rem;
}

.stats-grid-compact .stat-number {
    font-size: 1.75rem;
    font-weight: var(--font-bold);
}

.stats-grid-compact .stat-icon {
    font-size: 1.5rem;
    opacity: 0.8;
}

.stats-grid-compact .stat-link {
    padding: 0.5rem;
    font-size: var(--font-size-xs);
}

/* Clickable Cards */
.clickable-card {
    display: block;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.clickable-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.clickable-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: currentColor;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.clickable-card:hover::after {
    opacity: 0.05;
}

/* Clickable items in lists */
.clickable-item {
    display: block;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: all 0.2s ease;
}

.clickable-item:hover {
    background: var(--gray-50);
    transform: translateX(4px);
}

/* Ensure links within clickable cards don't interfere */
.clickable-card a {
    position: relative;
    z-index: 1;
}

/* Make stat cards look interactive */
.stat-card {
    transition: all 0.3s ease;
}

/* Responsive adjustments for compact stats */
@media (max-width: 768px) {
    .stats-grid-compact {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .stats-grid-compact .stat-card {
        padding: 0.75rem;
    }
    
    .clickable-card:hover {
        transform: translateY(-2px);
    }
}

@media (max-width: 480px) {
    .stats-grid-compact {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
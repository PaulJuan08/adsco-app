@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Header -->
    <div class="top-header">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
            </div>
            <div class="greeting">
                <h1>Welcome back, {{ auth()->user()->f_name }}</h1>
                <p>You have {{ $pendingApprovals }} approvals pending. Stay on top of things!</p>
            </div>
        </div>
        <div class="header-actions">
            <span class="badge badge-warning">
                <i class="fas fa-bell me-1"></i> {{ $pendingApprovals }} pending
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-icon icon-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-arrow-up text-success me-1"></i> All registered users
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $pendingApprovals }}</div>
                    <div class="stat-label">Pending Approvals</div>
                </div>
                <div class="stat-icon icon-pending">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-exclamation-circle text-warning me-1"></i> Require attention
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalCourses }}</div>
                    <div class="stat-label">Total Courses</div>
                </div>
                <div class="stat-icon icon-courses">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-check-circle text-success me-1"></i> All active courses
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalTopics }}</div>
                    <div class="stat-label">Total Topics</div>
                </div>
                <div class="stat-icon icon-topics">
                    <i class="fas fa-chalkboard"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-book-open text-info me-1"></i> Learning materials
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- Pending Approvals Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Pending Approvals</h2>
                    <a href="{{ route('admin.users.index') }}?status=pending" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($pendingUsers->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending approvals</p>
                        <p class="text-sm">All users are approved and active</p>
                    </div>
                @else
                    @foreach($pendingUsers->take(3) as $user)
                    <div class="course-item">
                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $user->f_name }} {{ $user->l_name }}</div>
                            <div class="course-desc">{{ $user->email }}</div>
                            <div class="course-teacher">
                                @php
                                    $roleName = match($user->role) {
                                        1 => 'Admin',
                                        2 => 'Registrar',
                                        3 => 'Teacher',
                                        4 => 'Student',
                                        default => 'Unknown'
                                    };
                                @endphp
                                <span class="badge badge-primary">{{ $roleName }}</span>
                                <span class="ms-2">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div>
                            <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this user?')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Recent Content Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Content</h2>
                </div>
                
                @php
                    // FIXED: Removed ->with('course') since topics don't have course relationship
                    $recentTopics = App\Models\Topic::latest()->take(3)->get();
                    // Check if assignments/quizzes have course relationship - remove if they don't
                    $recentAssignments = App\Models\Assignment::latest()->take(3)->get();
                    $recentQuizzes = App\Models\Quiz::latest()->take(3)->get();
                @endphp
                
                @if($recentTopics->isEmpty() && $recentAssignments->isEmpty() && $recentQuizzes->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>No recent content</p>
                        <p class="text-sm">Create topics, assignments, or quizzes</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentTopics as $topic)
                        <div class="course-item" style="border-left: 3px solid var(--primary);">
                            <div class="course-icon" style="background: var(--primary-light); color: var(--primary);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="course-info">
                                <div class="course-name">{{ $topic->title }}</div>
                                {{-- REMOVED course reference since topics don't have courses --}}
                                <div class="course-desc">{{ Str::limit($topic->description, 50) }}</div>
                                <div class="course-teacher">
                                    <span class="badge badge-primary">
                                        Topic
                                    </span>
                                    <span class="ms-2">{{ $topic->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                        
                        @foreach($recentAssignments as $assignment)
                        <div class="course-item" style="border-left: 3px solid var(--success);">
                            <div class="course-icon" style="background: var(--success-light); color: var(--success);">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="course-info">
                                <div class="course-name">{{ $assignment->title }}</div>
                                {{-- FIX: Check if assignment has course relationship --}}
                                <div class="course-desc">
                                    @if(method_exists($assignment, 'course') && $assignment->course)
                                        {{ $assignment->course->title ?? 'Course' }}
                                    @else
                                        Assignment Description
                                    @endif
                                </div>
                                <div class="course-teacher">
                                    <span class="badge badge-success">
                                        Assignment
                                    </span>
                                    <span class="ms-2">{{ $assignment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.assignments.show', Crypt::encrypt($assignment->id)) }}" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                        
                        @foreach($recentQuizzes as $quiz)
                        <div class="course-item" style="border-left: 3px solid var(--warning);">
                            <div class="course-icon" style="background: var(--warning-light); color: var(--warning);">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="course-info">
                                <div class="course-name">{{ $quiz->title }}</div>
                                {{-- FIX: Check if quiz has course relationship --}}
                                <div class="course-desc">
                                    @if(method_exists($quiz, 'course') && $quiz->course)
                                        {{ $quiz->course->title ?? 'Course' }}
                                    @else
                                        Quiz Description
                                    @endif
                                </div>
                                <div class="course-teacher">
                                    <span class="badge badge-warning">
                                        Quiz
                                    </span>
                                    <span class="ms-2">{{ $quiz->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.quizzes.show', Crypt::encrypt($quiz->id)) }}" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Quick Actions</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Topics -->
                    <a href="{{ route('admin.topics.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-1">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Topic</div>
                            <div class="course-desc">Add new learning material</div>
                        </div>
                    </a>
                    
                    <!-- Assignments -->
                    <a href="{{ route('admin.assignments.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-2">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Assignment</div>
                            <div class="course-desc">Add new assignment task</div>
                        </div>
                    </a>
                    
                    <!-- Quizzes -->
                    <a href="{{ route('admin.quizzes.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-3">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Quiz</div>
                            <div class="course-desc">Add new quiz/test</div>
                        </div>
                    </a>
                    
                    <!-- Users -->
                    <a href="{{ route('admin.users.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-4">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Add New User</div>
                            <div class="course-desc">Register staff or student</div>
                        </div>
                    </a>
                    
                    <!-- Courses -->
                    <a href="{{ route('admin.courses.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-5">
                            <i class="fas fa-book-medical"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Course</div>
                            <div class="course-desc">Setup new academic course</div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- System Overview Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">System Overview</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $totalAssignments }}</div>
                            <div class="text-sm text-secondary">Total Assignments</div>
                        </div>
                        <div class="stat-icon icon-courses" style="width: 40px; height: 40px;">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $totalQuizzes }}</div>
                            <div class="text-sm text-secondary">Total Quizzes</div>
                        </div>
                        <div class="stat-icon icon-courses" style="width: 40px; height: 40px;">
                            <i class="fas fa-question-circle"></i>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $activeEnrollments }}</div>
                            <div class="text-sm text-secondary">Active Enrollments</div>
                        </div>
                        <div class="stat-icon icon-users" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">{{ $todayLogins }}</div>
                            <div class="text-sm text-secondary">Today's Logins</div>
                        </div>
                        <div class="stat-icon icon-users" style="width: 40px; height: 40px;">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 School Management System. All rights reserved.</p>
        <p class="text-sm mt-1">v1.0.0 • Last updated: {{ now()->format('M d, Y') }}</p>
    </footer>
@endsection

@push('styles')
<style>
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    .flex {
        display: flex;
    }
    
    .justify-between {
        justify-content: space-between;
    }
    
    .items-center {
        align-items: center;
    }
    
    .font-semibold {
        font-weight: 600;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb;
    }
    
    .rounded-lg {
        border-radius: 8px;
    }
    
    .p-3 {
        padding: 0.75rem;
    }
    
    .text-sm {
        font-size: 0.875rem;
    }
    
    :root {
        --primary-light: #e3f2fd;
        --success-light: #d1e7dd;
        --warning-light: #fff3cd;
        --info-light: #cff4fc;
    }
    
    .course-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 10px;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .course-icon.course-1 {
        background: var(--primary-light);
        color: var(--primary);
    }
    
    .course-icon.course-2 {
        background: var(--success-light);
        color: var(--success);
    }
    
    .course-icon.course-3 {
        background: var(--warning-light);
        color: var(--warning);
    }
    
    .course-icon.course-4 {
        background: var(--info-light);
        color: var(--info);
    }
    
    .course-icon.course-5 {
        background: #f3e8ff;
        color: #8b5cf6;
    }
    
    .icon-topics {
        background: var(--primary-light);
        color: var(--primary);
    }
</style>
@endpush
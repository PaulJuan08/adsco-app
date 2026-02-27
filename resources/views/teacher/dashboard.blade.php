@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

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
                    <h1 class="welcome-title">Welcome, {{ auth()->user()->f_name }}!</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-chalkboard-teacher"></i> Teaching {{ $myCourses->count() }} courses
                        <span class="separator">•</span>
                        <i class="fas fa-users"></i> {{ $totalStudents }} students
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">My Courses</div>
                    <div class="stat-number">{{ $myCourses->count() }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="stat-link">
                View courses <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ $totalStudents }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <span class="stat-link" style="cursor: default;">
                <i class="fas fa-user-graduate"></i> Across all courses
            </span>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Today's Date</div>
                    <div class="stat-number" style="font-size: 1.5rem;">{{ now()->format('M d') }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <span class="stat-link" style="cursor: default;">
                <i class="fas fa-clock"></i> {{ now()->format('l') }}
            </span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- My Courses Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-book" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        My Courses
                    </h2>
                    <a href="{{ route('teacher.courses.index') }}" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($myCourses->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h3 class="empty-title">No Courses Assigned</h3>
                            <p class="empty-text">Contact administrator for course assignments</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($myCourses->take(5) as $course)
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius);">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $course->course_name ?? $course->title }}</div>
                                    <div class="item-details">{{ $course->course_code }} • {{ $course->credits ?? 0 }} credits</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            {{ $course->schedule ?? 'Schedule TBD' }}
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-users"></i> {{ $course->enrollments_count ?? 0 }} students
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Enrollments Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-user-plus" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Recent Enrollments
                    </h2>
                    <a href="{{ route('teacher.enrollments.index') }}" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($recentEnrollments->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <p class="empty-text">No recent enrollments</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($recentEnrollments as $enrollment)
                            <div class="list-item">
                                <div class="item-info">
                                    <div class="item-name">
                                        {{ $enrollment->student->f_name ?? 'Student' }} enrolled in {{ $enrollment->course->title ?? 'Course' }}
                                    </div>
                                    <div class="item-details">
                                        <i class="fas fa-clock"></i> {{ $enrollment->created_at->diffForHumans() }}
                                    </div>
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
                        <a href="{{ route('teacher.topics.create') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="action-title">Create Topic</div>
                            <div class="action-subtitle">Add learning material</div>
                        </a>
                        
                        <a href="{{ route('teacher.quizzes.create') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-title">Create Quiz</div>
                            <div class="action-subtitle">Add new quiz/test</div>
                        </a>
                        
                        @if(Route::has('teacher.grades.index'))
                        <a href="{{ route('teacher.grades.index') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="action-title">Enter Grades</div>
                            <div class="action-subtitle">Submit student grades</div>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Upcoming Deadlines
                    </h2>
                </div>
                
                <div class="card-body">
                    @if($upcomingAssignments->isEmpty() && $upcomingQuizzes->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <p class="empty-text">No upcoming deadlines</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($upcomingAssignments as $assignment)
                            <div class="list-item">
                                <div class="item-info">
                                    <div class="item-name">{{ $assignment->title }}</div>
                                    <div class="item-details">{{ $assignment->course->title ?? 'No Course' }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-warning">
                                            <i class="fas fa-tasks"></i> Assignment
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-calendar"></i> Due: {{ $assignment->due_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @foreach($upcomingQuizzes as $quiz)
                            <div class="list-item">
                                <div class="item-info">
                                    <div class="item-name">{{ $quiz->title }}</div>
                                    <div class="item-details">{{ Str::limit($quiz->description, 50) }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            <i class="fas fa-question-circle"></i> Quiz
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-calendar"></i> Until: {{ $quiz->available_until->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© 2024 School Management System. Teacher Portal</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time' }}
        </p>
    </footer>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
.separator {
    opacity: 0.5;
    margin: 0 0.5rem;
}
</style>
@endpush
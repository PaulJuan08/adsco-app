@extends('layouts.student')

@section('title', 'Student Dashboard')

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
                    <h1 class="welcome-title">Welcome back, {{ auth()->user()->f_name }}!</h1>
                    <p class="welcome-subtitle">
                        @php
                            $completionRate = ($stats['total_topics'] ?? 0) > 0 
                                ? round((($stats['completed_topics'] ?? 0) / $stats['total_topics']) * 100)
                                : 0;
                        @endphp
                        <i class="fas fa-book"></i> {{ $stats['total_courses'] ?? 0 }} courses
                        <span class="separator">•</span>
                        <i class="fas fa-check-circle"></i> {{ $completionRate }}% complete
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
                    <div class="stat-label">Enrolled Courses</div>
                    <div class="stat-number">{{ $stats['total_courses'] ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <a href="{{ route('student.courses.index') }}" class="stat-link">
                View courses <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-number">{{ $stats['completed_courses'] ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <span class="stat-link" style="cursor: default;">
                <i class="fas fa-graduation-cap"></i> {{ $stats['completed_courses'] ?? 0 }}/{{ $stats['total_courses'] ?? 0 }} courses
            </span>
        </div>

        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Progress</div>
                    <div class="stat-number">{{ $completionRate }}%</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <span class="stat-link" style="cursor: default;">
                <i class="fas fa-list-check"></i> {{ $stats['completed_topics'] ?? 0 }}/{{ $stats['total_topics'] ?? 0 }} topics
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
                    <a href="{{ route('student.courses.index') }}" class="stat-link">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    @if($enrolledCourses->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h3 class="empty-title">No Courses Enrolled</h3>
                            <p class="empty-text">Browse available courses to get started</p>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($enrolledCourses as $enrollment)
                            @php
                                $course = $enrollment->course;
                                $encryptedId = Crypt::encrypt($course->id);
                                // Use the progress data we calculated in the controller
                                $progressPercentage = $enrollment->progress ?? 0;
                                $completedTopics = $course->completed_topics ?? 0;
                                $totalTopics = $course->total_topics ?? $course->topics_count ?? 0;
                            @endphp
                            
                            <div class="list-item">
                                <div class="item-avatar" style="border-radius: var(--radius);">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $course->title }}</div>
                                    <div class="item-details">{{ $course->course_code }} • {{ $course->teacher->f_name ?? 'Instructor' }}</div>
                                    <div class="item-meta">
                                        <span class="item-badge badge-primary">
                                            {{ $totalTopics }} topics
                                        </span>
                                        <span class="item-badge badge-secondary">
                                            {{ $completedTopics }}/{{ $totalTopics }} completed
                                        </span>
                                    </div>
                                    <!-- Progress Bar -->
                                    <div style="margin-top: 0.5rem;">
                                        <div style="height: 6px; background: var(--gray-200); border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; background: var(--primary); width: {{ $progressPercentage }}%; transition: width 0.3s;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('student.courses.show', Crypt::encrypt($course->id)) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Available Courses Card -->
            @if(!$availableCourses->isEmpty())
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-plus-circle" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Available Courses
                    </h2>
                    <a href="{{ route('student.courses.index') }}" class="stat-link">
                        Browse all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($availableCourses->take(3) as $course)
                        <div class="list-item">
                            <div class="item-avatar" style="border-radius: var(--radius); background: var(--info-light); color: var(--info);">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $course->title }}</div>
                                <div class="item-details">{{ $course->course_code }} • {{ $course->teacher->f_name ?? 'Instructor' }}</div>
                                <div class="item-meta">
                                    <span class="item-badge badge-info">
                                        {{ $course->credits ?? 0 }} credits
                                    </span>
                                    <span class="item-badge badge-secondary">
                                        {{ $course->topics_count ?? 0 }} topics
                                    </span>
                                </div>
                            </div>
                            <div>
                                <form action="{{ route('student.courses.enroll', Crypt::encrypt($course->id)) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> Enroll
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Progress Summary Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-line" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Progress Summary
                    </h2>
                </div>
                
                <div class="card-body">
                    <!-- Overall Progress -->
                    <div style="padding: 1rem; background: var(--gray-50); border-radius: var(--radius); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-size: var(--font-size-sm); font-weight: 600; color: var(--gray-600);">Overall Progress</span>
                            <span style="font-size: var(--font-size-lg); font-weight: 700; color: var(--primary);">{{ $completionRate }}%</span>
                        </div>
                        <div style="height: 10px; background: var(--gray-200); border-radius: 5px; overflow: hidden;">
                            <div style="height: 100%; background: var(--primary); width: {{ $completionRate }}%; transition: width 0.3s;"></div>
                        </div>
                        <div style="text-align: center; margin-top: 0.5rem; font-size: var(--font-size-xs); color: var(--gray-500);">
                            {{ $stats['completed_topics'] ?? 0 }} of {{ $stats['total_topics'] ?? 0 }} topics completed
                        </div>
                    </div>
                    
                    <!-- Course Breakdown -->
                    @if($enrolledCourses->count() > 0)
                        <h3 style="font-size: var(--font-size-sm); font-weight: 600; color: var(--gray-600); margin-bottom: 0.75rem;">Course Breakdown</h3>
                        <div class="items-list">
                            @foreach($enrolledCourses->take(3) as $enrollment)
                                @php
                                    $course = $enrollment->course;
                                    $courseProgress = $course->getStudentProgress(auth()->id());
                                    $courseProgressPercentage = $courseProgress['percentage'];
                                @endphp
                                <div class="list-item" style="flex-direction: column; align-items: stretch;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                        <span style="font-size: var(--font-size-sm); font-weight: 500; color: var(--gray-700);">
                                            {{ Str::limit($course->title, 20) }}
                                        </span>
                                        <span style="font-size: var(--font-size-sm); font-weight: 700; color: {{ $courseProgressPercentage >= 80 ? 'var(--success)' : 'var(--primary)' }};">
                                            {{ $courseProgressPercentage }}%
                                        </span>
                                    </div>
                                    <div style="height: 6px; background: var(--gray-200); border-radius: 3px; overflow: hidden;">
                                        <div style="height: 100%; background: {{ $courseProgressPercentage >= 80 ? 'var(--success)' : 'var(--primary)' }}; width: {{ $courseProgressPercentage }}%; transition: width 0.3s;"></div>
                                    </div>
                                    <div style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: 0.25rem;">
                                        {{ $courseProgress['completed'] }}/{{ $courseProgress['total'] }} topics
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Access Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bolt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Quick Access
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="quick-actions-grid" style="grid-template-columns: 1fr;">
                        <a href="{{ route('student.topics.index') }}" class="action-card action-primary">
                            <div class="action-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="action-title">All Topics</div>
                            <div class="action-subtitle">{{ $stats['completed_topics'] ?? 0 }} completed</div>
                        </a>
                        
                        <a href="{{ route('student.quizzes.index') }}" class="action-card action-warning">
                            <div class="action-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="action-title">Take Quizzes</div>
                            <div class="action-subtitle">{{ $availableQuizzesCount ?? 0 }} available</div>
                        </a>
                        
                        <a href="{{ route('student.assignments.index') }}" class="action-card action-success">
                            <div class="action-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="action-title">Assignments</div>
                            <div class="action-subtitle">{{ $totalAssignments ?? 0 }} total</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines Card -->
            @if(!$upcomingQuizzes->isEmpty())
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-alt" style="color: var(--primary); margin-right: 0.5rem;"></i>
                        Upcoming Deadlines
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @foreach($upcomingQuizzes->take(3) as $quiz)
                            <div class="list-item" style="flex-direction: column; align-items: stretch;">
                                <div class="item-name">{{ $quiz->title }}</div>
                                <div class="item-details">{{ Str::limit($quiz->description, 50) }}</div>
                                <div class="item-meta">
                                    <span class="item-badge badge-warning">
                                        <i class="fas fa-calendar"></i> 
                                        {{ $quiz->available_until ? $quiz->available_until->format('M d, Y') : 'No deadline' }}
                                    </span>
                                </div>
                                <div style="margin-top: 0.5rem;">
                                    <a href="{{ route('student.quizzes.take', Crypt::encrypt($quiz->id)) }}" class="btn btn-primary btn-sm" style="width: 100%;">
                                        <i class="fas fa-play"></i> Take Quiz
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© 2024 School Management System. Student Portal</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time' }}
            • Progress updated: {{ now()->format('M d, Y h:i A') }}
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
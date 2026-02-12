@extends('layouts.student')

@section('title', 'My Courses - Student Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Courses</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-book-open"></i> Browse and access your enrolled courses
                        @if($enrolledCourses->count() > 0)
                            <span class="separator">•</span>
                            <span class="pending-notice">{{ $enrolledCourses->count() }} enrolled</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Enrolled Courses</div>
                    <div class="stat-number">{{ number_format($enrolledCourses->count()) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Completed Courses</div>
                    <div class="stat-number">{{ number_format($overallStats['completed_courses'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-number">{{ number_format($overallStats['in_progress_courses'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Topics</div>
                    <div class="stat-number">{{ number_format($overallStats['total_topics'] ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column - Courses List -->
        <div class="left-column">
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-book-open"></i>
                        My Enrolled Courses
                    </h2>
                    <div class="header-actions">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Search my courses..." id="search-courses">
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    @endif

                    @if($enrolledCourses->isEmpty())
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3 class="empty-title">No courses enrolled yet</h3>
                            <p class="empty-text">You haven't enrolled in any courses yet. Check out available courses below to get started.</p>
                            <div class="empty-hint">
                                <i class="fas fa-lightbulb"></i>
                                Enroll in courses to start learning and track your progress
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="courses-table">
                                <thead>
                                    <tr>
                                        <th>Course Title</th>
                                        <th class="hide-on-mobile">Code</th>
                                        <th class="hide-on-tablet">Teacher</th>
                                        <th>Progress</th>
                                        <th class="hide-on-tablet">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledCourses as $enrollment)
                                    @php
                                        $course = $enrollment->course;
                                        $encryptedId = Crypt::encrypt($course->id);
                                        
                                        // Get progress data from the enrollment object
                                        $progressData = $enrollment->progress_data ?? [
                                            'total' => 0,
                                            'completed' => 0,
                                            'percentage' => 0,
                                            'is_completed' => false
                                        ];
                                        
                                        $totalTopics = $progressData['total'];
                                        $completedTopics = $progressData['completed'];
                                        $progressPercentage = $progressData['percentage'];
                                        $isCompleted = $progressData['is_completed'] || $enrollment->grade !== null;
                                    @endphp
                                    <tr class="clickable-row" 
                                        data-href="{{ route('student.courses.show', $encryptedId) }}"
                                        data-title="{{ strtolower($course->title) }}">
                                        <td>
                                            <div class="course-info-cell">
                                                <div class="course-icon course-{{ ($loop->index % 4) + 1 }}">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                                <div class="course-details">
                                                    <div class="course-name">{{ $course->title }}</div>
                                                    <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                                                    <div class="course-mobile-info">
                                                        <div class="course-code-mobile">{{ $course->course_code }}</div>
                                                        @if($course->teacher)
                                                        <div class="teacher-mobile">
                                                            <i class="fas fa-chalkboard-teacher"></i>
                                                            {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hide-on-mobile">
                                            <span class="course-code">{{ $course->course_code }}</span>
                                        </td>
                                        <td class="hide-on-tablet">
                                            @if($course->teacher)
                                            <div class="teacher-info">
                                                <div class="teacher-avatar">
                                                    {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                                                </div>
                                                <div class="teacher-details">
                                                    <div class="teacher-name">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                                                    @if($course->teacher->employee_id)
                                                    <div class="teacher-id">{{ $course->teacher->employee_id }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @else
                                            <span class="no-teacher">Not assigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="students-count">
                                                @if($isCompleted)
                                                    <div class="count-number">{{ $enrollment->grade ?? 100 }}%</div>
                                                    <div class="count-label">Completed</div>
                                                @else
                                                    <div class="count-number">{{ $progressPercentage }}%</div>
                                                    <div class="count-label">
                                                        @if($totalTopics > 0)
                                                            {{ $completedTopics }}/{{ $totalTopics }} topics
                                                        @else
                                                            No topics
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="hide-on-tablet">
                                            @if($isCompleted)
                                                <span class="item-badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Completed
                                                </span>
                                            @else
                                                <span class="item-badge badge-primary">
                                                    <i class="fas fa-clock"></i> In Progress
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                @if($enrolledCourses instanceof \Illuminate\Pagination\AbstractPaginator && $enrolledCourses->hasPages())
                <div class="card-footer">
                    <div class="pagination-info">
                        Showing {{ $enrolledCourses->firstItem() }} to {{ $enrolledCourses->lastItem() }} of {{ $enrolledCourses->total() }} courses
                    </div>
                    <div class="pagination-links">
                        {{ $enrolledCourses->links() }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Available Courses Section -->
            @if($availableCourses->isNotEmpty())
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-plus-circle"></i>
                        Available Courses to Enroll
                    </h2>
                    <div class="header-actions">
                        <span class="item-badge badge-info">{{ $availableCourses->count() }} available</span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="courses-table">
                            <thead>
                                <tr>
                                    <th>Course Title</th>
                                    <th class="hide-on-mobile">Code</th>
                                    <th class="hide-on-tablet">Teacher</th>
                                    <th>Credits</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableCourses->take(6) as $course)
                                @php
                                    $encryptedId = Crypt::encrypt($course->id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="course-info-cell">
                                            <div class="course-icon" style="background: linear-gradient(135deg, var(--info-light), var(--info));">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="course-details">
                                                <div class="course-name">{{ $course->title }}</div>
                                                <div class="course-desc">{{ Str::limit($course->description, 50) }}</div>
                                                <div class="course-mobile-info">
                                                    <div class="course-code-mobile">{{ $course->course_code }}</div>
                                                    @if($course->teacher)
                                                    <div class="teacher-mobile">
                                                        <i class="fas fa-chalkboard-teacher"></i>
                                                        {{ $course->teacher->f_name }} {{ $course->teacher->l_name }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hide-on-mobile">
                                        <span class="course-code">{{ $course->course_code }}</span>
                                    </td>
                                    <td class="hide-on-tablet">
                                        @if($course->teacher)
                                        <div class="teacher-info">
                                            <div class="teacher-avatar" style="background: var(--info);">
                                                {{ strtoupper(substr($course->teacher->f_name, 0, 1)) }}
                                            </div>
                                            <div class="teacher-details">
                                                <div class="teacher-name">{{ $course->teacher->f_name }} {{ $course->teacher->l_name }}</div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="no-teacher">TBD</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="students-count">
                                            <div class="count-number">{{ $course->credits ?? 0 }}</div>
                                            <div class="count-label">Credits</div>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('student.courses.enroll', $encryptedId) }}" method="POST" class="enroll-form">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-user-plus"></i> Enroll
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($availableCourses->count() > 6)
                    <div class="view-all-container">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            View All {{ $availableCourses->count() }} Available Courses
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar Stats -->
        <div class="right-column">
            <!-- Quick Stats Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Learning Overview
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--primary-light), var(--primary));">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Total Enrollments</div>
                            </div>
                            <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--success-light), var(--success));">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Completed Courses</div>
                            </div>
                            <div class="stat-number">{{ $overallStats['completed_courses'] ?? 0 }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--warning-light), var(--warning));">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">In Progress</div>
                            </div>
                            <div class="stat-number">{{ $overallStats['in_progress_courses'] ?? 0 }}</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--info-light), var(--info));">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Average Grade</div>
                            </div>
                            <div class="stat-number">{{ $overallStats['average_grade'] ?? 0 }}%</div>
                        </div>
                        
                        <div class="list-item">
                            <div class="item-avatar" style="background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Completion Rate</div>
                            </div>
                            <div class="stat-number">
                                @if(($overallStats['total_topics'] ?? 0) > 0)
                                    {{ round((($overallStats['completed_topics'] ?? 0) / ($overallStats['total_topics'] ?? 1)) * 100) }}%
                                @else
                                    0%
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-history"></i>
                        Recent Activity
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        @forelse($recentActivities as $activity)
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--gray-100); color: var(--gray-700);">
                                @if($activity['type'] === 'grade')
                                    <i class="fas fa-graduation-cap" style="color: var(--success);"></i>
                                @elseif($activity['type'] === 'enrollment')
                                    <i class="fas fa-user-plus" style="color: var(--info);"></i>
                                @else
                                    <i class="fas fa-book-open" style="color: var(--primary);"></i>
                                @endif
                            </div>
                            <div class="item-info">
                                <div class="item-name">{{ $activity['text'] }}</div>
                                <div class="item-meta">{{ $activity['time'] }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="empty-text">No recent activity</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-lightbulb"></i>
                        Study Tips
                    </h2>
                </div>
                
                <div class="card-body">
                    <div class="items-list">
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Consistent Schedule</div>
                                <div class="item-meta">Study at the same time each day</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Take Notes</div>
                                <div class="item-meta">Improves retention by 34%</div>
                            </div>
                        </div>
                        <div class="list-item">
                            <div class="item-avatar" style="background: var(--warning-light); color: var(--warning-dark);">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="item-info">
                                <div class="item-name">Study Groups</div>
                                <div class="item-meta">Collaborate with classmates</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p class="footer-note">
            Student Dashboard • Last accessed {{ now()->format('M d, Y h:i A') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make rows clickable
        const clickableRows = document.querySelectorAll('.clickable-row');
        
        clickableRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || 
                    e.target.closest('a') || e.target.closest('button') || 
                    e.target.closest('.enroll-form')) {
                    return;
                }
                
                const href = this.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            });
        });

        // Search functionality
        const searchInput = document.getElementById('search-courses');
        const courseRows = document.querySelectorAll('.clickable-row');
        
        if (searchInput && courseRows.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                courseRows.forEach(row => {
                    const courseTitle = row.dataset.title || '';
                    if (searchTerm === '' || courseTitle.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Enroll button confirmation
        const enrollForms = document.querySelectorAll('.enroll-form');
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.stopPropagation();
                if (!confirm('Are you sure you want to enroll in this course?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
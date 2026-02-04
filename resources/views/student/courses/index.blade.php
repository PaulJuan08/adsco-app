@extends('layouts.student')

@section('title', 'My Courses - Student Dashboard')

@section('content')
<!-- Page Header -->
<div class="top-header">
    <div class="greeting">
        <h1>My Courses</h1>
        <p>Browse and access your enrolled courses</p>
    </div>
    <div class="user-info">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}
        </div>
        <span class="badge badge-student">
            <i class="fas fa-user-graduate"></i> Student
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                <div class="stat-label">Enrolled Courses</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <!-- FIX: Use overallStats instead of completedCount -->
                <div class="stat-number">{{ $overallStats['completed_courses'] ?? 0 }}</div>
                <div class="stat-label">Completed Courses</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <!-- FIX: Use overallStats instead of inProgressCount -->
                <div class="stat-number">{{ $overallStats['in_progress_courses'] ?? 0 }}</div>
                <div class="stat-label">In Progress</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <!-- FIX: Use overallStats instead of totalTopics -->
                <div class="stat-number">{{ $overallStats['total_topics'] ?? 0 }}</div>
                <div class="stat-label">Total Topics</div>
            </div>
            <div class="stat-icon">
                <i class="fas fa-list-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="content-grid">
    <!-- Courses List Card -->
    <div class="card main-card">
        <div class="card-header">
            <div class="card-title">My Enrolled Courses</div>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Search courses..." id="search-courses">
                </div>
            </div>
        </div>
        
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
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h3>No courses enrolled yet</h3>
            <p>You haven't enrolled in any courses yet. Check out available courses below to get started.</p>
            <div class="empty-hint">
                <i class="fas fa-lightbulb"></i>
                Enroll in courses to start learning
            </div>
        </div>
        @else
        <!-- Courses Grid -->
        <div class="courses-grid">
            @foreach($enrolledCourses as $enrollment)
            @php
                $course = $enrollment->course;
                $encryptedId = Crypt::encrypt($course->id);
            @endphp
            <div class="course-card" data-title="{{ strtolower($course->title) }}">
                <div class="course-card-header">
                    <div class="course-icon course-{{ ($loop->index % 4) + 1 }}">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="course-status">
                        @if($enrollment->grade)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i>
                                Completed
                            </span>
                        @else
                            <span class="badge badge-primary">
                                <i class="fas fa-clock"></i>
                                In Progress
                            </span>
                        @endif
                    </div>
                </div>
                <div class="course-card-body">
                    <h3 class="course-title">{{ $course->title }}</h3>
                    <div class="course-code">{{ $course->course_code }}</div>
                    <p class="course-description">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>{{ $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'TBD' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-credit-card"></i>
                            <span>{{ $course->credits }} Credits</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-list-check"></i>
                            <span>{{ $course->topics_count ?? 0 }} Topics</span>
                        </div>
                    </div>
                    
                    <!-- ADD PROGRESS BAR HERE -->
                    @if(isset($enrollment->course->progress) || isset($enrollment->progress))
                        @php
                            $progress = $enrollment->progress ?? ($enrollment->course->progress ?? null);
                        @endphp
                        @if($progress)
                        <div class="course-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress['percentage'] }}%"></div>
                            </div>
                            <div class="progress-text">
                                <span class="progress-percent">{{ $progress['percentage'] }}%</span>
                                <span class="progress-stats">
                                    {{ $progress['completed'] }}/{{ $progress['total'] }} topics
                                </span>
                            </div>
                        </div>
                        @endif
                    @endif
                    
                    @if($enrollment->grade)
                    <div class="grade-display">
                        <div class="grade-label">Your Grade</div>
                        <div class="grade-value">{{ $enrollment->grade }}% ({{ $enrollment->getGradeLetterAttribute() }})</div>
                    </div>
                    @endif
                </div>
                <div class="course-card-footer">
                    <a href="{{ route('student.courses.show', $encryptedId) }}" class="btn btn-primary">
                        <i class="fas fa-door-open"></i> Enter Course
                    </a>
                    <div class="action-links">
                        <a href="{{ route('student.courses.show', $encryptedId) }}" class="text-link">
                            <i class="fas fa-info-circle"></i> Details
                        </a>
                        @if($enrollment->grade)
                        <a href="{{ route('student.courses.grades', $encryptedId) }}" class="text-link">
                            <i class="fas fa-chart-bar"></i> Grades
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($enrolledCourses instanceof \Illuminate\Pagination\AbstractPaginator && $enrolledCourses->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                Showing {{ $enrolledCourses->firstItem() }} to {{ $enrolledCourses->lastItem() }} of {{ $enrolledCourses->total() }} courses
            </div>
            <div class="pagination-links">
                @if($enrolledCourses->onFirstPage())
                <span class="pagination-btn disabled">Previous</span>
                @else
                <a href="{{ $enrolledCourses->previousPageUrl() }}" class="pagination-btn">Previous</a>
                @endif
                
                @foreach(range(1, min(5, $enrolledCourses->lastPage())) as $page)
                    @if($page == $enrolledCourses->currentPage())
                    <span class="pagination-btn active">{{ $page }}</span>
                    @else
                    <a href="{{ $enrolledCourses->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach
                
                @if($enrolledCourses->hasMorePages())
                <a href="{{ $enrolledCourses->nextPageUrl() }}" class="pagination-btn">Next</a>
                @else
                <span class="pagination-btn disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
        @endif
        
        <!-- Available Courses Section -->
        @if($availableCourses->isNotEmpty())
        <div class="available-courses-section">
            <div class="section-header">
                <h3><i class="fas fa-plus-circle"></i> Available Courses to Enroll</h3>
                <p class="section-subtitle">Discover new courses to expand your knowledge</p>
            </div>
            
            <div class="available-courses-grid">
                @foreach($availableCourses as $course)
                @php
                    $encryptedId = Crypt::encrypt($course->id);
                @endphp
                <div class="available-course-card">
                    <div class="available-course-header">
                        <div class="available-course-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="available-course-badge">
                            <span class="badge badge-info">
                                <i class="fas fa-star"></i> New
                            </span>
                        </div>
                    </div>
                    <div class="available-course-body">
                        <h4 class="available-course-title">{{ $course->title }}</h4>
                        <div class="available-course-code">{{ $course->course_code }}</div>
                        <p class="available-course-description">{{ Str::limit($course->description, 80) }}</p>
                        
                        <div class="available-course-meta">
                            <div class="meta-item">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span>{{ $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'TBD' }}</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-credit-card"></i>
                                <span>{{ $course->credits }} Credits</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-list-check"></i>
                                <span>{{ $course->topics_count ?? 0 }} Topics</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $course->students_count ?? 0 }} Students</span>
                            </div>
                        </div>
                        
                        <div class="available-course-tags">
                            @if($course->duration_weeks)
                            <span class="course-tag">
                                <i class="fas fa-clock"></i> {{ $course->duration_weeks }} weeks
                            </span>
                            @endif
                            @if($course->level)
                            <span class="course-tag">
                                <i class="fas fa-chart-line"></i> {{ ucfirst($course->level) }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="available-course-footer">
                        <form action="{{ route('student.courses.enroll', $encryptedId) }}" method="POST" class="enroll-form">
                            @csrf
                            <button type="submit" class="btn btn-success enroll-btn">
                                <i class="fas fa-user-plus"></i> Enroll Now
                            </button>
                        </form>
                        <a href="{{ route('student.courses.show', $encryptedId) }}" class="btn btn-outline preview-btn">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            
            @if(isset($hasMoreAvailableCourses) && $hasMoreAvailableCourses)
            <div class="view-all-container">
                <a href="{{ route('student.courses.available') }}" class="btn btn-outline view-all-btn">
                    <i class="fas fa-list"></i> View All Available Courses
                </a>
            </div>
            @endif
        </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar-container">
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Quick Stats</div>
            </div>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Total Enrollments</span>
                    <span class="stat-value">{{ $enrolledCourses->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Completed Courses</span>
                    <!-- FIX: Use overallStats instead of completedCount -->
                    <span class="stat-value">{{ $overallStats['completed_courses'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">In Progress</span>
                    <!-- FIX: Use overallStats instead of inProgressCount -->
                    <span class="stat-value">{{ $overallStats['in_progress_courses'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Avg. Progress</span>
                    <!-- FIX: Use overallStats for average progress -->
                    <span class="stat-value">{{ $overallStats['average_progress'] ?? 0 }}%</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Avg. Grade</span>
                    <!-- FIX: Use overallStats instead of averageGrade -->
                    <span class="stat-value">{{ $overallStats['average_grade'] ?? 0 }}%</span>
                </div>
                @if($availableCourses->isNotEmpty())
                <div class="stat-item">
                    <span class="stat-label">Available Courses</span>
                    <span class="stat-value">{{ $availableCourses->count() }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Add Topic Progress Card -->
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Topic Progress</div>
            </div>
            <div class="stats-list">
                <div class="stat-item">
                    <span class="stat-label">Completed Topics</span>
                    <span class="stat-value">{{ $overallStats['completed_topics'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Topics</span>
                    <span class="stat-value">{{ $overallStats['total_topics'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Completion Rate</span>
                    <span class="stat-value">
                        @if(($overallStats['total_topics'] ?? 0) > 0)
                            {{ round((($overallStats['completed_topics'] ?? 0) / ($overallStats['total_topics'] ?? 1)) * 100) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Recent Activity</div>
            </div>
            <div class="activity-list">
                @forelse($recentActivities as $activity)
                <div class="activity-item">
                    <div class="activity-icon">
                        @if($activity['type'] === 'grade')
                        <i class="fas fa-graduation-cap"></i>
                        @elseif($activity['type'] === 'enrollment')
                        <i class="fas fa-user-plus"></i>
                        @else
                        <i class="fas fa-book-open"></i>
                        @endif
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">{{ $activity['text'] }}</div>
                        <div class="activity-time">{{ $activity['time'] }}</div>
                    </div>
                </div>
                @empty
                <div class="no-activity">
                    <i class="fas fa-info-circle"></i>
                    <div>No recent activity</div>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Quick Links -->
        <!-- <div class="card sidebar-card">
            <div class="card-header">
                <div class="card-title">Quick Links</div>
            </div>
            <div class="quick-links">
                <a href="{{ route('student.topics.index') }}" class="quick-link">
                    <i class="fas fa-book-open"></i>
                    <span>Browse Topics</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('student.assignments.index') }}" class="quick-link">
                    <i class="fas fa-tasks"></i>
                    <span>My Assignments</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('student.quizzes.index') }}" class="quick-link">
                    <i class="fas fa-question-circle"></i>
                    <span>Take Quizzes</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
                <a href="{{ route('student.progress.index') }}" class="quick-link">
                    <i class="fas fa-chart-line"></i>
                    <span>View Progress</span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div> -->
    </div>
</div>

<style>
    /* Base Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #e0e7ff;
        --secondary: #6c757d;
        --success: #28a745;
        --success-light: #d1fae5;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --info-light: #d0ebff;
        --light: #f8f9fa;
        --dark: #343a40;
        --border: #e9ecef;
    }

    /* Available Courses Section */
    .available-courses-section {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border);
    }

    .section-header {
        margin-bottom: 1.5rem;
    }

    .section-header h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-subtitle {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
    }

    .available-courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .available-courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .available-courses-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Available Course Card */
    .available-course-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border-left: 4px solid var(--info);
    }

    .available-course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        border-color: var(--info);
    }

    .available-course-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.5rem 1.5rem 0;
    }

    .available-course-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--info) 0%, #3bc9db 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .available-course-badge .badge-info {
        background: var(--info-light);
        color: var(--info);
        border: 1px solid #99e9f2;
    }

    .available-course-body {
        padding: 1rem 1.5rem;
    }

    .available-course-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .available-course-code {
        font-size: 0.875rem;
        color: var(--info);
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .available-course-description {
        font-size: 0.875rem;
        color: var(--secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .available-course-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .available-course-meta .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--secondary);
    }

    .available-course-meta .meta-item i {
        width: 16px;
        color: var(--info);
    }

    .available-course-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .course-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 20px;
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .available-course-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.75rem;
    }

    .enroll-form {
        flex: 1;
    }

    .enroll-btn {
        background: var(--success);
        color: white;
        width: 100%;
    }

    .enroll-btn:hover {
        background: #218838;
        transform: translateY(-1px);
    }

    .preview-btn {
        flex: 1;
        background: transparent;
        color: var(--info);
        border: 1px solid var(--info);
    }

    .preview-btn:hover {
        background: var(--info-light);
        border-color: var(--info);
    }

    .view-all-container {
        text-align: center;
        margin-top: 1.5rem;
    }

    .view-all-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
    }

    /* Quick Links */
    .quick-links {
        padding: 0.5rem;
    }

    .quick-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem;
        text-decoration: none;
        color: var(--dark);
        border-radius: 8px;
        transition: all 0.2s;
        margin-bottom: 0.5rem;
    }

    .quick-link:last-child {
        margin-bottom: 0;
    }

    .quick-link:hover {
        background: var(--primary-light);
        color: var(--primary);
        transform: translateX(4px);
    }

    .quick-link i:first-child {
        width: 24px;
        font-size: 1rem;
    }

    .quick-link span {
        flex: 1;
        margin: 0 1rem;
        font-size: 0.875rem;
    }

    .quick-link i:last-child {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    /* Existing styles remain the same */
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        
        .sidebar-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .main-card {
        grid-column: 1;
    }

    .sidebar-card {
        margin-bottom: 1.5rem;
    }

    .sidebar-card:last-child {
        margin-bottom: 0;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    @media (max-width: 768px) {
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
    }

    /* Search Container */
    .search-container {
        position: relative;
        min-width: 200px;
    }

    .search-container i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--secondary);
    }

    .search-container input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }

    .search-container input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    @media (max-width: 768px) {
        .search-container {
            min-width: unset;
            width: 100%;
        }
    }

    /* Courses Grid */
    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .courses-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Course Card */
    .course-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .course-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.5rem 1.5rem 0;
    }

    .course-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .course-1 {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .course-2 {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .course-3 {
        background: linear-gradient(135deg, #db2777 0%, #ec4899 100%);
    }

    .course-4 {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .course-card-body {
        padding: 1rem 1.5rem;
    }

    .course-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    .course-code {
        font-size: 0.875rem;
        color: var(--primary);
        font-weight: 500;
        margin-bottom: 0.75rem;
    }

    .course-description {
        font-size: 0.875rem;
        color: var(--secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
    }

    .course-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--secondary);
    }

    .meta-item i {
        width: 16px;
        color: var(--primary);
    }

    .grade-display {
        padding: 0.75rem;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-radius: 8px;
        margin-top: 1rem;
        border: 1px solid #bbf7d0;
    }

    .grade-label {
        font-size: 0.75rem;
        color: #166534;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .grade-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #166534;
    }

    .course-card-footer {
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .action-links {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 0.5rem;
    }

    .text-link {
        font-size: 0.8125rem;
        color: var(--primary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        transition: color 0.2s;
    }

    .text-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        justify-content: center;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #4f46e5;
        transform: translateY(-1px);
    }

    .btn-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary-light);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-student {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-primary {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .badge-success {
        background: #dcfce7;
        color: #065f46;
    }

    .badge-info {
        background: #d0ebff;
        color: #0c8599;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--secondary);
        margin-bottom: 1.5rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.5;
    }

    .empty-hint {
        margin-top: 1rem;
        color: var(--secondary);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Alerts */
    .alert {
        margin: 0 1.5rem 1.5rem;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert i {
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .alert {
            margin: 0 1rem 1rem;
        }
    }

    /* Pagination */
    .pagination-container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
    }

    @media (min-width: 768px) {
        .pagination-container {
            flex-direction: row;
        }
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-btn {
        padding: 0.5rem 0.75rem;
        background: var(--primary-light);
        color: var(--primary);
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .pagination-btn:hover:not(.disabled):not(.active) {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
    }

    .pagination-btn.disabled {
        background: #f3f4f6;
        color: var(--secondary);
        cursor: not-allowed;
    }

    /* Stats List */
    .stats-list {
        padding: 0.5rem;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .stat-item:last-child {
        border-bottom: none;
    }

    .stat-label {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .stat-value {
        font-weight: 600;
        color: var(--dark);
    }

    /* Activity List */
    .activity-list {
        padding: 0.5rem;
    }

    .activity-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-text {
        font-size: 0.875rem;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .no-activity {
        padding: 2rem 1rem;
        text-align: center;
        color: var(--secondary);
    }

    .no-activity i {
        font-size: 2rem;
        color: #d1d5db;
        margin-bottom: 0.5rem;
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }

    .greeting h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .stat-card:nth-child(1) .stat-icon {
        background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
    }

    .stat-card:nth-child(2) .stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-card:nth-child(3) .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .stat-card:nth-child(4) .stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    /* Course Progress Bar */
    .course-progress {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border);
    }

    .course-progress .progress-bar {
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .course-progress .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .course-progress .progress-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
    }

    .course-progress .progress-percent {
        font-weight: 600;
        color: #10b981;
    }

    .course-progress .progress-stats {
        color: #6b7280;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality for enrolled courses
        const searchInput = document.getElementById('search-courses');
        const courseCards = document.querySelectorAll('.course-card');
        
        if (searchInput && courseCards.length > 0) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                courseCards.forEach(card => {
                    const courseTitle = card.dataset.title || '';
                    
                    if (searchTerm === '' || courseTitle.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Enroll button confirmation
        const enrollForms = document.querySelectorAll('.enroll-form');
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to enroll in this course?')) {
                    e.preventDefault();
                }
            });
        });

        // Smooth scroll to available courses
        const enrollButtons = document.querySelectorAll('.enroll-btn');
        enrollButtons.forEach(button => {
            button.addEventListener('click', function() {
                const availableSection = document.querySelector('.available-courses-section');
                if (availableSection && window.scrollY > availableSection.offsetTop) {
                    availableSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
</script>
@endpush
@endsection
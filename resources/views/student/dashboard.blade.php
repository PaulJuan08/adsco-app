@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
    <!-- Header -->
    <div class="top-header">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
            </div>
            <div class="greeting">
                <h1>Welcome back, {{ auth()->user()->f_name }}!</h1>
                <p>
                    @php
                        $completionRate = ($stats['total_topics'] ?? 0) > 0 
                            ? round((($stats['completed_topics'] ?? 0) / $stats['total_topics']) * 100)
                            : 0;
                    @endphp
                    You're enrolled in {{ $stats['total_courses'] ?? 0 }} courses with {{ $stats['completed_topics'] ?? 0 }}/{{ $stats['total_topics'] ?? 0 }} topics completed ({{ $completionRate }}%)
                </p>
            </div>
        </div>
        <div class="header-actions">
            <span class="badge badge-student">
                <i class="fas fa-user-graduate me-1"></i> Student
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $stats['total_courses'] ?? 0 }}</div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                <div class="stat-icon icon-courses">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-chalkboard-teacher text-primary me-1"></i> {{ $stats['in_progress_courses'] ?? 0 }} in progress
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $stats['completed_courses'] ?? 0 }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-icon icon-users">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-graduation-cap text-success me-1"></i> {{ $stats['completed_courses'] ?? 0 }}/{{ $stats['total_courses'] ?? 0 }} courses
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($stats['average_grade'] ?? 0, 1) }}%</div>
                    <div class="stat-label">Avg. Grade</div>
                </div>
                <div class="stat-icon icon-logins">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-chart-line text-info me-1"></i> Overall performance
            </div>
        </div>

        <!-- Add Progress Card -->
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    @php
                        $overallProgress = ($stats['total_topics'] ?? 0) > 0 
                            ? round((($stats['completed_topics'] ?? 0) / $stats['total_topics']) * 100)
                            : 0;
                    @endphp
                    <div class="stat-number">{{ $overallProgress }}%</div>
                    <div class="stat-label">Overall Progress</div>
                </div>
                <div class="stat-icon icon-progress">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-list-check text-warning me-1"></i> {{ $stats['completed_topics'] ?? 0 }}/{{ $stats['total_topics'] ?? 0 }} topics
            </div>
            <!-- Mini progress bar -->
            <div class="progress mt-2" style="height: 4px;">
                <div class="progress-bar" style="width: {{ $overallProgress }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="left-column">
            <!-- My Courses Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">My Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($enrolledCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No courses enrolled</p>
                        <p class="text-sm">Browse available courses to get started</p>
                    </div>
                @else
                    @foreach($enrolledCourses->take(3) as $enrollment)
                    @php
                        $course = $enrollment->course;
                        $encryptedId = Crypt::encrypt($course->id);
                        
                        // Calculate course progress
                        $courseProgress = $course->getStudentProgress(auth()->id());
                        $progressPercentage = $courseProgress['percentage'];
                    @endphp
                    <div class="course-item">
                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $course->title }}</div>
                            <div class="course-desc">{{ $course->course_code }} • {{ $course->teacher->f_name ?? 'Instructor' }}</div>
                            <div class="course-teacher">
                                <span class="badge badge-primary">{{ $course->topics_count ?? 0 }} topics</span>
                                <span class="ms-2">{{ $enrollment->status == 'completed' ? 'Completed' : 'In Progress' }}</span>
                            </div>
                            
                            <!-- Progress Bar with Stats -->
                            <div class="course-progress-section">
                                <div class="progress-info">
                                    <span class="progress-text">
                                        {{ $courseProgress['completed'] }}/{{ $courseProgress['total'] }} topics completed
                                    </span>
                                    <span class="progress-percent">{{ $progressPercentage }}%</span>
                                </div>
                                <div class="progress mt-2">
                                    <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('student.courses.show', $encryptedId) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Available Courses Card -->
            @if(!$availableCourses->isEmpty())
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Available Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="view-all">
                        Browse all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @foreach($availableCourses->take(3) as $course)
                <div class="course-item">
                    <div class="course-icon course-{{ ($loop->index % 3) + 2 }}">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="course-info">
                        <div class="course-name">{{ $course->title }}</div>
                        <div class="course-desc">{{ $course->course_code }} • {{ $course->teacher->f_name ?? 'Instructor' }}</div>
                        <div class="course-teacher">
                            <span class="badge badge-info">{{ $course->credits ?? 0 }} credits</span>
                            <span class="ms-2">{{ $course->topics_count ?? 0 }} topics</span>
                        </div>
                        @if($course->topics_count > 0)
                        <div class="text-sm text-secondary mt-1">
                            <i class="fas fa-list-check"></i> Includes {{ $course->topics_count }} learning topics
                        </div>
                        @endif
                    </div>
                    <div>
                        @if($course->is_enrolled ?? false)
                            <span class="badge badge-success">Enrolled</span>
                        @else
                            <form action="{{ route('student.courses.enroll', Crypt::encrypt($course->id)) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus"></i> Enroll
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Progress Summary Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Progress Summary</h2>
                </div>
                
                <div class="card-body">
                    <!-- Overall Progress -->
                    <div class="progress-summary-item mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="text-sm font-semibold text-gray-600">Overall Progress</h3>
                            <span class="text-lg font-bold text-primary">{{ $overallProgress }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" style="width: {{ $overallProgress }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1 text-center">
                            {{ $stats['completed_topics'] ?? 0 }} of {{ $stats['total_topics'] ?? 0 }} topics completed
                        </div>
                    </div>
                    
                    <!-- Course Breakdown -->
                    <h3 class="text-sm font-semibold text-gray-600 mb-3">Course Breakdown</h3>
                    @if($enrolledCourses->count() > 0)
                        @foreach($enrolledCourses->take(3) as $enrollment)
                            @php
                                $course = $enrollment->course;
                                $courseProgress = $course->getStudentProgress(auth()->id());
                                $courseProgressPercentage = $courseProgress['percentage'];
                            @endphp
                            <div class="course-breakdown-item mb-3">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700 truncate" title="{{ $course->title }}">
                                        {{ Str::limit($course->title, 20) }}
                                    </span>
                                    <span class="text-sm font-bold {{ $courseProgressPercentage >= 80 ? 'text-success' : 'text-primary' }}">
                                        {{ $courseProgressPercentage }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $courseProgressPercentage >= 80 ? 'bg-success' : '' }}" 
                                         style="width: {{ $courseProgressPercentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $courseProgress['completed'] }}/{{ $courseProgress['total'] }} topics
                                </div>
                            </div>
                        @endforeach
                        
                        @if($enrolledCourses->count() > 3)
                            <div class="text-center mt-3">
                                <a href="{{ route('student.courses.index') }}" class="text-sm text-primary hover:underline">
                                    View all {{ $enrolledCourses->count() }} courses
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state py-4">
                            <i class="fas fa-book text-gray-300"></i>
                            <p class="text-sm text-gray-500 mt-2">No courses enrolled</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Quick Access</h2>
                </div>
                
                <div class="space-y-4">
                    <!-- Topics -->
                    <a href="{{ route('student.topics.index') }}" class="course-item quick-action-item">
                        <div class="course-icon course-1">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">All Topics</div>
                            <div class="course-desc">{{ $stats['completed_topics'] ?? 0 }} completed</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Quizzes -->
                    <a href="{{ route('student.quizzes.index') }}" class="course-item quick-action-item">
                        <div class="course-icon course-2">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Take Quizzes</div>
                            <div class="course-desc">{{ $availableQuizzesCount ?? 0 }} available</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <!-- Grades -->
                    @if(Route::has('student.grades.index'))
                    <a href="{{ route('student.grades.index') }}" class="course-item quick-action-item">
                        <div class="course-icon course-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">My Grades</div>
                            <div class="course-desc">{{ $stats['average_grade'] ?? 0 }}% average</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    @endif
                    
                    <!-- Assignments -->
                    <a href="{{ route('student.assignments.index') }}" class="course-item quick-action-item">
                        <div class="course-icon course-4">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Assignments</div>
                            <div class="course-desc">{{ $totalAssignments ?? 0 }} total</div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>

            <!-- Upcoming Deadlines Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Upcoming Deadlines</h2>
                </div>
                
                <div class="card-body">
                    @if($upcomingQuizzes->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p>No upcoming deadlines</p>
                        </div>
                    @else
                        <!-- Quizzes Section -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Available Quizzes</h3>
                            @foreach($upcomingQuizzes as $quiz)
                                <div class="deadline-item mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $quiz->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ Str::limit($quiz->description, 50) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700">
                                                Available Until: {{ $quiz->available_until ? $quiz->available_until->format('M d, Y') : 'No deadline' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                @if($quiz->available_until)
                                                    {{ $quiz->available_until->diffForHumans() }}
                                                @else
                                                    No deadline
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('student.quizzes.take', Crypt::encrypt($quiz->id)) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-play"></i> Take Quiz
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity Card -->
            @if(!$studentTopics->isEmpty() || !$studentAssignments->isEmpty())
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Activity</h2>
                </div>
                
                <div class="card-body">
                    <!-- Recent Topics -->
                    @if(!$studentTopics->isEmpty())
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Recent Topics</h3>
                            @foreach($studentTopics->take(3) as $topic)
                                @php
                                    $isCompleted = auth()->user()->completedTopics()
                                        ->where('topic_id', $topic->id)
                                        ->exists();
                                @endphp
                                <div class="activity-item mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-8 h-8 rounded-full {{ $isCompleted ? 'bg-green-100' : 'bg-blue-100' }} flex items-center justify-center">
                                                <i class="fas {{ $isCompleted ? 'fa-check text-green-600' : 'fa-book text-blue-600' }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800">{{ $topic->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ Str::limit($topic->description, 50) }}</p>
                                            <div class="text-xs text-gray-500 mt-1">
                                                @if($isCompleted)
                                                    <span class="text-success">✓ Completed</span>
                                                @else
                                                    <span class="text-primary">In Progress</span>
                                                @endif
                                                • {{ $topic->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('student.topics.show', Crypt::encrypt($topic->id)) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Recent Assignments -->
                    @if(!$studentAssignments->isEmpty())
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Recent Assignments</h3>
                            @foreach($studentAssignments->take(3) as $assignment)
                                <div class="activity-item mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-3">
                                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <i class="fas fa-tasks text-green-600 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-800">{{ $assignment->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $assignment->course->title ?? 'No Course' }}</p>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Due: {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : 'No deadline' }}
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('student.assignments.show', Crypt::encrypt($assignment->id)) }}" class="btn btn-sm btn-outline-primary">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 School Management System. Student Portal</p>
        <p class="text-sm mt-1">Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time' }}</p>
        <p class="text-xs text-gray-500 mt-1">
            Progress updated: {{ now()->format('M d, Y h:i A') }}
        </p>
    </footer>
@endsection

@push('styles')
<style>
    /* CSS Variables */
    :root {
        --primary: #4361ee;
        --primary-light: #eef2ff;
        --secondary: #6b7280;
        --success: #10b981;
        --success-light: #d1fae5;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --light: #f9fafb;
        --dark: #1f2937;
        --border: #e5e7eb;
    }

    /* Space utility */
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    /* Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.5rem;
    }

    .greeting h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0 0 0.25rem 0;
    }

    .greeting p {
        color: var(--secondary);
        font-size: 0.875rem;
        margin: 0;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-student {
        background: var(--success-light);
        color: #065f46;
    }

    .badge-primary {
        background: var(--primary-light);
        color: var(--primary);
    }

    .badge-success {
        background: var(--success-light);
        color: #065f46;
    }

    .badge-info {
        background: #e0f2fe;
        color: #0369a1;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--secondary);
        margin-top: 0.25rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .icon-courses {
        background: var(--primary-light);
        color: var(--primary);
    }

    .icon-users {
        background: #fef3c7;
        color: #f59e0b;
    }

    .icon-logins {
        background: #e0f2fe;
        color: var(--info);
    }

    .icon-progress {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-secondary {
        color: var(--secondary);
    }

    .text-gray-600 { color: #4b5563; }
    .text-gray-700 { color: #374151; }
    .text-gray-800 { color: #1f2937; }
    .text-gray-500 { color: #6b7280; }
    .text-success { color: var(--success); }
    .text-primary { color: var(--primary); }

    .font-semibold { font-weight: 600; }
    .font-medium { font-weight: 500; }
    .font-bold { font-weight: 700; }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--light);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark);
        margin: 0;
    }

    .view-all {
        font-size: 0.875rem;
        color: var(--primary);
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    .card-body {
        padding: 1.5rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    /* Course Items */
    .course-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .course-item:last-child {
        border-bottom: none;
    }

    .course-item:hover {
        background: var(--light);
    }

    .quick-action-item {
        text-decoration: none;
        color: inherit;
        cursor: pointer;
    }

    .quick-action-item:hover {
        background: var(--light);
        transform: translateX(4px);
        transition: all 0.2s;
    }

    .course-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
        font-size: 1.25rem;
    }

    .course-icon.course-1 {
        background: var(--primary-light);
        color: var(--primary);
    }

    .course-icon.course-2 {
        background: #fef3c7;
        color: #d97706;
    }

    .course-icon.course-3 {
        background: #e0f2fe;
        color: #0369a1;
    }

    .course-icon.course-4 {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .course-info {
        flex: 1;
        min-width: 0;
    }

    .course-name {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .course-desc {
        font-size: 0.875rem;
        color: var(--secondary);
        margin-bottom: 0.5rem;
    }

    .course-teacher {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
    }

    /* Progress Bar */
    .progress {
        height: 6px;
        background: var(--border);
        border-radius: 3px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-bar {
        height: 100%;
        background: var(--primary);
        border-radius: 3px;
        transition: width 0.3s;
    }

    .bg-success {
        background: var(--success) !important;
    }

    /* Course Progress Section */
    .course-progress-section {
        margin-top: 0.75rem;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .progress-text {
        font-size: 0.75rem;
        color: var(--secondary);
    }

    .progress-percent {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary);
    }

    /* Progress Summary */
    .progress-summary-item {
        padding: 0.75rem;
        background: var(--light);
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .course-breakdown-item {
        padding: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .course-breakdown-item:last-child {
        border-bottom: none;
    }

    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: #3a56d4;
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        background: transparent;
        color: var(--primary);
        border-color: var(--primary);
    }

    .btn-outline-primary:hover {
        background: var(--primary-light);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: var(--secondary);
        margin: 0;
    }

    /* Activity Items */
    .deadline-item,
    .activity-item {
        transition: transform 0.2s;
    }

    .deadline-item:hover,
    .activity-item:hover {
        transform: translateX(4px);
    }

    .flex {
        display: flex;
    }

    .justify-between {
        justify-content: space-between;
    }

    .items-start {
        align-items: flex-start;
    }

    .items-center {
        align-items: center;
    }

    .flex-shrink-0 {
        flex-shrink: 0;
    }

    .flex-1 {
        flex: 1;
    }

    .text-center {
        text-align: center;
    }

    .mr-2, .mx-2 { margin-right: 0.5rem; }
    .mr-3 { margin-right: 0.75rem; }
    .ms-1 { margin-left: 0.25rem; }
    .ms-2 { margin-left: 0.5rem; }
    .mt-1 { margin-top: 0.25rem; }
    .mt-2 { margin-top: 0.5rem; }
    .mt-3 { margin-top: 0.75rem; }
    .mb-1 { margin-bottom: 0.25rem; }
    .mb-2 { margin-bottom: 0.5rem; }
    .mb-3 { margin-bottom: 0.75rem; }
    .mb-4 { margin-bottom: 1rem; }

    .rounded-lg {
        border-radius: 0.5rem;
    }

    .bg-gray-50 {
        background: #f9fafb;
    }

    .bg-blue-100 {
        background: #dbeafe;
    }

    .bg-green-100 {
        background: #d1fae5;
    }

    /* Footer */
    .footer {
        text-align: center;
        padding: 1.5rem;
        color: var(--secondary);
        font-size: 0.875rem;
        margin-top: 2rem;
        border-top: 1px solid var(--border);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .top-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .user-info {
            width: 100%;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .course-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .course-icon {
            margin-bottom: 0.5rem;
        }
    }
    
    /* Inline form */
    .inline {
        display: inline;
    }
    
    .hover\:underline:hover {
        text-decoration: underline;
    }
    
    .text-lg {
        font-size: 1.125rem;
    }
    
    .text-xs {
        font-size: 0.75rem;
    }
</style>
@endpush
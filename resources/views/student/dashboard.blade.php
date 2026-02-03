@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
    <!-- Header -->
    <div class="top-header">
        <div class="greeting">
            <h1>Hello, {{ auth()->user()->f_name }}!</h1>
            <p>Welcome to your learning dashboard</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
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
                    <div class="stat-number">{{ $totalEnrolled ?? 0 }}</div>
                    <div class="stat-label">Enrolled Courses</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-check-circle text-success"></i> Active enrollments
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $completedCourses ?? 0 }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-trophy text-warning"></i> Courses finished
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($averageGrade ?? 0, 1) }}</div>
                    <div class="stat-label">Average Grade</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-percentage text-primary"></i> Overall performance
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $availableQuizzesCount ?? 0 }}</div>
                    <div class="stat-label">Available Quizzes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-pencil-alt"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-clock text-info"></i> Ready to take
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Left Column -->
        <div class="content-grid-left">
            <!-- Available Courses Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Available Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="view-all">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                @if($availableCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No courses available</p>
                        <p class="text-sm">Check back later for new courses</p>
                    </div>
                @else
                    @foreach($availableCourses->take(3) as $course)
                    <div class="course-item">
                        <div class="course-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $course->course_name }}</div>
                            <div class="course-desc">{{ $course->course_code }} • {{ $course->credits }} credits</div>
                            <div class="course-teacher">
                                <span class="badge badge-primary">Teacher: {{ $course->teacher->f_name ?? 'TBD' }}</span>
                            </div>
                        </div>
                        <div>
                            @if($course->isEnrolled(auth()->id()))
                                <a href="{{ route('student.course.show', $course->id) }}" class="btn btn-success">
                                    <i class="fas fa-door-open"></i> Access
                                </a>
                            @else
                                <form action="{{ route('student.courses.enroll', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Enroll
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- My Courses Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">My Enrolled Courses</h2>
                    <a href="{{ route('student.courses.index') }}" class="view-all">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                @if($enrolledCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <p>No enrolled courses yet</p>
                        <p class="text-sm">Enroll in available courses to get started</p>
                    </div>
                @else
                    @foreach($enrolledCourses->take(3) as $enrollment)
                    <div class="course-item">
                        <div class="course-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $enrollment->course->course_name ?? 'Unknown Course' }}</div>
                            <div class="course-desc">{{ $enrollment->course->course_code ?? 'N/A' }} • {{ $enrollment->course->credits ?? 0 }} credits</div>
                            <div class="course-teacher">
                                <span class="badge badge-primary">Teacher: {{ $enrollment->course->teacher->f_name ?? 'TBD' }}</span>
                                <span>Grade: {{ $enrollment->grade ?? 'Not graded' }}</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('student.course.show', $enrollment->course_id) }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="content-grid-right">
            <!-- Available Quizzes Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Available Quizzes</h2>
                    <a href="{{ route('student.quizzes.index') }}" class="view-all">
                        View all <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                
                @if($availableQuizzes->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No quizzes available</p>
                        <p class="text-sm">Check back later for new quizzes</p>
                    </div>
                @else
                    @foreach($availableQuizzes->take(3) as $quiz)
                    <div class="course-item">
                        <div class="course-icon quiz-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $quiz->title }}</div>
                            <div class="course-desc">{{ $quiz->questions->count() ?? 0 }} questions</div>
                            <div class="course-teacher">
                                <span class="badge badge-warning">Duration: {{ $quiz->duration ?? 0 }} mins</span>
                                @if($quiz->available_until)
                                    <span>Due: {{ $quiz->available_until->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            @php
                                // Check if student has attempted this quiz
                                // You'll need to implement this logic based on your QuizAttempt model
                                $hasAttempted = false; // Change this based on your implementation
                            @endphp
                            @if($hasAttempted)
                                <a href="{{ route('student.quizzes.results', Crypt::encrypt($quiz->id)) }}" class="btn btn-success">
                                    <i class="fas fa-chart-bar"></i> Results
                                </a>
                            @else
                                <a href="{{ route('student.quizzes.take', Crypt::encrypt($quiz->id)) }}" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Start
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Quick Links Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Quick Links</h2>
                </div>
                
                <div class="quick-links">
                    <a href="{{ route('student.timetable') }}" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="quick-link-info">
                            <div class="quick-link-title">Class Timetable</div>
                            <div class="quick-link-desc">View your schedule</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('student.grades') }}" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="quick-link-info">
                            <div class="quick-link-title">My Grades</div>
                            <div class="quick-link-desc">Check your performance</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('student.assignments') }}" class="quick-link">
                        <div class="quick-link-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="quick-link-info">
                            <div class="quick-link-title">Assignments</div>
                            <div class="quick-link-desc">View all assignments</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

<style>
/* Responsive Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .content-grid {
        grid-template-columns: 2fr 1fr;
    }
}

.content-grid-left {
    min-width: 0;
}

.content-grid-right {
    min-width: 0;
}

/* Header */
.top-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
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

.user-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    font-weight: 600;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.stat-card {
    background: var(--card-bg);
    padding: 1.25rem;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid var(--border);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
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
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.stat-card:nth-child(2) .stat-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.stat-card:nth-child(3) .stat-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-card:nth-child(4) .stat-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--secondary);
    font-size: 0.875rem;
    font-weight: 500;
}

.text-sm {
    font-size: 0.75rem;
}

.text-secondary {
    color: var(--secondary);
}

.text-success { color: #10b981; }
.text-warning { color: #f59e0b; }
.text-primary { color: #3b82f6; }
.text-info { color: #8b5cf6; }

/* Cards */
.card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid var(--border);
    margin-bottom: 1.5rem;
}

.card:last-child {
    margin-bottom: 0;
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
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Course Items */
.course-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.75rem;
    border: 1px solid var(--border);
    transition: all 0.2s;
}

.course-item:hover {
    background: #f9fafb;
    border-color: var(--primary);
}

.course-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    flex-shrink: 0;
}

.quiz-icon {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.course-info {
    flex: 1;
    min-width: 0;
}

.course-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.course-desc {
    color: var(--secondary);
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.course-teacher {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    font-size: 0.8rem;
}

.course-teacher span {
    color: var(--secondary);
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: #0da271;
    transform: translateY(-1px);
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #0da271;
}

.btn-warning {
    background: #f59e0b;
    color: white;
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

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-success {
    background: #dcfce7;
    color: #065f46;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--secondary);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-state p {
    margin-bottom: 0.25rem;
}

.empty-state .text-sm {
    font-size: 0.875rem;
    color: #9ca3af;
}

/* Quick Links */
.quick-links {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    border: 1px solid var(--border);
    transition: all 0.2s;
}

.quick-link:hover {
    background: #f9fafb;
    border-color: var(--primary);
    transform: translateY(-1px);
}

.quick-link-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.quick-link-info {
    flex: 1;
}

.quick-link-title {
    font-weight: 600;
    color: var(--dark);
    font-size: 0.95rem;
}

.quick-link-desc {
    color: var(--secondary);
    font-size: 0.8rem;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .top-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .user-info {
        width: 100%;
        justify-content: space-between;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .course-item {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
    
    .course-icon {
        align-self: center;
    }
    
    .course-teacher {
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Form inside course items */
.course-item form {
    margin: 0;
}

/* Ensure proper spacing */
.mb-4 {
    margin-bottom: 1.5rem !important;
}

.text-sm {
    font-size: 0.75rem;
}

/* Success/Error Messages */
.alert {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    border-left: 4px solid transparent;
}

.alert-success {
    background: #dcfce7;
    color: #065f46;
    border-left-color: #10b981;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
    border-left-color: #ef4444;
}

/* Update your CSS variables */
:root {
    --primary: #10b981;
    --primary-light: #d1fae5;
    --secondary: #6b7280;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --dark: #111827;
    --light: #f9fafb;
    --border: #e5e7eb;
    --card-bg: #ffffff;
}
</style>

@push('scripts')
<script>
    // Handle enrollment
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading states to enroll buttons
        const enrollForms = document.querySelectorAll('form[action*="enroll"]');
        
        enrollForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
                button.classList.add('loading');
            });
        });
        
        // Show success messages if any
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
    });
    
    function showNotification(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
        `;
        
        document.querySelector('.content-grid-left').prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
@endpush
@endsection
@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
    <!-- Header -->
    <div class="top-header">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->f_name, 0, 1)) }}
            </div>
            <div class="greeting">
                <h1>Welcome, {{ auth()->user()->f_name }}!</h1>
                <p>You're teaching {{ $myCourses->count() }} courses with {{ $totalStudents }} students</p>
            </div>
        </div>
        <div class="header-actions">
            <span class="badge badge-primary">
                <i class="fas fa-chalkboard-teacher me-1"></i> Teacher
            </span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $myCourses->count() }}</div>
                    <div class="stat-label">My Courses</div>
                </div>
                <div class="stat-icon icon-courses">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-chalkboard-teacher text-primary me-1"></i> Total courses assigned
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $totalStudents }}</div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-icon icon-users">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-user-graduate text-success me-1"></i> Across all courses
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ now()->format('M d') }}</div>
                    <div class="stat-label">Today's Date</div>
                </div>
                <div class="stat-icon icon-logins">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-clock text-info me-1"></i> {{ now()->format('l') }}
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
                    <a href="{{ route('teacher.courses.index') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($myCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No courses assigned</p>
                        <p class="text-sm">Contact administrator for course assignments</p>
                    </div>
                @else
                    @foreach($myCourses->take(3) as $course)
                    <div class="course-item">
                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $course->course_name ?? $course->title }}</div>
                            <div class="course-desc">{{ $course->course_code }} • {{ $course->credits ?? 0 }} credits</div>
                            <div class="course-teacher">
                                <span class="badge badge-primary">{{ $course->schedule ?? 'Schedule TBD' }}</span>
                                <span class="ms-2">{{ $course->enrollments_count ?? 0 }} students</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Recent Enrollments Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Enrollments</h2>
                    <a href="{{ route('teacher.enrollments') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($recentEnrollments->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <p>No recent enrollments</p>
                    </div>
                @else
                    @foreach($recentEnrollments as $enrollment)
                    <div class="announcement-item">
                        <div class="announcement-info">
                            <span class="announcement-badge">
                                New Enrollment
                            </span>
                            <div class="announcement-title">
                                {{ $enrollment->student->f_name ?? 'Student' }} enrolled in {{ $enrollment->course->title ?? 'Course' }}
                            </div>
                            <div class="announcement-text">
                                {{ $enrollment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @endforeach
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
                    <a href="{{ route('teacher.topics.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-1">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Topic</div>
                            <div class="course-desc">Add new learning material</div>
                        </div>
                    </a>
                    
                    
                    <!-- Quizzes -->
                    <a href="{{ route('teacher.quizzes.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-3">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Quiz</div>
                            <div class="course-desc">Add new quiz/test</div>
                        </div>
                    </a>
                    
                    <!-- Grades - Check if this route exists -->
                    @if(Route::has('teacher.grades.index'))
                    <a href="{{ route('teacher.grades.index') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Enter Grades</div>
                            <div class="course-desc">Submit student grades</div>
                        </div>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Upcoming Deadlines Card -->
            <div class="card">
            <div class="card-header">
                <h2 class="card-title">Upcoming Deadlines</h2>
            </div>
            
            <div class="card-body">
                @if($upcomingAssignments->isEmpty() && $upcomingQuizzes->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <p>No upcoming deadlines</p>
                    </div>
                @else
                    <!-- Assignments Section -->
                    @if(!$upcomingAssignments->isEmpty())
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Assignments</h3>
                            @foreach($upcomingAssignments as $assignment)
                                <div class="deadline-item mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $assignment->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $assignment->course->title ?? 'No Course' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700">
                                                Due: {{ $assignment->due_date->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $assignment->due_date->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Quizzes Section -->
                    @if(!$upcomingQuizzes->isEmpty())
                        <div>
                            <h3 class="text-sm font-semibold text-gray-600 mb-2">Quizzes</h3>
                            @foreach($upcomingQuizzes as $quiz)
                                <div class="deadline-item mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $quiz->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ Str::limit($quiz->description, 50) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700">
                                                Available Until: {{ $quiz->available_until->format('M d, Y') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $quiz->available_until->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 School Management System. Teacher Portal</p>
        <p class="text-sm mt-1">Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time' }}</p>
    </footer>
@endsection

@push('styles')
<style>
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    :root {
        --primary-light: #e3f2fd;
        --warning-light: #fff3cd;
        --success-light: #d1e7dd;
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
</style>
@endpush
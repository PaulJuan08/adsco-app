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
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">0</div>
                    <div class="stat-label">Pending Grading</div>
                </div>
                <div class="stat-icon icon-pending">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-exclamation-circle text-warning me-1"></i> Assignments to grade
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
                    
                    <!-- Assignments -->
                    <a href="{{ route('teacher.assignments.create') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-2">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Create Assignment</div>
                            <div class="course-desc">Post new assignment</div>
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
                
                @php
                    // Get upcoming deadlines for assignments and quizzes
                    $upcomingAssignments = App\Models\Assignment::whereHas('course', function($query) {
                        $query->where('teacher_id', auth()->id());
                    })
                    ->where('due_date', '>', now())
                    ->where('is_published', true)
                    ->orderBy('due_date', 'asc')
                    ->take(3)
                    ->get();
                    
                    $upcomingQuizzes = App\Models\Quiz::whereHas('course', function($query) {
                        $query->where('teacher_id', auth()->id());
                    })
                    ->where('available_until', '>', now())
                    ->where('is_published', true)
                    ->orderBy('available_until', 'asc')
                    ->take(3)
                    ->get();
                    
                    $allDeadlines = collect();
                    foreach ($upcomingAssignments as $assignment) {
                        $allDeadlines->push([
                            'type' => 'assignment',
                            'title' => $assignment->title,
                            'due_date' => $assignment->due_date,
                            'course' => $assignment->course,
                            'icon' => 'fas fa-tasks',
                            'color' => 'primary'
                        ]);
                    }
                    
                    foreach ($upcomingQuizzes as $quiz) {
                        $allDeadlines->push([
                            'type' => 'quiz',
                            'title' => $quiz->title,
                            'due_date' => $quiz->available_until,
                            'course' => $quiz->course,
                            'icon' => 'fas fa-question-circle',
                            'color' => 'warning'
                        ]);
                    }
                    
                    // Sort by due date
                    $allDeadlines = $allDeadlines->sortBy('due_date')->take(5);
                @endphp
                
                @if($allDeadlines->isEmpty())
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <i class="fas fa-flag-checkered"></i>
                        <p>No upcoming deadlines</p>
                        <p class="text-sm">All assignments and quizzes are up to date</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($allDeadlines as $deadline)
                        <div class="course-item" style="border-left: 3px solid var(--{{ $deadline['color'] }});">
                            <div class="course-icon" style="background: var(--{{ $deadline['color'] }}-light); color: var(--{{ $deadline['color'] }});">
                                <i class="{{ $deadline['icon'] }}"></i>
                            </div>
                            <div class="course-info">
                                <div class="course-name">{{ $deadline['title'] }}</div>
                                <div class="course-desc">{{ $deadline['course']->title ?? $deadline['course']->course_name }}</div>
                                <div class="course-teacher">
                                    <span class="badge badge-{{ $deadline['color'] }}">
                                        {{ ucfirst($deadline['type']) }}
                                    </span>
                                    <span class="ms-2">
                                        Due: {{ $deadline['due_date']->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('teacher.' . $deadline['type'] . 's.show', Crypt::encrypt($deadline['type'] == 'assignment' ? App\Models\Assignment::where('title', $deadline['title'])->first()->id : App\Models\Quiz::where('title', $deadline['title'])->first()->id)) }}" 
                                   class="btn btn-sm btn-outline-{{ $deadline['color'] }}">
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
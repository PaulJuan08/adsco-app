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
                <h1>Hello, {{ auth()->user()->f_name }}!</h1>
                <p>You're enrolled in {{ $enrolledCourses->count() }} courses</p>
            </div>
        </div>
        <div class="header-actions">
            <span class="badge badge-success">
                <i class="fas fa-user-graduate me-1"></i> Student
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
                <div class="stat-icon icon-courses">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-check-circle text-success me-1"></i> Active enrollments
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $completedCourses }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-icon icon-users">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-trophy text-warning me-1"></i> Courses finished
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ number_format($averageGrade, 1) }}</div>
                    <div class="stat-label">Average Grade</div>
                </div>
                <div class="stat-icon icon-logins">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-percentage text-primary me-1"></i> Overall performance
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-number">{{ $attendance->where('status', 'present')->count() }}</div>
                    <div class="stat-label">Attendance %</div>
                </div>
                <div class="stat-icon icon-pending">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="text-sm text-secondary">
                <i class="fas fa-user-check text-info me-1"></i> Last 30 days
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
                    <a href="{{ route('student.courses') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($enrolledCourses->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No enrolled courses</p>
                        <p class="text-sm">Contact registrar for enrollment</p>
                    </div>
                @else
                    @foreach($enrolledCourses->take(3) as $enrollment)
                    <div class="course-item">
                        <div class="course-icon course-{{ ($loop->index % 3) + 1 }}">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">{{ $enrollment->course->course_name ?? 'Unknown Course' }}</div>
                            <div class="course-desc">{{ $enrollment->course->course_code ?? 'N/A' }} • {{ $enrollment->course->credits ?? 0 }} credits</div>
                            <div class="course-teacher">
                                <span class="badge badge-primary">Teacher: {{ $enrollment->course->teacher->f_name ?? 'TBD' }}</span>
                                <span class="ms-2">Grade: {{ $enrollment->grade ?? 'Not graded' }}</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('student.course.show', $enrollment->course_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Attendance Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Recent Attendance</h2>
                    <a href="{{ route('student.attendance') }}" class="view-all">
                        View all <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
                
                @if($attendance->isEmpty())
                    <div class="empty-state">
                        <i class="fas fa-calendar"></i>
                        <p>No attendance records</p>
                        <p class="text-sm">Attendance will appear after classes</p>
                    </div>
                @else
                    @foreach($attendance->take(5) as $record)
                    <div class="announcement-item">
                        <div class="announcement-info">
                            <span class="announcement-badge {{ $record->status == 'present' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($record->status) }}
                            </span>
                            <div class="announcement-title">
                                {{ $record->course->course_name ?? 'Course' }} • {{ $record->date->format('M d, Y') }}
                            </div>
                            <div class="announcement-text">
                                {{ $record->remarks ?? 'No remarks' }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Upcoming Assignments Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title">Upcoming Assignments</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="empty-state" style="padding: 2rem 1rem;">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No upcoming assignments</p>
                        <p class="text-sm">Check back later for new assignments</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Quick Links</h2>
                </div>
                
                <div class="space-y-4">
                    <a href="{{ route('student.timetable') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-1">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Class Timetable</div>
                            <div class="course-desc">View your schedule</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('student.grades') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-2">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">My Grades</div>
                            <div class="course-desc">Check your performance</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('student.assignments') }}" class="course-item" style="text-decoration: none; color: inherit;">
                        <div class="course-icon course-3">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="course-info">
                            <div class="course-name">Assignments</div>
                            <div class="course-desc">View all assignments</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2024 School Management System. Student Portal</p>
        <p class="text-sm mt-1">Academic Year: {{ now()->format('Y') }}-{{ now()->addYear()->format('Y') }}</p>
    </footer>
@endsection

@push('styles')
<style>
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    .badge-success {
        background: #dcfce7;
        color: #065f46;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }
</style>
@endpush
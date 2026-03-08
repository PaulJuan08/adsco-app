@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                    @elseif(auth()->user()->sex === 'female')
                        <i class="fas fa-person-dress"></i>
                    @else
                        <i class="fas fa-person"></i>
                    @endif
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

    {{-- ── COURSE CHARTS ────────────────────────────────────────── --}}
    <div class="teacher-charts-grid">

        {{-- Course Performance: avg quiz score per course --}}
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar" style="color:var(--primary); margin-right:0.5rem;"></i>
                    Course Performance
                </h2>
                <span style="font-size:0.7rem; color:var(--gray-400);">Avg quiz score per course</span>
            </div>
            <div class="card-body">
                @if($courseChartData->count() > 0)
                    <canvas id="courseChart"></canvas>
                @else
                    <div class="chart-empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <p>No quiz data yet</p>
                        <small>Scores appear once students complete quizzes</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Assignment Submission Status --}}
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-tasks" style="color:#f59e0b; margin-right:0.5rem;"></i>
                    Assignment Submissions
                </h2>
                <span style="font-size:0.7rem; color:var(--gray-400);">Status overview</span>
            </div>
            <div class="card-body chart-donut-body">
                @php $totalSubs = array_sum($submissionStats); @endphp
                @if($totalSubs > 0)
                    <canvas id="submissionChart" style="max-width:180px; max-height:180px;"></canvas>
                    <div class="donut-legend">
                        <div><span class="legend-dot" style="background:#ef4444;"></span>Pending: {{ $submissionStats['pending'] }}</div>
                        <div><span class="legend-dot" style="background:#f59e0b;"></span>Submitted: {{ $submissionStats['submitted'] }}</div>
                        <div><span class="legend-dot" style="background:#10b981;"></span>Graded: {{ $submissionStats['graded'] }}</div>
                    </div>
                @else
                    <div class="chart-empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No submissions yet</p>
                    </div>
                @endif
            </div>
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
                            <div class="list-item clickable-row" onclick="window.location='{{ route('teacher.courses.show', Crypt::encrypt($course->id)) }}'">
                                <div class="item-avatar" style="border-radius: var(--radius);">
                                    <i class="fas fa-book-open"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $course->course_name ?? $course->title }}</div>
                                    <div class="item-details">{{ $course->course_code }} • {{ $course->credits ?? 0 }} credits</div>
                                    <div class="item-meta">
                                        @if($course->schedule)
                                        <span class="item-badge badge-primary">
                                            {{ $course->schedule }}
                                        </span>
                                        @endif
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-users"></i> {{ $course->enrollments_count ?? 0 }} students
                                        </span>
                                    </div>
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
                                        @if($quiz->due_date)
                                        <span class="item-badge badge-secondary">
                                            <i class="fas fa-calendar"></i> Due: {{ $quiz->due_date->format('M d, Y') }}
                                        </span>
                                        @endif
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

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<style>
.separator { opacity: 0.5; margin: 0 0.5rem; }
.clickable-row { cursor: pointer; transition: background 0.15s, transform 0.1s; }
.clickable-row:hover { background: var(--gray-50, #f9fafb); transform: translateX(2px); }
.teacher-charts-grid {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
.chart-card { margin-bottom: 0 !important; }
.chart-empty-state {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 160px; gap: 0.4rem;
}
.chart-empty-state i { font-size: 2.5rem; color: var(--gray-300); }
.chart-empty-state p { font-size: 0.875rem; color: var(--gray-500); margin: 0; }
.chart-empty-state small { font-size: 0.75rem; color: var(--gray-400); }
.chart-donut-body { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.donut-legend { display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.8rem; color: var(--gray-600); }
.donut-legend div { display: flex; align-items: center; gap: 0.4rem; }
.legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
@media (max-width: 768px) { .teacher-charts-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const P = { brown: '#552b20', gold: '#c49a24', orange: '#d3541b', teal: '#2a8a72', blue: '#2d7fa8', neutral: '#e8ddd9' };
    const tipOpts = { backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 6 };

    @if($courseChartData->count() > 0)
    (function () {
        const data = @json($courseChartData);
        const bg = data.map(d => d.avg_score >= 70 ? P.teal : d.avg_score >= 50 ? P.gold : P.orange);
        new Chart(document.getElementById('courseChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.title),
                datasets: [{
                    label: 'Avg Quiz Score (%)',
                    data: data.map(d => d.avg_score),
                    backgroundColor: bg,
                    borderWidth: 0,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tipOpts, callbacks: { label: c => [`  Avg score: ${c.parsed.x}%`, `  Enrolled: ${data[c.dataIndex].students}`] } }
                },
                scales: {
                    x: { min: 0, max: 100, grid: { color: 'rgba(0,0,0,0.05)' }, border: { display: false }, ticks: { callback: v => v + '%', color: '#94a3b8', font: { size: 11 } } },
                    y: { grid: { display: false }, border: { display: false }, ticks: { color: '#64748b', font: { size: 11 } } }
                }
            }
        });
    })();
    @endif

    @php $totalSubs = array_sum($submissionStats); @endphp
    @if($totalSubs > 0)
    (function () {
        const stats = @json($submissionStats);
        new Chart(document.getElementById('submissionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Submitted', 'Graded'],
                datasets: [{
                    data: [stats.pending || 0, stats.submitted || 0, stats.graded || 0],
                    backgroundColor: [P.orange, P.gold, P.teal],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: true, cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tipOpts, callbacks: { label: c => `  ${c.label}: ${c.parsed}` } }
                }
            }
        });
    })();
    @endif
});
</script>
@endpush
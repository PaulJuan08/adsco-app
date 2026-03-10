{{-- resources/views/student/dashboard.blade.php --}}

@extends('layouts.student')

@section('title', 'Student Dashboard')

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
    <div class="stats-grid stats-grid-compact">
        <a href="{{ route('student.courses.index') }}" class="stat-card stat-card-primary clickable-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Enrolled Courses</div>
                    <div class="stat-number">{{ $stats['total_courses'] ?? 0 }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
            <div class="stat-link">
                View courses <i class="fas fa-arrow-right"></i>
            </div>
        </a>

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
            <div class="stat-info">
                <i class="fas fa-graduation-cap"></i> {{ $stats['completed_courses'] ?? 0 }}/{{ $stats['total_courses'] ?? 0 }} courses
            </div>
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
            <div class="stat-info">
                <i class="fas fa-list-check"></i> {{ $stats['completed_topics'] ?? 0 }}/{{ $stats['total_topics'] ?? 0 }} topics
            </div>
        </div>
    </div>

    {{-- ── PERFORMANCE CHARTS ─────────────────────────────────── --}}
    <div class="student-charts-grid">

        {{-- Quiz Performance --}}
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar" style="color:var(--primary); margin-right:0.5rem;"></i>
                    Quiz Performance
                </h2>
                @if($quizChartData->count() > 0)
                <span style="font-size:0.7rem; color:var(--gray-400);">Last {{ $quizChartData->count() }} attempt(s)</span>
                @endif
            </div>
            <div class="card-body">
                @if($quizChartData->count() > 0)
                    <canvas id="quizChart"></canvas>
                @else
                    <div class="chart-empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>No quiz attempts yet</p>
                        <small>Take a quiz to see your performance here</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Assignment Scores --}}
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-tasks" style="color:#10b981; margin-right:0.5rem;"></i>
                    Assignment Scores
                </h2>
                @if($assignmentChartData->count() > 0)
                <span style="font-size:0.7rem; color:var(--gray-400);">Last {{ $assignmentChartData->count() }} graded</span>
                @endif
            </div>
            <div class="card-body">
                @if($assignmentChartData->count() > 0)
                    <canvas id="assignmentChart"></canvas>
                @else
                    <div class="chart-empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>No graded assignments yet</p>
                        <small>Submit assignments to see your scores here</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Topic Progress Doughnut --}}
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-chart-pie" style="color:#f59e0b; margin-right:0.5rem;"></i>
                    Topic Progress
                </h2>
            </div>
            <div class="card-body chart-donut-body">
                @php $chartTotalTopics = $stats['total_topics'] ?? 0; @endphp
                @if($chartTotalTopics > 0)
                    <canvas id="progressDonut" style="max-width:160px; max-height:160px;"></canvas>
                    <div class="donut-legend">
                        <div>
                            <span class="legend-dot" style="background:#10b981;"></span>
                            Completed: {{ $stats['completed_topics'] ?? 0 }}
                        </div>
                        <div>
                            <span class="legend-dot" style="background:#e2e8f0;"></span>
                            Remaining: {{ $chartTotalTopics - ($stats['completed_topics'] ?? 0) }}
                        </div>
                        <div style="margin-top:0.5rem; font-weight:700; color:var(--primary); font-size:0.9rem;">
                            {{ $completionRate }}% Complete
                        </div>
                    </div>
                @else
                    <div class="chart-empty-state">
                        <i class="fas fa-list-check"></i>
                        <p>No topics enrolled yet</p>
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
                            <p class="empty-text">You don't have any enrolled courses yet. Please contact the administrator to enroll you in courses.</p>
                            <div class="empty-hint">
                                <i class="fas fa-info-circle"></i>
                                Only administrators can enroll students in courses.
                            </div>
                        </div>
                    @else
                        <div class="items-list">
                            @foreach($enrolledCourses as $enrollment)
                            @php
                                $course = $enrollment->course;
                                $encryptedId = Crypt::encrypt($course->id);
                                $progressPercentage = $enrollment->progress ?? 0;
                                $completedTopics = $course->completed_topics ?? 0;
                                $totalTopics = $course->total_topics ?? $course->topics_count ?? 0;
                            @endphp
                            
                            <div class="list-item clickable-row" onclick="window.location='{{ route('student.courses.show', Crypt::encrypt($course->id)) }}'">
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
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🔥 REMOVED: Available Courses Card section --}}
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
                                    $courseProgressPercentage = $enrollment->progress ?? 0;
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
                                        {{ $course->completed_topics ?? 0 }}/{{ $course->total_topics ?? 0 }} topics
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
                    </div>
                </div>
            </div>

            <!-- Upcoming Deadlines Card -->
            @if(!empty($upcomingQuizzes) && $upcomingQuizzes->count() > 0)
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
                                        {{ $quiz->due_date ? $quiz->due_date->format('M d, Y') : 'No deadline' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
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
.student-charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
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
@media (max-width: 1024px) { .student-charts-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 640px)  { .student-charts-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const P = { brown: '#552b20', gold: '#c49a24', orange: '#d3541b', teal: '#2a8a72', blue: '#2d7fa8', neutral: '#e8ddd9' };
    const tipOpts = { backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569', borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 6 };
    const scaleOpts = {
        y: { min: 0, max: 100, grid: { color: 'rgba(0,0,0,0.05)' }, border: { display: false }, ticks: { callback: v => v + '%', color: '#94a3b8', font: { size: 11 } } },
        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } }
    };

    @if($quizChartData->count() > 0)
    (function () {
        const data = @json($quizChartData);
        new Chart(document.getElementById('quizChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{ label: 'Score', data: data.map(d => d.percentage), backgroundColor: data.map(d => d.passed ? P.teal : P.orange), borderWidth: 0, borderRadius: 5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tipOpts, callbacks: { label: c => [`  ${c.parsed.y}%`, data[c.dataIndex].passed ? '  ✓ Passed' : '  ✗ Failed'] } }
                },
                scales: scaleOpts
            }
        });
    })();
    @endif

    @if($assignmentChartData->count() > 0)
    (function () {
        const data = @json($assignmentChartData);
        new Chart(document.getElementById('assignmentChart'), {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{ label: 'Score', data: data.map(d => d.percentage), backgroundColor: data.map(d => d.percentage >= 70 ? P.teal : d.percentage >= 50 ? P.gold : P.orange), borderWidth: 0, borderRadius: 5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tipOpts, callbacks: { label: c => [`  ${data[c.dataIndex].score}/${data[c.dataIndex].total} pts (${c.parsed.y}%)`] } }
                },
                scales: scaleOpts
            }
        });
    })();
    @endif

    @php $chartTotalTopics = $stats['total_topics'] ?? 0; @endphp
    @if($chartTotalTopics > 0)
    (function () {
        const done = {{ $stats['completed_topics'] ?? 0 }};
        const left = {{ $chartTotalTopics - ($stats['completed_topics'] ?? 0) }};
        new Chart(document.getElementById('progressDonut'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Remaining'],
                datasets: [{ data: [done, left], backgroundColor: [P.brown, P.neutral], borderWidth: 3, borderColor: '#fff', hoverOffset: 5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true, cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tipOpts, callbacks: { label: c => `  ${c.label}: ${c.parsed} topic(s)` } }
                }
            }
        });
    })();
    @endif
});
</script>
@endpush
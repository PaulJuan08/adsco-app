@extends('layouts.admin')

@section('title', 'Analytics')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endpush

@section('content')
<div class="analytics-page">

    {{-- Header --}}
    <div class="analytics-header no-print">
        <div class="analytics-header-left">
            <div class="analytics-header-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <div class="analytics-header-title">Analytics</div>
                <div class="analytics-header-sub">Platform-wide progress and performance overview</div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="analytics-tabs no-print">
        <a href="{{ route('admin.analytics.index', ['tab' => 'overview'] + request()->except('tab', 'page')) }}"
           class="analytics-tab {{ $activeTab === 'overview' ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Overview
        </a>
        <a href="{{ route('admin.analytics.index', ['tab' => 'students'] + request()->except('tab', 'page')) }}"
           class="analytics-tab tab-students {{ $activeTab === 'students' ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <a href="{{ route('admin.analytics.index', ['tab' => 'teachers'] + request()->except('tab', 'page')) }}"
           class="analytics-tab tab-teachers {{ $activeTab === 'teachers' ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher"></i> Teachers
        </a>
        <button onclick="window.print()" class="btn-analytics btn-analytics-print ms-auto no-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    {{-- ═══════════════════════ OVERVIEW TAB ═══════════════════════ --}}
    @if($activeTab === 'overview')

    {{-- Stats Grid --}}
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Students</div>
                    <div class="stat-number">{{ number_format($totalStudents) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Teachers</div>
                    <div class="stat-number">{{ number_format($totalTeachers) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Courses</div>
                    <div class="stat-number">{{ number_format($totalCourses) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-book"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Topics</div>
                    <div class="stat-number">{{ number_format($totalTopics) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes</div>
                    <div class="stat-number">{{ number_format($totalQuizzes) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-question-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Assignments</div>
                    <div class="stat-number">{{ number_format($totalAssignments) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
    </div>

    {{-- Charts Grid --}}
    <div class="analytics-charts-grid">

        {{-- Trends (merged line charts) --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-line"></i> Monthly Trends (6 months)</div>
            </div>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Distributions (grouped doughnuts) --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-pie"></i> Distributions</div>
            </div>
            <div class="doughnut-group">
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Topic Completion</div>
                    <div class="doughnut-container">
                        @if($topicCompletionStats->sum() > 0)
                        <canvas id="topicCompletionChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-layer-group"></i><p>No progress yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Quiz Pass / Fail</div>
                    <div class="doughnut-container">
                        @if($quizPassCount + $quizFailCount > 0)
                        <canvas id="quizResultChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-question-circle"></i><p>No quiz attempts yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Submission Status</div>
                    <div class="doughnut-container">
                        @if($submissionStatus->sum() > 0)
                        <canvas id="submissionStatusChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-file-alt"></i><p>No submissions yet</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bar Charts (grouped) --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-bar"></i> Rankings &amp; Submission Trends</div>
            </div>
            <div class="bar-group">
                <div class="bar-section">
                    <div class="bar-section-label">Assignment Submissions (Monthly)</div>
                    <div class="chart-container">
                        <canvas id="submissionsTrendChart"></canvas>
                    </div>
                </div>
                <div class="bar-section">
                    <div class="bar-section-label">Top 10 Students by Avg Quiz Score</div>
                    <div class="chart-container" style="min-height:{{ max(200, $topStudents->count() * 38 + 40) }}px">
                        @if($topStudents->count() > 0)
                        <canvas id="topStudentsChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-users"></i><p>No quiz attempts yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="bar-section">
                    <div class="bar-section-label">Top Courses by Enrollment</div>
                    <div class="chart-container" style="min-height:{{ max(200, $topCourses->count() * 38 + 40) }}px">
                        @if($topCourses->count() > 0)
                        <canvas id="topCoursesChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-book"></i><p>No enrollments yet</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════ STUDENTS TAB ═══════════════════════ --}}
    @elseif($activeTab === 'students')

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.analytics.index') }}" class="analytics-filter-bar no-print">
        <input type="hidden" name="tab" value="students">
        <div class="filter-group">
            <label class="filter-label">College</label>
            <select name="college_id" id="filterCollege" onchange="this.form.submit()">
                <option value="">All Colleges</option>
                @foreach($colleges as $c)
                <option value="{{ $c->id }}" {{ $collegeId == $c->id ? 'selected' : '' }}>{{ $c->college_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Program</label>
            <select name="program_id" onchange="this.form.submit()">
                <option value="">All Programs</option>
                @foreach($programs as $p)
                <option value="{{ $p->id }}" {{ $programId == $p->id ? 'selected' : '' }}>{{ $p->program_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Year</label>
            <select name="year" onchange="this.form.submit()">
                <option value="">All Years</option>
                @foreach($years as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <label class="filter-label">Search</label>
            <input type="text" name="search_name" placeholder="Name or Student ID" value="{{ $searchName }}"
                   onkeyup="clearTimeout(window._st); window._st=setTimeout(()=>this.form.submit(),400)">
        </div>
        @if($collegeId || $programId || $year || $searchName)
        <div class="filter-group" style="justify-content:flex-end">
            <a href="{{ route('admin.analytics.index', ['tab'=>'students']) }}" class="btn-analytics btn-analytics-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
        @endif
    </form>

    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-user-graduate"></i> Students</div>
            <span style="font-size:0.8rem;color:#4b5563">{{ $students->total() }} total</span>
        </div>
        @if($students->count() > 0)
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>ID</th>
                        <th>College / Program</th>
                        <th>Year</th>
                        <th>Courses</th>
                        <th>Quiz Attempts</th>
                        <th>Submissions</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td class="td-name">{{ $student->f_name }} {{ $student->l_name }}</td>
                        <td class="td-muted">{{ $student->student_id ?? '—' }}</td>
                        <td>
                            <div style="font-size:0.82rem">{{ $student->college->college_name ?? '—' }}</div>
                            <div class="td-muted">{{ $student->program->program_name ?? '—' }}</div>
                        </td>
                        <td class="td-muted">{{ $student->college_year ? 'Year '.$student->college_year : '—' }}</td>
                        <td>{{ $student->courses_count }}</td>
                        <td>{{ $student->quiz_attempts_count }}</td>
                        <td>{{ $student->submissions_count }}</td>
                        <td>
                            <a href="{{ route('admin.analytics.student', Crypt::encrypt($student->id)) }}"
                               class="btn-analytics btn-analytics-primary" style="font-size:0.78rem;padding:0.3rem 0.65rem">
                                <i class="fas fa-chart-bar"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem">{{ $students->links() }}</div>
        @else
        <div class="analytics-empty"><i class="fas fa-users"></i><p>No students found</p></div>
        @endif
    </div>

    {{-- ═══════════════════════ TEACHERS TAB ═══════════════════════ --}}
    @elseif($activeTab === 'teachers')

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.analytics.index') }}" class="analytics-filter-bar no-print">
        <input type="hidden" name="tab" value="teachers">
        <div class="filter-group">
            <label class="filter-label">Search</label>
            <input type="text" name="search_name" placeholder="Name or Employee ID" value="{{ $searchName }}"
                   onkeyup="clearTimeout(window._st); window._st=setTimeout(()=>this.form.submit(),400)">
        </div>
        @if($searchName)
        <div class="filter-group" style="justify-content:flex-end">
            <a href="{{ route('admin.analytics.index', ['tab'=>'teachers']) }}" class="btn-analytics btn-analytics-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
        @endif
    </form>

    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-chalkboard-teacher"></i> Teachers</div>
            <span style="font-size:0.8rem;color:#4b5563">{{ $teachers->total() }} total</span>
        </div>
        @if($teachers->count() > 0)
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Employee ID</th>
                        <th>College</th>
                        <th>Courses</th>
                        <th>Topics</th>
                        <th>Quizzes</th>
                        <th>Assignments</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    <tr>
                        <td class="td-name">{{ $teacher->f_name }} {{ $teacher->l_name }}</td>
                        <td class="td-muted">{{ $teacher->employee_id ?? '—' }}</td>
                        <td class="td-muted">{{ $teacher->college->college_name ?? '—' }}</td>
                        <td>{{ $teacher->courses_count }}</td>
                        <td>{{ $teacher->topics_count }}</td>
                        <td>{{ $teacher->quizzes_count }}</td>
                        <td>{{ $teacher->assignments_count }}</td>
                        <td>
                            <a href="{{ route('admin.analytics.teacher', Crypt::encrypt($teacher->id)) }}"
                               class="btn-analytics btn-analytics-primary" style="font-size:0.78rem;padding:0.3rem 0.65rem">
                                <i class="fas fa-chart-bar"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem">{{ $teachers->links() }}</div>
        @else
        <div class="analytics-empty"><i class="fas fa-chalkboard-teacher"></i><p>No teachers found</p></div>
        @endif
    </div>

    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($activeTab === 'overview')
<script>
const P = {
    brown: '#552b20', gold: '#ddb238', orange: '#d3541b',
    teal: '#2a8a72', green: '#48bb78', red: '#f56565',
    blue: '#4299e1', purple: '#9f7aea', gray: '#a0aec0',
};
const tipOpts = {
    backgroundColor: '#fff', titleColor: '#1e293b', bodyColor: '#475569',
    borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8,
};
const monthLabels = {!! json_encode($monthlyEnrollments->keys()->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))) !!};

// ── Monthly Trends (combined line chart)
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [
            {
                label: 'Enrollments',
                data: {!! json_encode($monthlyEnrollments->values()) !!},
                borderColor: P.brown, backgroundColor: 'rgba(85,43,32,0.08)',
                tension: 0.4, fill: true, pointBackgroundColor: P.brown, pointRadius: 5,
            },
            {
                label: 'Quiz Attempts',
                data: {!! json_encode($monthlyAttempts->values()) !!},
                borderColor: P.gold, backgroundColor: 'rgba(221,178,56,0.08)',
                tension: 0.4, fill: true, pointBackgroundColor: P.gold, pointRadius: 5,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'top' }, tooltip: tipOpts },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── Submissions Trend
new Chart(document.getElementById('submissionsTrendChart'), {
    type: 'bar',
    data: {
        labels: monthLabels,
        datasets: [{
            label: 'Submissions',
            data: {!! json_encode($monthlySubmissions->values()) !!},
            backgroundColor: P.teal, borderRadius: 4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tipOpts },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

@if($topicCompletionStats->sum() > 0)
// ── Topic Completion
new Chart(document.getElementById('topicCompletionChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Incomplete'],
        datasets: [{
            data: [
                {{ $topicCompletionStats['completed'] ?? 0 }},
                {{ $topicCompletionStats['in_progress'] ?? 0 }},
                {{ $topicCompletionStats['incomplete'] ?? 0 }},
            ],
            backgroundColor: [P.green, P.gold, P.gray],
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '65%',
        plugins: { legend: { position: 'bottom' }, tooltip: tipOpts }
    }
});
@endif

@if($quizPassCount + $quizFailCount > 0)
// ── Quiz Pass/Fail
new Chart(document.getElementById('quizResultChart'), {
    type: 'doughnut',
    data: {
        labels: ['Passed', 'Failed'],
        datasets: [{
            data: [{{ $quizPassCount }}, {{ $quizFailCount }}],
            backgroundColor: [P.green, P.red], borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: { legend: { position: 'bottom' }, tooltip: tipOpts }
    }
});
@endif

@if($submissionStatus->sum() > 0)
// ── Submission Status
new Chart(document.getElementById('submissionStatusChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($submissionStatus->keys()->map(fn($k) => ucfirst($k))) !!},
        datasets: [{
            data: {!! json_encode($submissionStatus->values()) !!},
            backgroundColor: [P.blue, P.teal, P.green, P.red, P.orange],
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' }, tooltip: tipOpts }
    }
});
@endif

@if($topStudents->count() > 0)
// ── Top Students
new Chart(document.getElementById('topStudentsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($topStudents->pluck('name')) !!},
        datasets: [{
            label: 'Avg Score %',
            data: {!! json_encode($topStudents->pluck('avg_score')) !!},
            backgroundColor: {!! json_encode($topStudents->map(fn($s) => $s->avg_score >= 75 ? '#48bb78' : ($s->avg_score >= 50 ? '#ddb238' : '#f56565'))) !!},
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tipOpts },
        scales: { x: { beginAtZero: true, max: 100, ticks: { callback: v => v+'%' } } }
    }
});
@endif

@if($topCourses->count() > 0)
// ── Top Courses
new Chart(document.getElementById('topCoursesChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($topCourses->map(fn($c) => \Str::limit($c->title, 22))) !!},
        datasets: [{
            label: 'Enrollments',
            data: {!! json_encode($topCourses->pluck('enrollments_count')) !!},
            backgroundColor: P.brown, borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tipOpts },
        scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
@endif
</script>
@endif
@endpush

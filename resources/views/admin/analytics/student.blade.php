@extends('layouts.admin')

@section('title', 'Student Analytics — {{ $student->f_name }} {{ $student->l_name }}')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endpush

@section('content')
<div class="analytics-page">

    {{-- Header --}}
    <div class="analytics-header no-print">
        <div class="analytics-header-left">
            <a href="{{ route('admin.analytics.index', ['tab' => 'students']) }}"
               class="btn-analytics btn-analytics-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="analytics-header-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="analytics-header-title">Student Analytics</div>
                <div class="analytics-header-sub">Individual performance overview</div>
            </div>
        </div>
        <div class="analytics-header-actions">
            <button onclick="window.print()" class="btn-analytics btn-analytics-secondary no-print">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- Student Profile --}}
    <div class="profile-header-card">
        <div class="profile-header-avatar">
            @if($student->avatar)
                <img src="{{ asset('storage/' . $student->avatar) }}" alt="Avatar">
            @else
                {{ strtoupper(substr($student->f_name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-header-info">
            <div class="profile-header-name">{{ $student->f_name }} {{ $student->l_name }}</div>
            <div class="profile-header-meta">
                @if($student->student_id)
                <span class="profile-meta-item"><i class="fas fa-id-card"></i> {{ $student->student_id }}</span>
                @endif
                <span class="profile-meta-item"><i class="fas fa-envelope"></i> {{ $student->email }}</span>
                @if($student->college)
                <span class="profile-meta-item"><i class="fas fa-university"></i> {{ $student->college->college_name }}</span>
                @endif
                @if($student->program)
                <span class="profile-meta-item"><i class="fas fa-graduation-cap"></i> {{ $student->program->program_name }}</span>
                @endif
                @if($student->college_year)
                <span class="profile-meta-item"><i class="fas fa-calendar"></i> Year {{ $student->college_year }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Enrolled Courses</div>
                    <div class="stat-number">{{ $enrolledCourses->count() }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Topics Completed</div>
                    <div class="stat-number">{{ $enrolledCourses->sum('completed') }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quiz Attempts</div>
                    <div class="stat-number">{{ $quizAttempts->count() }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-question-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes Passed</div>
                    <div class="stat-number">{{ $quizPassCount }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Assignments Submitted</div>
                    <div class="stat-number">{{ $submissions->count() }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        @if($quizAttempts->count() > 0)
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Quiz Score</div>
                    <div class="stat-number">{{ round($quizAttempts->avg('percentage'), 1) }}%</div>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
        @endif
        @if($avgAssignmentScore !== null)
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Assignment Score</div>
                    <div class="stat-number">{{ $avgAssignmentScore }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-medal"></i></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Charts Grid --}}
    <div class="analytics-charts-grid">

        {{-- Quiz Score Trend --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-line"></i> Quiz Score Trend</div>
            </div>
            <div class="chart-container">
                @if($quizTrend->count() > 0)
                <canvas id="quizTrendChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-chart-line"></i><p>No quiz attempts yet</p></div>
                @endif
            </div>
        </div>

        {{-- Course Topic Progress --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-tasks"></i> Course Topic Completion</div>
            </div>
            <div class="chart-container" style="min-height:{{ max(180, $courseTopicProgress->count() * 38 + 40) }}px">
                @if($courseTopicProgress->count() > 0)
                <canvas id="courseProgressChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-book"></i><p>Not enrolled in any courses</p></div>
                @endif
            </div>
        </div>

        {{-- Avg Score per Course --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-bar"></i> Avg Quiz Score per Course</div>
            </div>
            <div class="chart-container" style="min-height:{{ max(180, $avgScorePerCourse->count() * 38 + 40) }}px">
                @if($avgScorePerCourse->count() > 0)
                <canvas id="avgScoreChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-chart-bar"></i><p>No quiz data</p></div>
                @endif
            </div>
        </div>

        {{-- Quiz Pass/Fail --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-clipboard-check"></i> Quiz Results</div>
            </div>
            <div class="chart-container">
                @if($quizAttempts->count() > 0)
                <canvas id="quizResultChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-question-circle"></i><p>No attempts yet</p></div>
                @endif
            </div>
        </div>

        {{-- Submission Status --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-file-upload"></i> Assignment Submissions</div>
            </div>
            <div class="chart-container">
                @if($submissions->count() > 0)
                <canvas id="submissionStatusChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-file-alt"></i><p>No submissions yet</p></div>
                @endif
            </div>
        </div>

    </div>

    {{-- Course Progress List --}}
    @if($enrolledCourses->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-book-open"></i> Course Progress</div>
        </div>
        <div style="padding:1rem 1.25rem">
            <div class="course-progress-list">
                @foreach($enrolledCourses as $ec)
                <div class="course-progress-item">
                    <div class="course-progress-top">
                        <div class="course-progress-name">{{ $ec['course']->title }}</div>
                        <div class="course-progress-pct">{{ $ec['pct'] }}%</div>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill @if($ec['pct']>=70)fill-good @elseif($ec['pct']>=40)fill-medium @else fill-low @endif"
                             style="width:{{ $ec['pct'] }}%"></div>
                    </div>
                    <div class="course-progress-meta">
                        <span>{{ $ec['completed'] }} completed</span>
                        <span>{{ $ec['in_progress'] }} in progress</span>
                        <span>{{ $ec['not_started'] }} not started</span>
                        <span>/ {{ $ec['total'] }} total topics</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Quiz Attempts Table --}}
    @if($quizAttempts->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-question-circle"></i> Quiz Attempts</div>
            <span style="font-size:0.8rem;color:var(--gray-500)">{{ $quizAttempts->count() }} total</span>
        </div>
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr><th>Quiz</th><th>Course</th><th>Score</th><th>%</th><th>Result</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @foreach($quizAttempts->sortByDesc('completed_at') as $attempt)
                    <tr>
                        <td class="td-name">{{ $attempt->quiz->title ?? 'N/A' }}</td>
                        <td>{{ $attempt->quiz->course->title ?? '—' }}</td>
                        <td>{{ $attempt->score ?? 0 }} / {{ $attempt->total_points ?? 0 }}</td>
                        <td><strong>{{ round($attempt->percentage, 1) }}%</strong></td>
                        <td>
                            @if($attempt->passed)
                            <span class="badge-a badge-pass"><i class="fas fa-check"></i> Passed</span>
                            @else
                            <span class="badge-a badge-fail"><i class="fas fa-times"></i> Failed</span>
                            @endif
                        </td>
                        <td class="td-muted">{{ $attempt->completed_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Assignment Submissions Table --}}
    @if($submissions->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-file-alt"></i> Assignment Submissions</div>
            <span style="font-size:0.8rem;color:var(--gray-500)">{{ $submissions->count() }} total</span>
        </div>
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr><th>Assignment</th><th>Course</th><th>Score</th><th>Status</th><th>Submitted</th></tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    <tr>
                        <td class="td-name">{{ $sub->assignment->title ?? 'N/A' }}</td>
                        <td>{{ $sub->assignment->course->title ?? '—' }}</td>
                        <td>{{ $sub->score !== null ? $sub->score : '—' }}</td>
                        <td>
                            <span class="badge-a badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                        </td>
                        <td class="td-muted">{{ $sub->submitted_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const P = { brown:'#552b20', gold:'#ddb238', orange:'#d3541b', teal:'#2a8a72', green:'#48bb78', red:'#f56565', blue:'#4299e1', gray:'#a0aec0' };
const tipOpts = { backgroundColor:'#fff', titleColor:'#1e293b', bodyColor:'#475569', borderColor:'#e2e8f0', borderWidth:1, padding:10, cornerRadius:8 };

@if($quizTrend->count() > 0)
new Chart(document.getElementById('quizTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($quizTrend->pluck('date')) !!},
        datasets: [{
            label: 'Score %',
            data: {!! json_encode($quizTrend->pluck('score')) !!},
            borderColor: P.brown, backgroundColor: 'rgba(85,43,32,0.08)',
            tension: 0.4, fill: true, pointBackgroundColor: {!! json_encode($quizTrend->map(fn($t) => $t['score'] >= 75 ? '#48bb78' : ($t['score'] >= 50 ? '#ddb238' : '#f56565'))) !!},
            pointRadius: 6, pointHoverRadius: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { ...tipOpts, callbacks: { afterLabel: ctx => {
            const quizzes = {!! json_encode($quizTrend->pluck('quiz')) !!};
            return quizzes[ctx.dataIndex] ?? '';
        }}}},
        scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v+'%' } } }
    }
});
@endif

@if($courseTopicProgress->count() > 0)
new Chart(document.getElementById('courseProgressChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($courseTopicProgress->pluck('course')) !!},
        datasets: [{
            label: 'Completion %',
            data: {!! json_encode($courseTopicProgress->pluck('pct')) !!},
            backgroundColor: {!! json_encode($courseTopicProgress->map(fn($c) => $c['pct']>=70 ? '#48bb78' : ($c['pct']>=40 ? '#ddb238' : '#f56565'))->values()) !!},
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

@if($avgScorePerCourse->count() > 0)
new Chart(document.getElementById('avgScoreChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($avgScorePerCourse->keys()) !!},
        datasets: [{
            label: 'Avg Score %',
            data: {!! json_encode($avgScorePerCourse->values()) !!},
            backgroundColor: P.gold, borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: tipOpts },
        scales: { x: { beginAtZero: true, max: 100, ticks: { callback: v => v+'%' } } }
    }
});
@endif

@if($quizAttempts->count() > 0)
new Chart(document.getElementById('quizResultChart'), {
    type: 'doughnut',
    data: {
        labels: ['Passed', 'Failed'],
        datasets: [{ data: [{{ $quizPassCount }}, {{ $quizFailCount }}], backgroundColor: [P.green, P.red], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, maintainAspectRatio: true, cutout: '65%', plugins: { legend: { position: 'bottom' }, tooltip: tipOpts } }
});
@endif

@if($submissions->count() > 0)
new Chart(document.getElementById('submissionStatusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($submissionStatus->keys()->map(fn($k) => ucfirst($k))) !!},
        datasets: [{ data: {!! json_encode($submissionStatus->values()) !!}, backgroundColor: [P.blue, P.teal, P.red, P.orange], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { responsive: true, maintainAspectRatio: true, cutout: '65%', plugins: { legend: { position: 'bottom' }, tooltip: tipOpts } }
});
@endif
</script>
@endpush

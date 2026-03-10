@extends('layouts.student')

@section('title', 'My Progress')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endpush

@section('content')
<div class="analytics-page">

    {{-- Header --}}
    <div class="analytics-header no-print">
        <div class="analytics-header-left">
            <div class="analytics-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="analytics-header-title">My Progress</div>
                <div class="analytics-header-sub">Your courses, topics, quizzes and assignments overview</div>
            </div>
        </div>
        <button onclick="window.print()" class="btn-analytics btn-analytics-print no-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    {{-- Stats Cards --}}
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
                    <div class="stat-number">{{ $completedCount }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Topics In Progress</div>
                    <div class="stat-number">{{ $inProgressCount }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-spinner"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes Taken</div>
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
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Assignments Submitted</div>
                    <div class="stat-number">{{ $submissions->count() }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        @if($avgScore !== null)
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Quiz Score</div>
                    <div class="stat-number">{{ $avgScore }}%</div>
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

        {{-- Line Chart --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-line"></i> Quiz Score Trend (Last 10 Attempts)</div>
            </div>
            <div class="chart-container">
                @if($quizTrend->count() > 0)
                <canvas id="quizTrendChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-chart-line"></i><p>No quiz attempts yet</p></div>
                @endif
            </div>
        </div>

        {{-- Distributions (grouped doughnuts) --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-pie"></i> Distributions</div>
            </div>
            <div class="doughnut-group">
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Topic Progress</div>
                    <div class="doughnut-container">
                        @if($totalTopics > 0)
                        <canvas id="topicProgressChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-layer-group"></i><p>No topics yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Quiz Results</div>
                    <div class="doughnut-container">
                        @if($quizAttempts->count() > 0)
                        <canvas id="quizResultChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-question-circle"></i><p>No quiz attempts yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Assignment Submissions</div>
                    <div class="doughnut-container">
                        @if($submissions->count() > 0)
                        <canvas id="submissionStatusChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-file-alt"></i><p>No submissions yet</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Bar Chart --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-tasks"></i> Course Completion Progress</div>
            </div>
            <div class="chart-container" style="min-height:{{ max(180, $courseTopicProgress->count() * 40 + 40) }}px">
                @if($courseTopicProgress->count() > 0)
                <canvas id="courseProgressChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-book"></i><p>Not enrolled in any courses</p></div>
                @endif
            </div>
        </div>

    </div>

    {{-- Course Progress Cards --}}
    @if($enrolledCourses->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-book-open"></i> My Courses Progress</div>
        </div>
        <div style="padding: 1rem 1.25rem;">
            <div class="course-progress-list">
                @foreach($enrolledCourses as $ec)
                <div class="course-progress-item">
                    <div class="course-progress-top">
                        <div class="course-progress-name">{{ $ec['course']->title }}</div>
                        <div class="course-progress-pct">{{ $ec['pct'] }}%</div>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill @if($ec['pct'] >= 70) fill-good @elseif($ec['pct'] >= 40) fill-medium @else fill-low @endif"
                             style="width: {{ $ec['pct'] }}%"></div>
                    </div>
                    <div class="course-progress-meta">
                        <span><i class="fas fa-check"></i> {{ $ec['completed'] }} completed</span>
                        <span><i class="fas fa-spinner"></i> {{ $ec['in_progress'] }} in progress</span>
                        <span><i class="fas fa-circle text-muted"></i> {{ $ec['not_started'] }} not started</span>
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
                    <tr>
                        <th>Quiz</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>%</th>
                        <th>Result</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizAttempts as $attempt)
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
                        <td class="td-muted">{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : '—' }}</td>
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
                    <tr>
                        <th>Assignment</th>
                        <th>Course</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    <tr>
                        <td class="td-name">{{ $sub->assignment->title ?? 'N/A' }}</td>
                        <td>{{ $sub->assignment->course->title ?? '—' }}</td>
                        <td>{{ $sub->score !== null ? $sub->score : '—' }}</td>
                        <td>
                            @php $st = $sub->status; @endphp
                            <span class="badge-a badge-{{ $st }}">
                                @if($st === 'graded') <i class="fas fa-check-circle"></i>
                                @elseif($st === 'submitted') <i class="fas fa-paper-plane"></i>
                                @elseif($st === 'late') <i class="fas fa-clock"></i>
                                @else <i class="fas fa-hourglass"></i>
                                @endif
                                {{ ucfirst($st) }}
                            </span>
                        </td>
                        <td class="td-muted">{{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y') : '—' }}</td>
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
const P = {
    brown:   '#552b20',
    gold:    '#ddb238',
    orange:  '#d3541b',
    teal:    '#2a8a72',
    green:   '#48bb78',
    red:     '#f56565',
    blue:    '#4299e1',
    gray:    '#a0aec0',
    neutral: '#e8ddd9',
};
const tipOpts = {
    backgroundColor: '#fff',
    titleColor: '#1e293b',
    bodyColor: '#475569',
    borderColor: '#e2e8f0',
    borderWidth: 1,
    padding: 10,
    cornerRadius: 8,
};

@if($totalTopics > 0)
new Chart(document.getElementById('topicProgressChart'), {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Not Started'],
        datasets: [{
            data: [{{ $completedCount }}, {{ $inProgressCount }}, {{ $notStartedCount }}],
            backgroundColor: [P.green, P.gold, P.gray],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { ...tipOpts },
        }
    }
});
@endif

@if($quizAttempts->count() > 0)
new Chart(document.getElementById('quizResultChart'), {
    type: 'doughnut',
    data: {
        labels: ['Passed', 'Failed'],
        datasets: [{
            data: [{{ $quizPassCount }}, {{ $quizFailCount }}],
            backgroundColor: [P.green, P.red],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { ...tipOpts },
        }
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
            backgroundColor: {!! json_encode($courseTopicProgress->map(fn($c) => $c['pct'] >= 70 ? '#48bb78' : ($c['pct'] >= 40 ? '#ddb238' : '#f56565'))->values()) !!},
            borderRadius: 5,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { ...tipOpts } },
        scales: {
            x: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
            y: { ticks: { font: { size: 12 } } }
        }
    }
});
@endif

@if($quizTrend->count() > 0)
new Chart(document.getElementById('quizTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($quizTrend->pluck('date')) !!},
        datasets: [{
            label: 'Score %',
            data: {!! json_encode($quizTrend->pluck('score')) !!},
            borderColor: P.brown,
            backgroundColor: 'rgba(85,43,32,0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: P.brown,
            pointRadius: 5,
            pointHoverRadius: 7,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                ...tipOpts,
                callbacks: {
                    afterLabel: (ctx) => {
                        const quizzes = {!! json_encode($quizTrend->pluck('quiz')) !!};
                        return quizzes[ctx.dataIndex] ?? '';
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
            x: { ticks: { font: { size: 11 } } }
        }
    }
});
@endif

@if($submissions->count() > 0)
new Chart(document.getElementById('submissionStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Submitted', 'Graded', 'Late'],
        datasets: [{
            data: [
                {{ $submissionStats['submitted'] }},
                {{ $submissionStats['graded'] }},
                {{ $submissionStats['late'] }},
            ],
            backgroundColor: [P.blue, P.teal, P.red],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { ...tipOpts },
        }
    }
});
@endif
</script>
@endpush

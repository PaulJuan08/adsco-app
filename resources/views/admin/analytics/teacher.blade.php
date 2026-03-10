@extends('layouts.admin')

@section('title', 'Teacher Analytics — {{ $teacher->f_name }} {{ $teacher->l_name }}')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
@endpush

@section('content')
<div class="analytics-page">

    {{-- Header --}}
    <div class="analytics-header no-print">
        <div class="analytics-header-left">
            <a href="{{ route('admin.analytics.index', ['tab' => 'teachers']) }}"
               class="btn-analytics btn-analytics-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="analytics-header-icon">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
                <div class="analytics-header-title">Teacher Analytics</div>
                <div class="analytics-header-sub">Content creation and student performance overview</div>
            </div>
        </div>
        <div class="analytics-header-actions">
            <button onclick="window.print()" class="btn-analytics btn-analytics-secondary no-print">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    {{-- Teacher Profile --}}
    <div class="profile-header-card">
        <div class="profile-header-avatar">
            @if($teacher->avatar)
                <img src="{{ asset('storage/' . $teacher->avatar) }}" alt="Avatar">
            @else
                {{ strtoupper(substr($teacher->f_name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-header-info">
            <div class="profile-header-name">{{ $teacher->f_name }} {{ $teacher->l_name }}</div>
            <div class="profile-header-meta">
                @if($teacher->employee_id)
                <span class="profile-meta-item"><i class="fas fa-id-badge"></i> {{ $teacher->employee_id }}</span>
                @endif
                <span class="profile-meta-item"><i class="fas fa-envelope"></i> {{ $teacher->email }}</span>
                @if($teacher->college)
                <span class="profile-meta-item"><i class="fas fa-university"></i> {{ $teacher->college->college_name }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Courses</div>
                    <div class="stat-number">{{ $courses->count() }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-book"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Students</div>
                    <div class="stat-number">{{ number_format($totalStudents) }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Topics Created</div>
                    <div class="stat-number">{{ $topicsCreated }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes Created</div>
                    <div class="stat-number">{{ $quizzesCreated }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-question-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Assignments Created</div>
                    <div class="stat-number">{{ $assignmentsCreated }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        @if($quizPassCount + $quizFailCount > 0)
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Students Passed Quiz</div>
                    <div class="stat-number">{{ $quizPassCount }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Charts Grid --}}
    <div class="analytics-charts-grid">

        {{-- Students per Course --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-users"></i> Students Enrolled per Course</div>
            </div>
            <div class="chart-container" style="min-height:{{ max(180, $enrollmentsPerCourse->count() * 38 + 40) }}px">
                @if($enrollmentsPerCourse->count() > 0)
                <canvas id="enrollmentsChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-book"></i><p>No courses yet</p></div>
                @endif
            </div>
        </div>

        {{-- Avg Quiz Score per Course --}}
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

        {{-- Monthly Quiz Attempts Trend --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-chart-line"></i> Monthly Quiz Attempts Trend</div>
            </div>
            <div class="chart-container">
                <canvas id="monthlyAttemptsChart"></canvas>
            </div>
        </div>

        {{-- Quizzes Created Monthly --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-plus-circle"></i> Quizzes Created (6 months)</div>
            </div>
            <div class="chart-container">
                <canvas id="quizCreatedChart"></canvas>
            </div>
        </div>

        {{-- Quiz Pass/Fail --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-clipboard-check"></i> Quiz Pass / Fail</div>
            </div>
            <div class="chart-container">
                @if($quizPassCount + $quizFailCount > 0)
                <canvas id="quizResultChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-question-circle"></i><p>No quiz attempts yet</p></div>
                @endif
            </div>
        </div>

        {{-- Submission Status --}}
        <div class="chart-card-a">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-file-upload"></i> Assignment Submission Status</div>
            </div>
            <div class="chart-container">
                @if($submissionStats->sum() > 0)
                <canvas id="submissionStatusChart"></canvas>
                @else
                <div class="analytics-empty"><i class="fas fa-file-alt"></i><p>No submissions yet</p></div>
                @endif
            </div>
        </div>

        {{-- Content Summary (Bar) --}}
        <div class="chart-card-a chart-full">
            <div class="chart-card-a-header">
                <div class="chart-card-a-title"><i class="fas fa-cubes"></i> Content Summary</div>
            </div>
            <div class="chart-container" style="min-height:220px">
                <canvas id="contentSummaryChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Courses Table --}}
    @if($courses->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-book"></i> Courses</div>
        </div>
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr><th>Course</th><th>Code</th><th>Topics</th><th>Quizzes</th><th>Assignments</th><th>Students</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td class="td-name">{{ $course->title }}</td>
                        <td class="td-muted">{{ $course->course_code }}</td>
                        <td>{{ $course->topics_count }}</td>
                        <td>{{ $course->quizzes_count }}</td>
                        <td>{{ $course->assignments_count }}</td>
                        <td>{{ $course->enrollments_count }}</td>
                        <td>
                            @if($course->is_published)
                            <span class="badge-a badge-graded">Published</span>
                            @else
                            <span class="badge-a badge-pending">Draft</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Students Table --}}
    @if($recentStudents->count() > 0)
    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-users"></i> Enrolled Students</div>
            <span style="font-size:0.8rem;color:var(--gray-500)">{{ $recentStudents->total() }} total</span>
        </div>
        <div style="overflow-x:auto">
            <table class="analytics-table">
                <thead>
                    <tr><th>Student</th><th>ID</th><th>Program</th><th>Year</th><th>Quiz Attempts</th><th>Submissions</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($recentStudents as $s)
                    <tr>
                        <td class="td-name">{{ $s->f_name }} {{ $s->l_name }}</td>
                        <td class="td-muted">{{ $s->student_id ?? '—' }}</td>
                        <td class="td-muted">{{ $s->program->program_name ?? '—' }}</td>
                        <td class="td-muted">{{ $s->college_year ? 'Year '.$s->college_year : '—' }}</td>
                        <td>{{ $s->quiz_attempts_count }}</td>
                        <td>{{ $s->submissions_count }}</td>
                        <td>
                            <a href="{{ route('admin.analytics.student', Crypt::encrypt($s->id)) }}"
                               class="btn-analytics btn-analytics-primary" style="font-size:0.78rem;padding:0.3rem 0.65rem">
                                <i class="fas fa-chart-bar"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:1rem 1.5rem">{{ $recentStudents->links() }}</div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const P = { brown:'#552b20', gold:'#ddb238', orange:'#d3541b', teal:'#2a8a72', green:'#48bb78', red:'#f56565', blue:'#4299e1', gray:'#a0aec0' };
const tipOpts = { backgroundColor:'#fff', titleColor:'#1e293b', bodyColor:'#475569', borderColor:'#e2e8f0', borderWidth:1, padding:10, cornerRadius:8 };
const monthLabels = {!! json_encode($monthlyAttempts->keys()->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m',$m)->format('M Y'))) !!};

@if($enrollmentsPerCourse->count() > 0)
new Chart(document.getElementById('enrollmentsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($enrollmentsPerCourse->pluck('course')) !!},
        datasets: [{ label: 'Students', data: {!! json_encode($enrollmentsPerCourse->pluck('students')) !!}, backgroundColor: P.brown, borderRadius: 5 }]
    },
    options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tipOpts }, scales:{ x:{ beginAtZero:true, ticks:{stepSize:1} } } }
});
@endif

@if($avgScorePerCourse->count() > 0)
new Chart(document.getElementById('avgScoreChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($avgScorePerCourse->pluck('course')) !!},
        datasets: [{ label: 'Avg Score %', data: {!! json_encode($avgScorePerCourse->pluck('avg')) !!}, backgroundColor: {!! json_encode($avgScorePerCourse->map(fn($c) => $c['avg']>=75?'#48bb78':($c['avg']>=50?'#ddb238':'#f56565'))) !!}, borderRadius:5 }]
    },
    options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tipOpts }, scales:{ x:{ beginAtZero:true, max:100, ticks:{callback:v=>v+'%'} } } }
});
@endif

new Chart(document.getElementById('monthlyAttemptsChart'), {
    type: 'line',
    data: {
        labels: monthLabels,
        datasets: [{ label:'Quiz Attempts', data:{!! json_encode($monthlyAttempts->values()) !!}, borderColor:P.gold, backgroundColor:'rgba(221,178,56,0.08)', tension:0.4, fill:true, pointBackgroundColor:P.gold, pointRadius:5 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tipOpts }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});

new Chart(document.getElementById('quizCreatedChart'), {
    type: 'bar',
    data: {
        labels: monthLabels,
        datasets: [{ label:'Quizzes Created', data:{!! json_encode($quizCreatedMonthly->values()) !!}, backgroundColor:P.teal, borderRadius:4 }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tipOpts }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});

@if($quizPassCount + $quizFailCount > 0)
new Chart(document.getElementById('quizResultChart'), {
    type: 'doughnut',
    data: { labels:['Passed','Failed'], datasets:[{ data:[{{ $quizPassCount }},{{ $quizFailCount }}], backgroundColor:[P.green,P.red], borderWidth:2, borderColor:'#fff' }] },
    options: { responsive:true, maintainAspectRatio:true, cutout:'65%', plugins:{ legend:{position:'bottom'}, tooltip:tipOpts } }
});
@endif

@if($submissionStats->sum() > 0)
new Chart(document.getElementById('submissionStatusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($submissionStats->keys()->map(fn($k) => ucfirst($k))) !!},
        datasets: [{ data:{!! json_encode($submissionStats->values()) !!}, backgroundColor:[P.blue,P.teal,P.red,P.orange], borderWidth:2, borderColor:'#fff' }]
    },
    options: { responsive:true, maintainAspectRatio:true, cutout:'65%', plugins:{ legend:{position:'bottom'}, tooltip:tipOpts } }
});
@endif

new Chart(document.getElementById('contentSummaryChart'), {
    type: 'bar',
    data: {
        labels: ['Courses', 'Topics', 'Quizzes', 'Assignments'],
        datasets: [{
            label: 'Count',
            data: [{{ $courses->count() }}, {{ $topicsCreated }}, {{ $quizzesCreated }}, {{ $assignmentsCreated }}],
            backgroundColor: [P.brown, P.teal, P.gold, P.orange],
            borderRadius: 6,
        }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false}, tooltip:tipOpts }, scales:{ y:{ beginAtZero:true, ticks:{stepSize:1} } } }
});
</script>
@endpush

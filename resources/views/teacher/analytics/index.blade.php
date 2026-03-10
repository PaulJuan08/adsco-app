@extends('layouts.teacher')

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
                <div class="analytics-header-sub">Your courses and student performance overview</div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="analytics-tabs no-print">
        <a href="{{ route('teacher.analytics.index', ['tab' => 'overview'] + request()->except('tab','page')) }}"
           class="analytics-tab {{ $activeTab === 'overview' ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Overview
        </a>
        <a href="{{ route('teacher.analytics.index', ['tab' => 'students'] + request()->except('tab','page')) }}"
           class="analytics-tab tab-students {{ $activeTab === 'students' ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <button onclick="window.print()" class="btn-analytics btn-analytics-print ms-auto no-print">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

    {{-- ═══════════════════════ OVERVIEW TAB ═══════════════════════ --}}
    @if($activeTab === 'overview')

    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div>
                    <div class="stat-label">My Courses</div>
                    <div class="stat-number">{{ $myCourses->count() }}</div>
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
                    <div class="stat-label">Quiz Attempts</div>
                    <div class="stat-number">{{ $totalAttempts }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-clipboard-check"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Quizzes Passed</div>
                    <div class="stat-number">{{ $passCount }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-orange">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Submissions</div>
                    <div class="stat-number">{{ $totalSubmissions }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-danger">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Review</div>
                    <div class="stat-number">{{ $pendingSubmissions }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        @if($avgScore !== null)
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Quiz Score</div>
                    <div class="stat-number">{{ round($avgScore, 1) }}%</div>
                </div>
                <div class="stat-icon"><i class="fas fa-star"></i></div>
            </div>
        </div>
        @endif
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Graded</div>
                    <div class="stat-number">{{ $gradedSubmissions }}</div>
                </div>
                <div class="stat-icon"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
    </div>

    <div class="analytics-charts-grid">

        {{-- Monthly Trends --}}
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
                    <div class="doughnut-item-label">Quiz Pass / Fail</div>
                    <div class="doughnut-container">
                        @if($passCount + $failCount > 0)
                        <canvas id="quizResultChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-question-circle"></i><p>No quiz attempts yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="doughnut-item">
                    <div class="doughnut-item-label">Submission Status</div>
                    <div class="doughnut-container">
                        @if($submissionStats->sum() > 0)
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
                <div class="chart-card-a-title"><i class="fas fa-chart-bar"></i> Rankings</div>
            </div>
            <div class="bar-group">
                <div class="bar-section">
                    <div class="bar-section-label">Students per Course</div>
                    <div class="chart-container" style="min-height:{{ max(180, $enrollmentsPerCourse->count() * 38 + 40) }}px">
                        @if($enrollmentsPerCourse->count() > 0)
                        <canvas id="enrollmentsChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-book"></i><p>No courses yet</p></div>
                        @endif
                    </div>
                </div>
                <div class="bar-section">
                    <div class="bar-section-label">Avg Quiz Score per Course</div>
                    <div class="chart-container" style="min-height:{{ max(180, $avgScorePerCourse->count() * 38 + 40) }}px">
                        @if($avgScorePerCourse->count() > 0)
                        <canvas id="avgScoreChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-chart-bar"></i><p>No quiz data</p></div>
                        @endif
                    </div>
                </div>
                <div class="bar-section">
                    <div class="bar-section-label">Top Students by Avg Quiz Score</div>
                    <div class="chart-container" style="min-height:{{ max(200, $topStudents->count() * 38 + 40) }}px">
                        @if($topStudents->count() > 0)
                        <canvas id="topStudentsChart"></canvas>
                        @else
                        <div class="analytics-empty"><i class="fas fa-users"></i><p>No quiz attempts yet</p></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════ STUDENTS TAB ═══════════════════════ --}}
    @elseif($activeTab === 'students')

    <form method="GET" action="{{ route('teacher.analytics.index') }}" class="analytics-filter-bar no-print">
        <input type="hidden" name="tab" value="students">
        <div class="filter-group">
            <label class="filter-label">College</label>
            <select name="college_id" onchange="this.form.submit()">
                <option value="">All Colleges</option>
                @foreach($colleges as $c)
                <option value="{{ $c->id }}" {{ $collegeId == $c->id ? 'selected' : '' }}>{{ $c->college_name }}</option>
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
        @if($collegeId || $year || $searchName)
        <div class="filter-group" style="justify-content:flex-end">
            <a href="{{ route('teacher.analytics.index', ['tab'=>'students']) }}" class="btn-analytics btn-analytics-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
        @endif
    </form>

    <div class="analytics-table-card">
        <div class="analytics-table-header">
            <div class="analytics-table-title"><i class="fas fa-user-graduate"></i> Students in Your Courses</div>
            <span style="font-size:0.8rem;color:var(--gray-500)">{{ $students->total() }} total</span>
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
                        <td>{{ $student->quiz_attempts_count }}</td>
                        <td>{{ $student->submissions_count }}</td>
                        <td>
                            <a href="{{ route('teacher.analytics.student', Crypt::encrypt($student->id)) }}"
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

    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@if($activeTab === 'overview')
<script>
const P = { brown:'#552b20', gold:'#ddb238', orange:'#d3541b', teal:'#2a8a72', green:'#48bb78', red:'#f56565', blue:'#4299e1', gray:'#a0aec0' };
const tipOpts = { backgroundColor:'#fff', titleColor:'#1e293b', bodyColor:'#475569', borderColor:'#e2e8f0', borderWidth:1, padding:10, cornerRadius:8 };
const monthLabels = {!! json_encode($monthlyAttempts->keys()->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m',$m)->format('M Y'))) !!};

@if($enrollmentsPerCourse->count() > 0)
new Chart(document.getElementById('enrollmentsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($enrollmentsPerCourse->pluck('course')) !!},
        datasets: [{ label:'Students', data:{!! json_encode($enrollmentsPerCourse->pluck('students')) !!}, backgroundColor:P.brown, borderRadius:5 }]
    },
    options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false},tooltip:tipOpts}, scales:{x:{beginAtZero:true,ticks:{stepSize:1}}} }
});
@endif

@if($avgScorePerCourse->count() > 0)
new Chart(document.getElementById('avgScoreChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($avgScorePerCourse->pluck('course')) !!},
        datasets: [{ label:'Avg Score %', data:{!! json_encode($avgScorePerCourse->pluck('avg')) !!}, backgroundColor:{!! json_encode($avgScorePerCourse->map(fn($c)=>$c['avg']>=75?'#48bb78':($c['avg']>=50?'#ddb238':'#f56565'))) !!}, borderRadius:5 }]
    },
    options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false},tooltip:tipOpts}, scales:{x:{beginAtZero:true,max:100,ticks:{callback:v=>v+'%'}}} }
});
@endif

// ── Monthly Trends (combined)
new Chart(document.getElementById('trendChart'), {
    data: {
        labels: monthLabels,
        datasets: [
            { type:'line', label:'Quiz Attempts', data:{!! json_encode($monthlyAttempts->values()) !!}, borderColor:P.gold, backgroundColor:'rgba(221,178,56,0.08)', tension:0.4, fill:true, pointBackgroundColor:P.gold, pointRadius:5, yAxisID:'y' },
            { type:'bar',  label:'Submissions',   data:{!! json_encode($monthlySubmissions->values()) !!}, backgroundColor:'rgba(42,138,114,0.7)', borderRadius:4, yAxisID:'y' }
        ]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'top'},tooltip:tipOpts}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});

@if($passCount + $failCount > 0)
new Chart(document.getElementById('quizResultChart'), {
    type: 'doughnut',
    data: { labels:['Passed','Failed'], datasets:[{ data:[{{ $passCount }},{{ $failCount }}], backgroundColor:[P.green,P.red], borderWidth:2, borderColor:'#fff' }] },
    options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{position:'bottom'},tooltip:tipOpts} }
});
@endif

@if($submissionStats->sum() > 0)
new Chart(document.getElementById('submissionStatusChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($submissionStats->keys()->map(fn($k)=>ucfirst($k))) !!},
        datasets: [{ data:{!! json_encode($submissionStats->values()) !!}, backgroundColor:[P.blue,P.teal,P.red,P.orange], borderWidth:2, borderColor:'#fff' }]
    },
    options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{position:'bottom'},tooltip:tipOpts} }
});
@endif

@if($topStudents->count() > 0)
new Chart(document.getElementById('topStudentsChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($topStudents->pluck('name')) !!},
        datasets: [{ label:'Avg Score %', data:{!! json_encode($topStudents->pluck('avg_score')) !!}, backgroundColor:{!! json_encode($topStudents->map(fn($s)=>$s->avg_score>=75?'#48bb78':($s->avg_score>=50?'#ddb238':'#f56565'))) !!}, borderRadius:5 }]
    },
    options: { indexAxis:'y', responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false},tooltip:tipOpts}, scales:{x:{beginAtZero:true,max:100,ticks:{callback:v=>v+'%'}}} }
});
@endif
</script>
@endif
@endpush

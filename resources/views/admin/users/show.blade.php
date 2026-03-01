@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-show.css') }}">
@endpush

@section('content')
<div class="user-show-container">

    <!-- Page Header -->
    <div class="page-header-bar">
        <div>
            <h1 class="page-header-title">User Details</h1>
            <p class="page-header-sub">View and manage user information</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Card -->
    <div class="user-card">

        <!-- User Card Top: Avatar + Name + Actions -->
        <div class="user-card-top">
            <div class="user-card-identity">
                <div class="user-avatar-circle">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}{{ strtoupper(substr($user->l_name, 0, 1)) }}
                </div>
                <div class="user-card-info">
                    <h2 class="user-card-name">{{ $user->f_name }} {{ $user->l_name }}</h2>
                    <div class="user-card-meta">
                        @php
                            $roleDisplay = match($user->role) {
                                1 => 'Admin',
                                2 => 'Registrar',
                                3 => 'Teacher',
                                4 => 'Student',
                                default => 'Unknown'
                            };
                            $roleClass = match($user->role) {
                                1 => 'role-admin',
                                2 => 'role-registrar',
                                3 => 'role-teacher',
                                4 => 'role-student',
                                default => 'role-unknown'
                            };
                        @endphp
                        <span class="role-pill {{ $roleClass }}">{{ $roleDisplay }}</span>
                        <span class="user-id-label">• ID: {{ $user->id }}</span>
                    </div>
                </div>
            </div>
            <div class="user-card-actions">
                @if(auth()->user()->isAdmin())
                <button class="btn-reset-password" onclick="document.getElementById('resetPasswordModal').style.display='flex'">
                    <i class="fas fa-key"></i> Reset Password
                </button>
                @endif
                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="btn-edit-user">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" id="deleteForm" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="button" class="btn-delete-user" id="deleteButton">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Tabs -->
        <div class="user-tabs">
            <button class="user-tab active" data-tab="basic">Basic Information</button>
            <button class="user-tab" data-tab="progress">Learning Progress</button>
        </div>

        <!-- ===================== TAB: BASIC INFORMATION ===================== -->
        <div id="tab-basic" class="tab-panel active">
            <div class="basic-info-grid">

                <!-- Left: Basic Information -->
                <div class="basic-info-section">
                    <h3 class="basic-section-title">Basic Information</h3>
                    <div class="basic-field-group">
                        <div class="basic-field">
                            <span class="basic-field-label">Email</span>
                            <span class="basic-field-value">{{ $user->email }}</span>
                        </div>
                        <div class="basic-field">
                            <span class="basic-field-label">Contact</span>
                            <span class="basic-field-value">{{ $user->contact ?? '—' }}</span>
                        </div>
                        <div class="basic-field">
                            <span class="basic-field-label">Password</span>
                            <span class="basic-field-value password-dots">••••••••••••</span>
                        </div>
                        @if($user->age || $user->sex)
                        <div class="basic-field">
                            <span class="basic-field-label">Age / Gender</span>
                            <span class="basic-field-value">
                                {{ $user->age ? $user->age . ' yrs' : '—' }}
                                @if($user->age && $user->sex) &nbsp;/&nbsp; @endif
                                {{ $user->sex ? ucfirst($user->sex) : '' }}
                            </span>
                        </div>
                        @endif
                        <div class="basic-field">
                            <span class="basic-field-label">Status</span>
                            <span class="basic-field-value">
                                <span class="status-pill {{ $user->is_approved ? 'status-approved' : 'status-pending' }}">
                                    {{ $user->is_approved ? 'Approved' : 'Pending Approval' }}
                                </span>
                            </span>
                        </div>
                        <div class="basic-field">
                            <span class="basic-field-label">Email Verified</span>
                            <span class="basic-field-value">
                                @if($user->email_verified_at)
                                    <span class="status-pill status-approved"><i class="fas fa-check"></i> Verified</span>
                                @else
                                    <span class="status-pill status-pending"><i class="fas fa-clock"></i> Pending</span>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.users.resend-verification', Crypt::encrypt($user->id)) }}" class="resend-link">Resend email</a>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="timestamp-row">
                        <div class="timestamp-item">
                            <span class="timestamp-label">Created At</span>
                            <span class="timestamp-value">{{ $user->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="timestamp-item">
                            <span class="timestamp-label">Updated At</span>
                            <span class="timestamp-value">{{ $user->updated_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Additional Information -->
                <div class="basic-info-section">
                    <h3 class="basic-section-title">Additional Information</h3>
                    <div class="basic-field-group">
                        @if($user->role == 3 || $user->role == 2)
                        <div class="basic-field">
                            <span class="basic-field-label">Employee ID</span>
                            <span class="basic-field-value">{{ $user->employee_id ?? '—' }}</span>
                        </div>
                        @endif
                        @if($user->role == 4)
                        <div class="basic-field">
                            <span class="basic-field-label">Student ID</span>
                            <span class="basic-field-value">{{ $user->student_id ?? '—' }}</span>
                        </div>
                        @endif
                        <div class="basic-field">
                            <span class="basic-field-label">College/Department</span>
                            <span class="basic-field-value">
                                @if($user->college)
                                    {{ $user->college->college_name }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                        @if($user->program)
                        <div class="basic-field">
                            <span class="basic-field-label">Program</span>
                            <span class="basic-field-value">{{ $user->program->program_name }}</span>
                        </div>
                        @endif
                        @if($user->college_year)
                        <div class="basic-field">
                            <span class="basic-field-label">Year Level</span>
                            <span class="basic-field-value">{{ $user->college_year }}</span>
                        </div>
                        @endif
                        @if($user->approved_at && $user->approvedBy)
                        <div class="basic-field">
                            <span class="basic-field-label">Approved By</span>
                            <span class="basic-field-value">
                                {{ $user->approvedBy->f_name }} {{ $user->approvedBy->l_name }}
                                <span style="color:#94a3b8; font-size:0.75rem;">({{ $user->approved_at->format('M d, Y') }})</span>
                            </span>
                        </div>
                        @endif
                        @if($user->last_login_at)
                        <div class="basic-field">
                            <span class="basic-field-label">Last Login</span>
                            <span class="basic-field-value">{{ $user->last_login_at->diffForHumans() }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Approve Button -->
                    @if(!$user->is_approved && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()))
                    <div style="margin-top: 1.5rem;">
                        <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" id="approveForm">
                            @csrf
                            <button type="button" class="btn-approve" id="approveButton">
                                <i class="fas fa-check-circle"></i> Approve User
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ===================== TAB: LEARNING PROGRESS ===================== -->
        <div id="tab-progress" class="tab-panel" style="display:none;">
            @if($user->role == 4)

            @php
                $enrolledCourses = App\Models\Enrollment::where('student_id', $user->id)
                    ->with(['course.topics', 'course.teacher'])
                    ->orderBy('enrolled_at', 'desc')
                    ->get();

                $completedTopicIds = App\Models\Progress::where('student_id', $user->id)
                    ->where('status', 'completed')
                    ->pluck('topic_id')
                    ->toArray();

                $quizAttempts = App\Models\QuizAttempt::where('user_id', $user->id)
                    ->with('quiz')
                    ->latest('completed_at')
                    ->get();

                $enrolledTopicIds = [];
                $courseTopicsMap = [];
                foreach ($enrolledCourses as $enrollment) {
                    if ($enrollment->course && $enrollment->course->topics) {
                        $courseTopics = $enrollment->course->topics->pluck('id')->toArray();
                        $enrolledTopicIds = array_merge($enrolledTopicIds, $courseTopics);
                        $courseTopicsMap[$enrollment->course->id] = $courseTopics;
                    }
                }
                $validCompletedTopicIds = array_intersect($completedTopicIds, $enrolledTopicIds);

                $totalTopics = count($enrolledTopicIds);
                $totalCompletedTopics = count($validCompletedTopicIds);
                $overallProgress = $totalTopics > 0 ? round(($totalCompletedTopics / $totalTopics) * 100) : 0;

                $completedCourses = 0;
                foreach ($enrolledCourses as $enrollment) {
                    if (!$enrollment->course) continue;
                    $ct = $courseTopicsMap[$enrollment->course->id] ?? [];
                    $cc = count(array_intersect($validCompletedTopicIds, $ct));
                    $totalCt = count($ct);
                    if ($totalCt > 0 && $cc >= $totalCt) $completedCourses++;
                }

                $avgQuizScore = $quizAttempts->where('completed_at', '!=', null)->avg('percentage') ?? 0;
                $correctAnswers = $quizAttempts->sum('score');
                $totalAnswers   = $quizAttempts->sum('total_points');

                // Assignment data
                $assignmentSubmissions = App\Models\AssignmentSubmission::where('student_id', $user->id)
                    ->with(['assignment.course'])
                    ->latest('submitted_at')
                    ->get();

                $totalAssignments     = $assignmentSubmissions->count();
                $gradedAssignments    = $assignmentSubmissions->where('status', 'graded')->count();
                $pendingAssignments   = $assignmentSubmissions->whereIn('status', ['submitted', 'pending'])->count();
                $avgAssignmentScore   = $assignmentSubmissions->where('status', 'graded')
                                            ->whereNotNull('score')
                                            ->avg(fn($s) => $s->assignment ? ($s->score / max($s->assignment->points, 1)) * 100 : 0) ?? 0;
            @endphp

            <!-- Summary Cards -->
            <h3 class="progress-section-heading">Learning Progress Summary</h3>
            <div class="progress-summary-cards">

                <div class="summary-card">
                    <div class="summary-card-left">
                        <div class="summary-card-label">Courses Completed</div>
                        <div class="summary-card-value">{{ $completedCourses }}/{{ $enrolledCourses->count() }}</div>
                        <div class="summary-card-bar-wrap">
                            <div class="summary-card-bar" style="width:{{ $enrolledCourses->count() > 0 ? round(($completedCourses / $enrolledCourses->count()) * 100) : 0 }}%; background:linear-gradient(90deg,#667eea,#764ba2);"></div>
                        </div>
                        <div class="summary-card-sub">{{ $enrolledCourses->count() > 0 ? round(($completedCourses / $enrolledCourses->count()) * 100) : 0 }}% Complete</div>
                    </div>
                    <div class="summary-card-icon" style="background:#eef2ff; color:#667eea;">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-card-left">
                        <div class="summary-card-label">Topics Mastered</div>
                        <div class="summary-card-value">{{ $totalCompletedTopics }}/{{ $totalTopics }}</div>
                        <div class="summary-card-bar-wrap">
                            <div class="summary-card-bar" style="width:{{ $overallProgress }}%; background:linear-gradient(90deg,#48bb78,#38a169);"></div>
                        </div>
                        <div class="summary-card-sub">{{ $overallProgress }}% Complete</div>
                    </div>
                    <div class="summary-card-icon" style="background:#f0fdf4; color:#48bb78;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-card-left">
                        <div class="summary-card-label">Avg. Quiz Score</div>
                        <div class="summary-card-value">{{ round($avgQuizScore) }}%</div>
                        @if($totalAnswers > 0)
                        <div class="summary-card-bar-wrap">
                            <div class="summary-card-bar" style="width:{{ round($avgQuizScore) }}%; background:linear-gradient(90deg,#a855f7,#7c3aed);"></div>
                        </div>
                        <div class="summary-card-sub">{{ $correctAnswers }}/{{ $totalAnswers }} correct answers</div>
                        @else
                        <div class="summary-card-bar-wrap">
                            <div class="summary-card-bar" style="width:0%;"></div>
                        </div>
                        <div class="summary-card-sub">No quiz attempts yet</div>
                        @endif
                    </div>
                    <div class="summary-card-icon" style="background:#faf5ff; color:#a855f7;">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>

                <div class="summary-card">
                    <div class="summary-card-left">
                        <div class="summary-card-label">Assignments Submitted</div>
                        <div class="summary-card-value">{{ $gradedAssignments }}/{{ $totalAssignments }}</div>
                        <div class="summary-card-bar-wrap">
                            <div class="summary-card-bar" style="width:{{ $totalAssignments > 0 ? round(($gradedAssignments / $totalAssignments) * 100) : 0 }}%; background:linear-gradient(90deg,#f97316,#ea580c);"></div>
                        </div>
                        <div class="summary-card-sub">
                            @if($totalAssignments > 0)
                                {{ $gradedAssignments }} graded • {{ $pendingAssignments }} pending
                            @else
                                No submissions yet
                            @endif
                        </div>
                    </div>
                    <div class="summary-card-icon" style="background:#fff7ed; color:#f97316;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>

            </div>

            <!-- Course Progress Table -->
            <h3 class="progress-section-heading" style="margin-top:2rem;">Course Progress</h3>
            <div class="course-progress-table-wrap">
                <table class="course-progress-table">
                    <thead>
                        <tr>
                            <th>COURSE NAME</th>
                            <th>PROGRESS</th>
                            <th>STATUS</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrolledCourses as $enrollment)
                        @php
                            $course = $enrollment->course;
                            if (!$course) continue;
                            $ct = $courseTopicsMap[$course->id] ?? [];
                            $totalCt = count($ct);
                            $completedCt = count(array_intersect($validCompletedTopicIds, $ct));
                            $pct = $totalCt > 0 ? round(($completedCt / $totalCt) * 100) : 0;
                            $status = $pct >= 100 ? 'Completed' : ($pct > 0 ? 'In Progress' : 'Not Started');
                            $statusClass = $pct >= 100 ? 'cstatus-done' : ($pct > 0 ? 'cstatus-progress' : 'cstatus-new');
                        @endphp
                        <tr>
                            <td>
                                <div class="course-name-cell">
                                    <div class="course-icon-box"><i class="fas fa-desktop"></i></div>
                                    <div>
                                        <div class="course-title">{{ $course->title }}</div>
                                        <div class="course-meta">{{ $totalCt }} Topic{{ $totalCt != 1 ? 's' : '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="table-progress-wrap">
                                    <span class="table-progress-pct">{{ $pct }}%</span>
                                    <div class="table-progress-bar-bg">
                                        <div class="table-progress-bar-fill" style="width:{{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="course-status-pill {{ $statusClass }}">{{ $status }}</span></td>
                            <td>
                                <a href="{{ route('admin.courses.show', urlencode(Crypt::encrypt($course->id))) }}" class="table-action-link">
                                    View Course
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="table-empty-cell">
                                <i class="fas fa-book-open"></i>
                                No courses enrolled
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Recently Completed Topics -->
            <h3 class="progress-section-heading" style="margin-top:2rem;">Recently Completed Topics</h3>
            @php
                $recentTopics = App\Models\Progress::where('student_id', $user->id)
                    ->where('status', 'completed')
                    ->with('topic')
                    ->latest('completed_at')
                    ->take(5)
                    ->get();
            @endphp
            @if($recentTopics->isEmpty())
                <div class="no-data-msg">No recently completed topics found</div>
            @else
            <div class="recent-topics-list">
                @foreach($recentTopics as $prog)
                @if($prog->topic)
                <div class="recent-topic-item">
                    <div class="recent-topic-icon"><i class="fas fa-check"></i></div>
                    <div class="recent-topic-name">{{ $prog->topic->title }}</div>
                    <div class="recent-topic-date">{{ $prog->completed_at ? $prog->completed_at->format('M d, Y') : '' }}</div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            <!-- Assignment Progress -->
            <h3 class="progress-section-heading" style="margin-top:2rem;">Assignment Progress</h3>
            <div class="course-progress-table-wrap">
                <table class="course-progress-table">
                    <thead>
                        <tr>
                            <th>ASSIGNMENT</th>
                            <th>COURSE</th>
                            <th>SCORE</th>
                            <th>STATUS</th>
                            <th>SUBMITTED</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignmentSubmissions as $submission)
                        @php
                            $assignment = $submission->assignment;
                            $maxPoints  = $assignment ? $assignment->points : 0;
                            $score      = $submission->score;
                            $scorePct   = ($maxPoints > 0 && $score !== null) ? round(($score / $maxPoints) * 100) : null;
                            $aStatus    = $submission->status ?? 'submitted';
                            $aStatusClass = match($aStatus) {
                                'graded'    => 'cstatus-done',
                                'submitted', 'pending' => 'cstatus-progress',
                                default     => 'cstatus-new',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="course-name-cell">
                                    <div class="course-icon-box" style="background:#fff7ed; color:#f97316;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <div class="course-title">{{ $assignment->title ?? '—' }}</div>
                                        <div class="course-meta">{{ $maxPoints > 0 ? $maxPoints . ' pts' : '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:0.8125rem; color:var(--gray-700,#374151);">
                                    {{ $assignment && $assignment->course ? $assignment->course->title : '—' }}
                                </span>
                            </td>
                            <td>
                                @if($score !== null && $maxPoints > 0)
                                <div class="table-progress-wrap">
                                    <span class="table-progress-pct">{{ $score }}/{{ $maxPoints }}</span>
                                    <div class="table-progress-bar-bg">
                                        <div class="table-progress-bar-fill" style="width:{{ $scorePct }}%; background:linear-gradient(90deg,#f97316,#ea580c);"></div>
                                    </div>
                                </div>
                                @else
                                    <span style="font-size:0.8125rem; color:var(--gray-400,#94a3b8);">Not graded</span>
                                @endif
                            </td>
                            <td><span class="course-status-pill {{ $aStatusClass }}">{{ ucfirst($aStatus) }}</span></td>
                            <td>
                                <span style="font-size:0.8125rem; color:var(--gray-600,#475569);">
                                    {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y') : '—' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="table-empty-cell">
                                <i class="fas fa-file-alt"></i>
                                No assignments submitted yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @else
            {{-- Non-student: show a simple message --}}
            <div class="no-data-msg" style="margin-top:2rem;">
                <i class="fas fa-info-circle"></i>
                Learning progress is only available for student accounts.
            </div>
            @endif
        </div>

    </div><!-- /.user-card -->

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="flash-msg flash-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash-msg flash-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

</div><!-- /.user-show-container -->


<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3 class="modal-title"><i class="fas fa-key"></i> Reset Password</h3>
        <p class="modal-desc">Enter a new password for <strong>{{ $user->f_name }} {{ $user->l_name }}</strong>.</p>
        <form action="{{ route('admin.users.update', Crypt::encrypt($user->id)) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="f_name" value="{{ $user->f_name }}">
            <input type="hidden" name="l_name" value="{{ $user->l_name }}">
            <input type="hidden" name="email" value="{{ $user->email }}">
            <input type="hidden" name="role" value="{{ $user->role }}">
            @if($user->employee_id) <input type="hidden" name="employee_id" value="{{ $user->employee_id }}"> @endif
            @if($user->student_id) <input type="hidden" name="student_id" value="{{ $user->student_id }}"> @endif
            <div class="modal-field">
                <label class="modal-label">New Password</label>
                <input type="password" name="password" class="modal-input" placeholder="Min. 8 characters" required minlength="8">
            </div>
            <div class="modal-field">
                <label class="modal-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="modal-input" placeholder="Repeat password" required minlength="8">
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-cancel" onclick="document.getElementById('resetPasswordModal').style.display='none'">Cancel</button>
                <button type="submit" class="modal-submit">Reset Password</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ───────────────────────────────────────────
    document.querySelectorAll('.user-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.user-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).style.display = 'block';
        });
    });

    // ── Approve ─────────────────────────────────────────────────
    const approveBtn = document.getElementById('approveButton');
    if (approveBtn) {
        approveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Approve User?',
                text: 'This will grant the user access to the system.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#48bb78',
                cancelButtonColor: '#a0aec0',
                confirmButtonText: 'Yes, Approve',
            }).then(result => {
                if (result.isConfirmed) {
                    approveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
                    approveBtn.disabled = true;
                    document.getElementById('approveForm').submit();
                }
            });
        });
    }

    // ── Delete ──────────────────────────────────────────────────
    const deleteBtn = document.getElementById('deleteButton');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete User?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f56565',
                cancelButtonColor: '#a0aec0',
                confirmButtonText: 'Yes, Delete',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    deleteBtn.disabled = true;
                    document.getElementById('deleteForm').submit();
                }
            });
        });
    }

    // ── Close modal on overlay click ────────────────────────────
    document.getElementById('resetPasswordModal').addEventListener('click', function (e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endpush
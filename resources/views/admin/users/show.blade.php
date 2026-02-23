@extends('layouts.admin')

@section('title', 'User Details - Admin Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-show.css') }}">
@endpush

@section('content')
<div class="user-show-container">
    <!-- Profile Header -->
    <div class="profile-header-card">
        <div class="profile-header-gradient">
            <div class="profile-title-group">
                <div class="profile-title-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2 class="profile-title">User Profile</h2>
            </div>
            
            <div class="profile-actions">
                @if($user->role == 4) {{-- If student --}}
                <a href="{{ route('admin.enrollments.student', Crypt::encrypt($user->id)) }}" class="profile-action-btn btn-enroll">
                    <i class="fas fa-user-graduate"></i> Enrollments
                </a>
                @endif
                
                <a href="{{ route('admin.users.edit', Crypt::encrypt($user->id)) }}" class="profile-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                
                @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', Crypt::encrypt($user->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="profile-action-btn btn-delete" id="deleteButton">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </form>
                @endif
                
                <a href="{{ route('admin.users.index') }}" class="profile-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="profile-content">
            <!-- Avatar & Basic Info -->
            <div class="profile-avatar-section">
                <div class="avatar-large">
                    {{ strtoupper(substr($user->f_name, 0, 1)) }}{{ strtoupper(substr($user->l_name, 0, 1)) }}
                </div>
                
                <div class="profile-name-section">
                    <h1 class="profile-name">{{ $user->f_name }} {{ $user->l_name }}</h1>
                    <div class="profile-email">{{ $user->email }}</div>
                    
                    <div class="profile-badge-group">
                        <span class="badge {{ $user->is_approved ? 'badge-approved' : 'badge-pending' }}">
                            <i class="fas {{ $user->is_approved ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            {{ $user->is_approved ? 'Approved' : 'Pending' }}
                        </span>
                        
                        @php
                            $roleDisplay = match($user->role) {
                                1 => 'Admin',
                                2 => 'Registrar',
                                3 => 'Teacher',
                                4 => 'Student',
                                default => 'Unknown'
                            };
                            $roleClass = match($user->role) {
                                1 => 'badge-role-admin',
                                2 => 'badge-role-registrar',
                                3 => 'badge-role-teacher',
                                4 => 'badge-role-student',
                                default => ''
                            };
                        @endphp
                        
                        <span class="badge {{ $roleClass }}">
                            <i class="fas fa-user-tag"></i> {{ $roleDisplay }}
                        </span>
                        
                        @if($user->student_id)
                        <span class="badge badge-college">
                            <i class="fas fa-id-card"></i> ID: {{ $user->student_id }}
                        </span>
                        @endif
                        
                        @if($user->employee_id)
                        <span class="badge badge-college">
                            <i class="fas fa-id-card"></i> ID: {{ $user->employee_id }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ===== SECTION 1: INFORMATION ===== -->
            <div class="section-divider">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Information
                </div>
            </div>

            <!-- Quick Stats Row -->
            <div class="stats-grid-compact">
                <div class="stat-card-mini">
                    <div class="stat-icon-mini">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="stat-number-mini">#{{ $user->id }}</div>
                    <div class="stat-label-mini">User ID</div>
                </div>
                
                <div class="stat-card-mini">
                    <div class="stat-icon-mini">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number-mini">{{ $user->created_at->format('M Y') }}</div>
                    <div class="stat-label-mini">Joined</div>
                </div>
                
                @if($user->last_login_at)
                <div class="stat-card-mini">
                    <div class="stat-icon-mini">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number-mini">{{ $user->last_login_at->diffForHumans() }}</div>
                    <div class="stat-label-mini">Last Login</div>
                </div>
                @endif
                
                @if($user->is_approved && $user->approved_at)
                <div class="stat-card-mini">
                    <div class="stat-icon-mini">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-number-mini">{{ $user->approved_at->format('M Y') }}</div>
                    <div class="stat-label-mini">Approved</div>
                </div>
                @endif
            </div>

            <!-- Information Grid - 2 Columns -->
            <div class="info-grid-2col">
                <!-- Personal Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-user"></i>
                        <h3>Personal Details</h3>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $user->f_name }} {{ $user->l_name }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $user->email }}</span>
                        </div>
                        
                        @if($user->age || $user->sex)
                        <div class="info-row">
                            <span class="info-label">Age/Gender</span>
                            <span class="info-value">
                                @if($user->age){{ $user->age }} years @endif
                                @if($user->age && $user->sex) • @endif
                                @if($user->sex){{ ucfirst($user->sex) }} @endif
                                @if(!$user->age && !$user->sex)Not provided @endif
                            </span>
                        </div>
                        @endif
                        
                        @if($user->contact)
                        <div class="info-row">
                            <span class="info-label">Contact</span>
                            <span class="info-value">{{ $user->contact }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Account Information -->
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-cog"></i>
                        <h3>Account Details</h3>
                    </div>
                    <div class="info-card-body">
                        <div class="info-row">
                            <span class="info-label">Role</span>
                            <span class="info-value">
                                <span class="badge {{ $roleClass }}">{{ $roleDisplay }}</span>
                            </span>
                        </div>
                        
                        @if($user->student_id || $user->employee_id)
                        <div class="info-row">
                            <span class="info-label">ID Number</span>
                            <span class="info-value">
                                {{ $user->student_id ?? $user->employee_id }}
                            </span>
                        </div>
                        @endif
                        
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge {{ $user->is_approved ? 'badge-approved' : 'badge-pending' }}">
                                    {{ $user->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Email Verified</span>
                            <span class="info-value">
                                @if($user->email_verified_at)
                                    <span style="color:#48bb78;">
                                        <i class="fas fa-check-circle"></i> Verified
                                    </span>
                                @else
                                    <span style="color:#f59e0b;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @endif
                            </span>
                        </div>
                        
                        @if($user->created_by && $user->createdBy)
                        <div class="info-row">
                            <span class="info-label">Created By</span>
                            <span class="info-value">
                                {{ $user->createdBy->f_name }} {{ $user->createdBy->l_name }}
                                <div class="info-subvalue">{{ $user->created_at->format('M d, Y') }}</div>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic Information (Students Only) -->
            @if($user->role == 4)
            <div class="info-card" style="margin-top: 0.5rem;">
                <div class="info-card-header">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Academic Information</h3>
                </div>
                <div class="info-card-body">
                    <div class="info-grid-2col" style="gap: 0.5rem; margin-bottom: 0;">
                        <div class="info-row">
                            <span class="info-label">College</span>
                            <span class="info-value">
                                @if($user->college)
                                    <a href="{{ route('admin.colleges.show', Crypt::encrypt($user->college->id)) }}" style="color:#4f46e5; text-decoration:none;">
                                        {{ $user->college->college_name }}
                                    </a>
                                @else
                                    <span style="color:#94a3b8;">Not assigned</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Program</span>
                            <span class="info-value">
                                @if($user->program)
                                    <a href="{{ route('admin.programs.show', Crypt::encrypt($user->program->id)) }}" style="color:#4f46e5; text-decoration:none;">
                                        {{ $user->program->program_name }}
                                    </a>
                                @else
                                    <span style="color:#94a3b8;">Not assigned</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Year Level</span>
                            <span class="info-value">
                                @if($user->college_year)
                                    <span class="badge badge-year">{{ $user->college_year }}</span>
                                @else
                                    <span style="color:#94a3b8;">Not set</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Approval Information (if approved) -->
            @if($user->is_approved && $user->approved_at)
            <div class="info-card" style="margin-top: 0.5rem;">
                <div class="info-card-header">
                    <i class="fas fa-check-circle" style="color:#48bb78;"></i>
                    <h3>Approval Information</h3>
                </div>
                <div class="info-card-body">
                    <div class="info-grid-2col" style="gap: 0.5rem; margin-bottom: 0;">
                        <div class="info-row">
                            <span class="info-label">Approved On</span>
                            <span class="info-value">
                                {{ $user->approved_at->format('M d, Y') }}
                                <div class="info-subvalue">{{ $user->approved_at->diffForHumans() }}</div>
                            </span>
                        </div>
                        
                        @if($user->approved_by && $user->approvedBy)
                        <div class="info-row">
                            <span class="info-label">Approved By</span>
                            <span class="info-value">
                                {{ $user->approvedBy->f_name }} {{ $user->approvedBy->l_name }}
                                <div class="info-subvalue">{{ $roleNames[$user->approvedBy->role] ?? 'Admin' }}</div>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Approve Button for Pending Users -->
            @if(!$user->is_approved && (auth()->user()->isAdmin() || auth()->user()->isRegistrar()))
            <div style="margin-top: 1.5rem; text-align: center;">
                <form action="{{ route('admin.users.approve', Crypt::encrypt($user->id)) }}" method="POST" id="approveForm">
                    @csrf
                    <button type="submit" class="profile-action-btn" id="approveButton" style="background: #48bb78; border: none; padding: 0.6rem 2rem;">
                        <i class="fas fa-check-circle"></i> Approve User
                    </button>
                </form>
            </div>
            @endif

            <!-- ===== SECTION 2: PROGRESS ===== -->
            @if($user->role == 4)
            <div class="section-divider" style="margin-top: 2rem;">
                <div class="section-title">
                    <i class="fas fa-chart-line"></i>
                    Progress
                </div>
            </div>

            <!-- Progress Tabs -->
            <div class="progress-tabs-compact">
                <button class="tab-btn-compact active" onclick="showProgressTab('courses')" id="tab-courses">Courses</button>
                <button class="tab-btn-compact" onclick="showProgressTab('quizzes')" id="tab-quizzes">Quizzes</button>
                <button class="tab-btn-compact" onclick="showProgressTab('assignments')" id="tab-assignments">Assignments</button>
            </div>

            <!-- Courses Tab -->
            <div id="progress-courses">
                @php
                    $enrolledCourses = App\Models\Enrollment::where('student_id', $user->id)
                        ->with(['course.topics', 'course.teacher'])
                        ->orderBy('enrolled_at', 'desc')
                        ->get();
                    
                    $completedTopicIds = App\Models\Progress::where('student_id', $user->id)
                        ->where('status', 'completed')
                        ->pluck('topic_id')
                        ->toArray();
                    
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
                @endphp

                <!-- Overall Progress Bar -->
                <div style="background:#f8fafc; border-radius:8px; padding:1rem; margin-bottom:1.5rem; border:1px solid #e2e8f0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                        <span style="font-size:0.75rem; font-weight:600; color:#334155;">
                            <i class="fas fa-chart-line" style="margin-right:0.375rem; color:#667eea;"></i>
                            Overall Progress
                        </span>
                        <span style="font-size:0.875rem; font-weight:700; color:#667eea;">{{ $overallProgress }}%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: {{ $overallProgress }}%"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:0.5rem; font-size:0.625rem; color:#64748b;">
                        <span>{{ $enrolledCourses->count() }} Courses</span>
                        <span>{{ $totalCompletedTopics }}/{{ $totalTopics }} Topics</span>
                    </div>
                </div>

                @if($enrolledCourses->isEmpty())
                    <div class="empty-state-mini">
                        <i class="fas fa-book-open"></i>
                        <p>No courses enrolled</p>
                        <a href="{{ route('admin.enrollments.student', Crypt::encrypt($user->id)) }}" class="profile-action-btn" style="background:#667eea; color:white; margin-top:0.75rem;">
                            <i class="fas fa-user-plus"></i> Enroll Now
                        </a>
                    </div>
                @else
                    @foreach($enrolledCourses as $enrollment)
                        @php
                            $course = $enrollment->course;
                            if (!$course) continue;
                            
                            $courseTopics = $courseTopicsMap[$course->id] ?? [];
                            $courseTopicCount = count($courseTopics);
                            $courseCompletedTopics = count(array_intersect($validCompletedTopicIds, $courseTopics));
                            $courseProgress = $courseTopicCount > 0 ? round(($courseCompletedTopics / $courseTopicCount) * 100) : 0;
                            
                            $teacherName = $course->teacher ? $course->teacher->f_name . ' ' . $course->teacher->l_name : 'Not Assigned';
                        @endphp
                        
                        <div class="progress-item">
                            <div class="progress-item-header" onclick="toggleCourseDetails({{ $course->id }})">
                                <div style="display:flex; align-items:center; gap:0.75rem; flex:1;">
                                    <i class="fas fa-book" style="color:#667eea; width:16px;"></i>
                                    <div style="flex:1;">
                                        <div style="font-weight:600; font-size:0.8125rem; color:#1e293b;">
                                            {{ $course->title }}
                                        </div>
                                        <div style="font-size:0.625rem; color:#64748b;">
                                            {{ $course->course_code }} • {{ $teacherName }}
                                        </div>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <span style="font-weight:600; font-size:0.75rem; color:{{ $courseProgress >= 80 ? '#48bb78' : ($courseProgress >= 50 ? '#fbbf24' : '#f87171') }};">
                                        {{ $courseProgress }}%
                                    </span>
                                    <i class="fas fa-chevron-down" id="chevron-{{ $course->id }}" style="color:#94a3b8; font-size:0.75rem; transition:transform 0.2s;"></i>
                                </div>
                            </div>
                            
                            <!-- Course Details (Hidden by default) -->
                            <div id="course-{{ $course->id }}" style="display: none; padding:0.75rem 1rem 1rem 2rem; border-top:1px solid #edf2f7;">
                                <!-- Enrollment Info Row -->
                                <div style="display:flex; gap:1.5rem; background:#f8fafc; padding:0.5rem 0.75rem; border-radius:6px; margin-bottom:1rem; font-size:0.6875rem;">
                                    <div>
                                        <span style="color:#64748b;">Enrolled:</span>
                                        <span style="font-weight:600; margin-left:0.375rem;">{{ $enrollment->enrolled_at->format('M d, Y') }}</span>
                                    </div>
                                    <div>
                                        <span style="color:#64748b;">Status:</span>
                                        <span style="font-weight:600; margin-left:0.375rem; color:{{ $enrollment->grade ? '#48bb78' : '#fbbf24' }};">
                                            {{ $enrollment->grade ? 'Completed' : 'In Progress' }}
                                        </span>
                                    </div>
                                    @if($enrollment->grade)
                                    <div>
                                        <span style="color:#64748b;">Grade:</span>
                                        <span style="font-weight:600; margin-left:0.375rem;">{{ $enrollment->grade }}%</span>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Topics List -->
                                <div style="margin-top:0.5rem;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                                        <span style="font-size:0.6875rem; font-weight:600; color:#334155;">
                                            <i class="fas fa-list" style="margin-right:0.375rem;"></i>
                                            Topics ({{ $courseTopicCount }})
                                        </span>
                                        <a href="{{ route('admin.courses.show', Crypt::encrypt($course->id)) }}" style="font-size:0.625rem; color:#667eea; text-decoration:none;">
                                            View Course <i class="fas fa-external-link-alt" style="font-size:0.5rem; margin-left:0.25rem;"></i>
                                        </a>
                                    </div>
                                    
                                    @if($courseTopicCount > 0)
                                        <div style="max-height:200px; overflow-y:auto; border:1px solid #edf2f7; border-radius:6px;">
                                            @foreach($course->topics as $topic)
                                                @php $isCompleted = in_array($topic->id, $validCompletedTopicIds); @endphp
                                                <div style="padding:0.5rem; border-bottom:1px solid #edf2f7; display:flex; align-items:center; justify-content:space-between;">
                                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                                        <div style="width:16px; text-align:center;">
                                                            @if($isCompleted)
                                                                <i class="fas fa-check-circle" style="color:#48bb78; font-size:0.75rem;"></i>
                                                            @else
                                                                <i class="fas fa-circle" style="color:#e2e8f0; font-size:0.375rem;"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <span style="font-size:0.75rem; font-weight:500; color:#1e293b;">{{ Str::limit($topic->title, 40) }}</span>
                                                            <div style="display:flex; gap:0.5rem; margin-top:0.125rem;">
                                                                @if($topic->video_link)
                                                                    <span style="font-size:0.5625rem; background:#fee2e2; color:#b91c1c; padding:0.125rem 0.25rem; border-radius:4px;">Video</span>
                                                                @endif
                                                                @if($topic->pdf_file)
                                                                    <span style="font-size:0.5625rem; background:#dbeafe; color:#1e3a8a; padding:0.125rem 0.25rem; border-radius:4px;">PDF</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('admin.topics.show', Crypt::encrypt($topic->id)) }}" style="font-size:0.5625rem; color:#64748b;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div style="text-align:center; padding:0.75rem; background:#f8fafc; border-radius:6px; color:#94a3b8; font-size:0.6875rem;">
                                            No topics added yet
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Quizzes Tab (Hidden by default) -->
            <div id="progress-quizzes" style="display:none;">
                @php
                    $quizAttempts = App\Models\QuizAttempt::where('user_id', $user->id)
                        ->with('quiz')
                        ->latest('completed_at')
                        ->get();
                @endphp

                @if($quizAttempts->isEmpty())
                    <div class="empty-state-mini">
                        <i class="fas fa-brain"></i>
                        <p>No quiz attempts</p>
                    </div>
                @else
                    @foreach($quizAttempts as $attempt)
                    <div class="progress-item">
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <i class="fas fa-question-circle" style="color:#667eea; width:16px;"></i>
                                <div>
                                    <div style="font-weight:600; font-size:0.8125rem;">{{ $attempt->quiz->title ?? 'Unknown Quiz' }}</div>
                                    <div style="display:flex; gap:1rem; margin-top:0.125rem; font-size:0.625rem; color:#64748b;">
                                        <span>Score: {{ $attempt->score }}/{{ $attempt->total_points }}</span>
                                        @if($attempt->completed_at)
                                            <span>{{ $attempt->completed_at->format('M d, Y') }}</span>
                                        @else
                                            <span>In Progress</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="badge {{ $attempt->passed ? 'badge-approved' : 'badge-pending' }}" style="padding:0.2rem 0.5rem;">
                                {{ $attempt->percentage }}%
                            </span>
                        </div>
                        <div class="progress-bar-container" style="margin-top:0.5rem;">
                            <div class="progress-bar-fill" style="width: {{ $attempt->percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <!-- Assignments Tab (Hidden by default) -->
            <div id="progress-assignments" style="display:none;">
                @php
                    $assignmentSubmissions = App\Models\AssignmentSubmission::where('student_id', $user->id)
                        ->with('assignment')
                        ->latest('submitted_at')
                        ->get();
                @endphp

                @if($assignmentSubmissions->isEmpty())
                    <div class="empty-state-mini">
                        <i class="fas fa-file-alt"></i>
                        <p>No assignments submitted</p>
                    </div>
                @else
                    @foreach($assignmentSubmissions as $submission)
                    <div class="progress-item">
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <i class="fas fa-file-alt" style="color:#667eea; width:16px;"></i>
                                <div>
                                    <div style="font-weight:600; font-size:0.8125rem;">{{ $submission->assignment->title ?? 'Unknown Assignment' }}</div>
                                    <div style="display:flex; gap:1rem; margin-top:0.125rem; font-size:0.625rem; color:#64748b;">
                                        <span>
                                            Score: 
                                            @if($submission->score !== null)
                                                {{ $submission->score }}/{{ $submission->assignment->points ?? 0 }}
                                            @else
                                                Not graded
                                            @endif
                                        </span>
                                        @if($submission->submitted_at)
                                            <span>{{ $submission->submitted_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="badge {{ $submission->status == 'graded' ? 'badge-approved' : 'badge-pending' }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            @endif

            <!-- Messages -->
            @if(session('success'))
            <div class="message-compact message-success-compact">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="message-compact message-error-compact">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle approve button click
        const approveButton = document.getElementById('approveButton');
        if (approveButton) {
            approveButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Approve User?',
                    text: 'This will grant the user access to the system.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        approveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Approving...';
                        approveButton.disabled = true;
                        document.getElementById('approveForm').submit();
                    }
                });
            });
        }
        
        // Handle delete button click
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete User?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }
    });

    // Progress tab switching
    window.showProgressTab = function(tab) {
        document.getElementById('progress-courses').style.display = 'none';
        document.getElementById('progress-quizzes').style.display = 'none';
        document.getElementById('progress-assignments').style.display = 'none';
        
        document.getElementById('tab-courses').classList.remove('active');
        document.getElementById('tab-quizzes').classList.remove('active');
        document.getElementById('tab-assignments').classList.remove('active');
        
        document.getElementById('progress-' + tab).style.display = 'block';
        document.getElementById('tab-' + tab).classList.add('active');
    };

    // Toggle course details
    window.toggleCourseDetails = function(courseId) {
        const detailsDiv = document.getElementById('course-' + courseId);
        const chevron = document.getElementById('chevron-' + courseId);
        
        if (detailsDiv.style.display === 'none') {
            detailsDiv.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            detailsDiv.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    };
</script>
@endpush
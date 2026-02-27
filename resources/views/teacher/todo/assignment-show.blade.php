@extends('layouts.teacher')

@section('title', $assignment->title . ' - Assignment Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('teacher.todo.index') }}">To-Do</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('teacher.todo.index', ['type' => 'assignment']) }}">Assignments</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($assignment->title, 30) }}</span>
    </div>

    {{-- Main Container --}}
    <div class="form-container">
        {{-- Header with Publish/Unpublish Toggle --}}
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h1 class="card-title">{{ $assignment->title }}</h1>
            </div>
            <div class="top-actions">
                {{-- Grant Access Button --}}
                <button type="button" 
                        class="top-action-btn" 
                        style="background: #48bb78;"
                        onclick="openAccessModal()">
                    <i class="fas fa-user-plus"></i> Grant Access
                </button>
                
                {{-- Publish/Unpublish Button --}}
                @if($assignment->is_published)
                    <button type="button" 
                            class="top-action-btn" 
                            style="background: #f56565;"
                            onclick="confirmUnpublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                @else
                    <button type="button" 
                            class="top-action-btn"
                            style="background: #48bb78;"
                            onclick="confirmPublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye"></i> Publish
                    </button>
                @endif
                
                <a href="{{ route('teacher.assignments.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="top-action-btn">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                <a href="{{ route('teacher.todo.index', ['type' => 'assignment']) }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">
            {{-- Success Alert --}}
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif

            {{-- Error Alert --}}
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif

            {{-- Assignment Preview --}}
            <div class="assignment-preview">
                <div class="assignment-preview-avatar">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="assignment-preview-content">
                    <h2 class="assignment-preview-title">{{ $assignment->title }}</h2>
                    <div class="assignment-preview-meta">
                        @if($assignment->is_published)
                            <span class="assignment-preview-badge published">
                                <i class="fas fa-check-circle"></i> Published
                            </span>
                        @else
                            <span class="assignment-preview-badge draft">
                                <i class="fas fa-pen"></i> Draft
                            </span>
                        @endif
                        <span>
                            <i class="fas fa-star"></i> {{ $assignment->points }} points
                        </span>
                        <span>
                            <i class="fas fa-book"></i> {{ $assignment->course?->course_name ?? 'No Course' }}
                        </span>
                        @if($assignment->topic)
                        <span>
                            <i class="fas fa-tag"></i> {{ $assignment->topic->name }}
                        </span>
                        @endif
                        @if($assignment->due_date)
                        <span>
                            <i class="fas fa-calendar-alt"></i> Due: {{ $assignment->due_date->format('M d, Y') }}
                            @if($assignment->due_date->isPast())
                                <span style="color: #f56565;">(Overdue)</span>
                            @endif
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            @php
                $totalSubmissions = $assignment->submissions_count ?? 0;
                $gradedCount = $assignment->submissions()->where('status', 'graded')->count();
                $pendingCount = $assignment->submissions()->where('status', 'submitted')->count();
                $lateCount = $assignment->submissions()->where('status', 'late')->count();
                $allowedCount = $assignment->allowed_students_count ?? 0;
                
                // Calculate average score
                $avgScore = $assignment->submissions()
                    ->whereNotNull('score')
                    ->avg('score');
            @endphp
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $allowedCount }}</div>
                    <div class="stat-label">Allowed Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="stat-value">{{ $totalSubmissions }}</div>
                    <div class="stat-label">Total Submissions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value">{{ $gradedCount }}</div>
                    <div class="stat-label">Graded</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">{{ $pendingCount + $lateCount }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>

            {{-- Additional Stats Row --}}
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="background: #f7fafc; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.75rem; color: #718096;">Average Score</span>
                    <div style="font-size: 1.125rem; font-weight: 700; color: #2d3748;">
                        {{ $avgScore ? round($avgScore, 1) . '/' . $assignment->points : 'N/A' }}
                    </div>
                </div>
                <div style="background: #f7fafc; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.75rem; color: #718096;">Submission Rate</span>
                    <div style="font-size: 1.125rem; font-weight: 700; color: #2d3748;">
                        {{ $allowedCount > 0 ? round(($totalSubmissions / $allowedCount) * 100) : 0 }}%
                    </div>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="two-column-layout">
                {{-- Left Column - Assignment Details --}}
                <div class="form-column">
                    {{-- Description Section --}}
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        <div class="description-box">
                            {{ $assignment->description ?? 'No description provided.' }}
                        </div>
                    </div>

                    {{-- Instructions Section --}}
                    @if($assignment->instructions)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-list-ol"></i> Instructions
                        </h3>
                        <div class="instructions-box">
                            {!! nl2br(e($assignment->instructions)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Attachment Section --}}
                    @if($assignment->attachment)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-paperclip"></i> Assignment Materials
                        </h3>
                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <i class="fas fa-file-pdf" style="font-size: 2rem; color: #f56565;"></i>
                            <div style="flex: 1; min-width: 200px;">
                                <div style="font-weight: 600;">{{ basename($assignment->attachment) }}</div>
                                <div style="font-size: 0.75rem; color: #718096;">Assignment file</div>
                            </div>
                            <a href="{{ Storage::url($assignment->attachment) }}" 
                               target="_blank"
                               class="btn-sm btn-sm-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Submissions Section --}}
                    <div class="detail-section" style="margin-top: 1rem;">
                        <h3 class="detail-section-title">
                            <i class="fas fa-file-upload"></i> Student Submissions
                            <span style="margin-left: auto; font-size: 0.75rem; color: #718096; display: flex; align-items: center; gap: 0.5rem;">
                                <span>{{ $totalSubmissions }} total</span>
                                @if($pendingCount > 0)
                                    <span class="badge badge-warning">
                                        {{ $pendingCount }} pending
                                    </span>
                                @endif
                                @if($lateCount > 0)
                                    <span class="badge badge-danger" style="background: #fff5f5; color: #c53030; border-color: #fc8181;">
                                        {{ $lateCount }} late
                                    </span>
                                @endif
                            </span>
                        </h3>

                        @php
                            $submissions = $assignment->submissions()
                                ->with(['student', 'gradedBy'])
                                ->latest()
                                ->get();
                        @endphp

                        @if($submissions->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <h3>No submissions yet</h3>
                                <p>Students haven't submitted this assignment yet</p>
                            </div>
                        @else
                            @foreach($submissions as $submission)
                                <div class="submission-card">
                                    <div class="submission-header">
                                        <div class="student-info-small">
                                            <div class="student-avatar-small">
                                                @if($submission->student)
                                                    {{ strtoupper(substr($submission->student->f_name ?? '', 0, 1) . substr($submission->student->l_name ?? '', 0, 1)) }}
                                                @else
                                                    <i class="fas fa-user-slash"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;">
                                                    @if($submission->student)
                                                        {{ $submission->student->full_name }}
                                                        @if($submission->student->student_id)
                                                            <span style="font-size: 0.6875rem; color: #718096; margin-left: 0.5rem;">
                                                                ID: {{ $submission->student->student_id }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span style="color: #f56565;">[Deleted Student]</span>
                                                    @endif
                                                </div>
                                                <div class="submission-meta">
                                                    <span><i class="fas fa-clock"></i> {{ $submission->submitted_at->diffForHumans() }}</span>
                                                    @if($submission->status == 'late')
                                                        <span style="color: #f56565;"><i class="fas fa-exclamation-circle"></i> Late Submission</span>
                                                    @endif
                                                    @if($submission->is_resubmission)
                                                        <span class="badge badge-info">Resubmission</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            @if($submission->status == 'graded')
                                                <span class="graded-badge">
                                                    <i class="fas fa-check-circle"></i> Graded
                                                </span>
                                            @elseif($submission->status == 'late')
                                                <span class="badge badge-danger">Late</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Answer Text --}}
                                    @if($submission->answer_text)
                                        <div class="submission-content">
                                            {!! nl2br(e($submission->answer_text)) !!}
                                        </div>
                                    @endif

                                    {{-- Attachment --}}
                                    @if($submission->attachment_path)
                                        <div style="padding: 1rem;">
                                            <a href="{{ Storage::url($submission->attachment_path) }}" 
                                               target="_blank"
                                               class="attachment-link">
                                                <i class="fas fa-paperclip"></i>
                                                {{ basename($submission->attachment_path) }}
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Grade Display / Grading Form --}}
                                    @if($submission->status == 'graded')
                                        <div style="display: flex; align-items: center; gap: 2rem; background: #f0fff4; padding: 1rem; border-radius: 0; flex-wrap: wrap;">
                                            <div>
                                                <div style="font-size: 0.75rem; color: #718096;">Score</div>
                                                <div class="score-display">{{ $submission->score }}/{{ $assignment->points }}</div>
                                            </div>
                                            @if($submission->feedback)
                                                <div style="flex: 1; min-width: 200px;">
                                                    <div style="font-size: 0.75rem; color: #718096;">Feedback</div>
                                                    <div style="font-size: 0.875rem;">{{ $submission->feedback }}</div>
                                                </div>
                                            @endif
                                            @if($submission->gradedBy)
                                                <div style="font-size: 0.75rem; color: #718096; text-align: right;">
                                                    <div>Graded by {{ $submission->gradedBy->full_name }}</div>
                                                    <div>{{ $submission->graded_at->format('M d, Y h:i A') }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="grade-form">
                                            <form action="{{ route('teacher.todo.submission.grade', $submission->id) }}" method="POST">
                                                @csrf
                                                <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 1rem; align-items: start; flex-wrap: wrap;">
                                                    <div>
                                                        <label style="font-size: 0.75rem; font-weight: 600; color: #4a5568;">Score</label>
                                                        <div class="grade-input-group">
                                                            <input type="number" 
                                                                name="score" 
                                                                class="grade-input" 
                                                                min="0" 
                                                                max="{{ $assignment->points }}"
                                                                required
                                                                placeholder="0-{{ $assignment->points }}">
                                                            <span style="color: #718096;">/ {{ $assignment->points }}</span>
                                                        </div>
                                                    </div>
                                                    <div style="flex: 1; min-width: 200px;">
                                                        <label style="font-size: 0.75rem; font-weight: 600; color: #4a5568;">Feedback (Optional)</label>
                                                        <textarea name="feedback" 
                                                                class="feedback-textarea" 
                                                                rows="2"
                                                                placeholder="Provide feedback to the student..."></textarea>
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn-sm btn-sm-primary" style="margin-top: 1.5rem;">
                                                            <i class="fas fa-check"></i> Submit Grade
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Right Column - Assignment Info --}}
                <div class="sidebar-column">
                    {{-- Assignment Info Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Assignment Details
                        </h3>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-heading"></i> Title</span>
                            <span class="val">{{ Str::limit($assignment->title, 20) }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hashtag"></i> Assignment ID</span>
                            <span class="val">#{{ $assignment->id }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color: {{ $assignment->is_published ? '#48bb78' : '#ed8936' }}">
                                {{ $assignment->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-star"></i> Points</span>
                            <span class="val">{{ $assignment->points }}</span>
                        </div>
                        
                        @if($assignment->course)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-book"></i> Course</span>
                            <span class="val">{{ $assignment->course->course_name }}</span>
                        </div>
                        @endif
                        
                        @if($assignment->topic)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-tag"></i> Topic</span>
                            <span class="val">{{ $assignment->topic->name }}</span>
                        </div>
                        @endif
                        
                        @if($assignment->due_date)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Due Date</span>
                            <span class="val">{{ $assignment->due_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-users"></i> Allowed Students</span>
                            <span class="val highlight">{{ $allowedCount }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-file-upload"></i> Submissions</span>
                            <span class="val">{{ $totalSubmissions }}</span>
                        </div>

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Graded</span>
                            <span class="val">{{ $gradedCount }}</span>
                        </div>

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Pending</span>
                            <span class="val">{{ $pendingCount + $lateCount }}</span>
                        </div>

                        {{-- Creator info --}}
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-circle"></i> Created By</span>
                            <span class="val">
                                @if($assignment->creator)
                                    {{ $assignment->creator->f_name }} {{ $assignment->creator->l_name }}
                                    @if($assignment->creator->role == 1)
                                        <span style="color: #718096;">(Admin)</span>
                                    @endif
                                @else
                                    <span style="color: #a0aec0;">System</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created At</span>
                            <span class="val">
                                {{ $assignment->created_at->format('M d, Y') }}
                                <span style="display: block; font-size: 0.7rem; color: #718096;">
                                    {{ $assignment->created_at->diffForHumans() }}
                                </span>
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Last Updated</span>
                            <span class="val">
                                {{ $assignment->updated_at->format('M d, Y') }}
                                <span style="display: block; font-size: 0.7rem; color: #718096;">
                                    {{ $assignment->updated_at->diffForHumans() }}
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Quick Actions Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </h3>
                        
                        <button onclick="openAccessModal()" class="quick-action-link" style="width: 100%; border: none; cursor: pointer; background: var(--gray-100); text-align: left;">
                            <i class="fas fa-user-plus"></i>
                            <span>Grant Student Access</span>
                        </button>
                        
                        <a href="{{ route('teacher.assignments.edit', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-edit"></i>
                            <span>Edit Assignment Details</span>
                        </a>
                        
                        <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="quick-action-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Progress Reports</span>
                        </a>
                        
                        <a href="{{ route('teacher.assignments.create') }}" class="quick-action-link">
                            <i class="fas fa-plus"></i>
                            <span>Create New Assignment</span>
                        </a>
                        
                        <a href="{{ route('teacher.todo.index', ['type' => 'assignment']) }}" class="quick-action-link">
                            <i class="fas fa-list"></i>
                            <span>All Assignments</span>
                        </a>
                    </div>

                    {{-- Help Card --}}
                    <div class="sidebar-card help-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i> Quick Tips
                        </h3>
                        
                        <div class="help-text">
                            <p style="margin-bottom: 0.75rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i> 
                                <strong>Grant Access:</strong> Click "Grant Access" to manage which students can submit.
                            </p>
                            <p style="margin-bottom: 0.75rem;">
                                <i class="fas fa-eye" style="color: #667eea;"></i> 
                                <strong>Publishing:</strong> Students only see published assignments they have access to.
                            </p>
                            <p style="margin-bottom: 0.75rem;">
                                <i class="fas fa-star" style="color: #f59e0b;"></i> 
                                <strong>Grading:</strong> Use the forms below each submission to grade student work.
                            </p>
                            <p>
                                <i class="fas fa-exclamation-circle" style="color: #f56565;"></i> 
                                <strong>Late Submissions:</strong> Late submissions are highlighted in red.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Access Management Modal --}}
<div id="accessModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fas fa-user-plus"></i>
                Manage Student Access - {{ $assignment->title }}
            </h3>
            <button class="modal-close" onclick="closeAccessModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modal-body">
            {{-- Modal content will be loaded via AJAX --}}
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                <p style="margin-top: 1rem; color: var(--gray-600);">Loading students...</p>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms --}}
<form id="publish-form" method="POST" action="{{ route('teacher.assignments.publish', $encryptedId) }}" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<form id="grant-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="student_ids" id="grant-student-ids">
</form>

<form id="revoke-form" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="student_ids" id="revoke-student-ids">
</form>

<form id="toggle-form" method="POST" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Modal functions
    function openAccessModal() {
        const modal = document.getElementById('accessModal');
        modal.classList.add('show');
        
        // Load modal content via AJAX
        fetch('{{ route("teacher.todo.assignment.access.modal", $encryptedId) }}')
            .then(response => response.text())
            .then(html => {
                document.getElementById('modal-body').innerHTML = html;
                initializeModalScripts();
            })
            .catch(error => {
                console.error('Error loading modal:', error);
                document.getElementById('modal-body').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Error Loading Students</h3>
                        <p>Please try again later.</p>
                    </div>
                `;
            });
    }

    function closeAccessModal() {
        const modal = document.getElementById('accessModal');
        modal.classList.remove('show');
        // Clear modal content after animation
        setTimeout(() => {
            if (!modal.classList.contains('show')) {
                document.getElementById('modal-body').innerHTML = `
                    <div style="text-align: center; padding: 2rem;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                        <p style="margin-top: 1rem; color: var(--gray-600);">Loading students...</p>
                    </div>
                `;
            }
        }, 300);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('accessModal');
        if (event.target === modal) {
            closeAccessModal();
        }
    });

    // Initialize scripts after modal content is loaded
    function initializeModalScripts() {
        // Select All Checkbox
        const selectAll = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                studentCheckboxes.forEach(cb => cb.checked = this.checked);
                
                // Update selected count
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                const countDisplay = document.getElementById('selected-count');
                if (countDisplay) {
                    countDisplay.textContent = checkedCount + ' selected';
                }
            });
        }

        // Update selected count when individual checkboxes change
        studentCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
                const countDisplay = document.getElementById('selected-count');
                if (countDisplay) {
                    countDisplay.textContent = checkedCount + ' selected';
                }
            });
        });

        // Individual Toggle Access via AJAX
        document.querySelectorAll('.toggle-access').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const url = this.dataset.url;
                const checked = this.checked;
                const self = this;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Show success notification
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || (data.status === 'allowed' ? 'Access granted' : 'Access revoked'),
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    // Update the data-status attribute for bulk actions
                    const row = self.closest('tr');
                    const checkbox = row.querySelector('.student-checkbox');
                    if (checkbox) {
                        checkbox.dataset.status = data.status;
                    }
                })
                .catch(error => {
                    self.checked = !checked; // Revert on error
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update access status.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            });
        });

        // Dynamic Program Loading based on College
        const collegeFilter = document.getElementById('college-filter');
        const programFilter = document.getElementById('program-filter');
        
        if (collegeFilter) {
            collegeFilter.addEventListener('change', function() {
                const collegeId = this.value;
                
                // Clear and disable program filter
                programFilter.innerHTML = '<option value="">All Programs</option>';
                programFilter.disabled = !collegeId;
                
                if (!collegeId) return;
                
                // Show loading state
                programFilter.innerHTML = '<option value="">Loading...</option>';
                
                // Fetch programs
                fetch(`{{ url('teacher/todo/colleges') }}/${collegeId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programFilter.innerHTML = '<option value="">All Programs</option>';
                        
                        if (programs && programs.length > 0) {
                            programs.forEach(program => {
                                const option = document.createElement('option');
                                option.value = program.id;
                                option.textContent = program.program_name;
                                programFilter.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading programs:', error);
                        programFilter.innerHTML = '<option value="">Error loading programs</option>';
                    });
            });
        }

        // Form submission for bulk actions
        const grantBtn = document.getElementById('grant-selected');
        const revokeBtn = document.getElementById('revoke-selected');
        
        if (grantBtn) {
            grantBtn.addEventListener('click', function() {
                const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
                
                if (studentIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Grant Access?',
                    text: `Grant access to ${studentIds.length} selected student(s)?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Grant Access'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('grant-form');
                        document.getElementById('grant-student-ids').value = JSON.stringify(studentIds);
                        form.action = '{{ route("teacher.todo.assignment.grant", $encryptedId) }}';
                        form.submit();
                    }
                });
            });
        }
        
        if (revokeBtn) {
            revokeBtn.addEventListener('click', function() {
                const studentIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
                
                if (studentIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Revoke Access?',
                    text: `Revoke access from ${studentIds.length} selected student(s)?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Revoke Access'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.getElementById('revoke-form');
                        document.getElementById('revoke-student-ids').value = JSON.stringify(studentIds);
                        form.action = '{{ route("teacher.todo.assignment.revoke", $encryptedId) }}';
                        form.submit();
                    }
                });
            });
        }

        // Handle filter form submission via AJAX
        const filterForm = document.querySelector('.filter-bar form');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const url = this.action + '?' + new URLSearchParams(new FormData(this)).toString();
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modal-body').innerHTML = html;
                    initializeModalScripts(); // Re-initialize scripts for new content
                })
                .catch(error => {
                    console.error('Error loading filtered results:', error);
                });
            });
        }

        // Handle clear filter link
        const clearLink = document.querySelector('[data-ajax-link="true"]');
        if (clearLink) {
            clearLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                fetch(this.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modal-body').innerHTML = html;
                    initializeModalScripts();
                })
                .catch(error => {
                    console.error('Error clearing filters:', error);
                });
            });
        }
    }

    // Publish/Unpublish functions
    window.confirmPublish = function(encryptedId) {
        Swal.fire({
            title: 'Publish Assignment?',
            text: 'Once published, this assignment will be visible to students who have access.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Publish',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('publish-form').submit();
            }
        });
    };

    window.confirmUnpublish = function(encryptedId) {
        Swal.fire({
            title: 'Unpublish Assignment?',
            text: 'This assignment will be hidden from students until you publish it again.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Unpublish',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('publish-form').submit();
            }
        });
    };
</script>
@endpush
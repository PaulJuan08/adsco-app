@extends('layouts.admin')

@section('title', $assignment->title . ' - Assignment Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
<style>
    /* Additional styles for grading */
    .grade-form {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid #e2e8f0;
    }
    
    .grade-input-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    
    .grade-input {
        width: 100px;
        padding: 0.5rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
    }
    
    .grade-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .feedback-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        resize: vertical;
    }
    
    .submission-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #edf2f7;
    }
    
    .student-info-small {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .student-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    
    .submission-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: #718096;
    }
    
    .submission-content {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        white-space: pre-line;
        font-size: 0.9375rem;
        line-height: 1.6;
    }
    
    .attachment-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #4a5568;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    
    .attachment-link:hover {
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .graded-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #d1fae5;
        color: #065f46;
    }
    
    .score-display {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary);
    }
    
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        margin-left: 0.5rem;
    }
    
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider-small {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: .3s;
        border-radius: 20px;
    }
    
    .toggle-slider-small:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider-small {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }
    
    input:checked + .toggle-slider-small:before {
        transform: translateX(20px);
    }
</style>
@endpush

@section('content')
<div class="form-container">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h1 class="card-title">{{ $assignment->title }}</h1>
        </div>
        <div class="top-actions">
            <a href="{{ route('admin.assignments.edit', $encryptedId) }}" class="top-action-btn">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.todo.assignment.access', $encryptedId) }}" class="top-action-btn">
                <i class="fas fa-users"></i> Manage Access
            </a>
            <a href="{{ route('admin.assignments.index') }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Body --}}
    <div class="card-body">
        {{-- Assignment Preview --}}
        <div class="assignment-preview">
            <div class="assignment-preview-avatar">
                <i class="fas fa-file-alt"></i>
            </div>
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
                <span class="assignment-preview-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-star"></i> {{ $assignment->points }} points
                </span>
            </div>
        </div>

        {{-- Stats Grid --}}
        @php
            $totalSubmissions = $assignment->submissions_count ?? 0;
            $gradedCount = $assignment->submissions()->where('status', 'graded')->count();
            $pendingCount = $assignment->submissions()->where('status', 'submitted')->count();
        @endphp
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $assignment->allowed_students_count ?? 0 }}</div>
                <div class="stat-label">Allowed Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="stat-value">{{ $totalSubmissions }}</div>
                <div class="stat-label">Submissions</div>
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
                <div class="stat-value">{{ $pendingCount }}</div>
                <div class="stat-label">Pending</div>
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
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <i class="fas fa-file-pdf" style="font-size: 2rem; color: #f56565;"></i>
                        <div style="flex: 1;">
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
            </div>

            {{-- Right Column - Assignment Info --}}
            <div class="sidebar-column">
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">
                        <i class="fas fa-info-circle"></i> Assignment Details
                    </h3>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-book"></i> Course</span>
                        <span class="info-value">{{ $assignment->course->course_name ?? 'Not assigned' }}</span>
                    </div>

                    @if($assignment->topic)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-tag"></i> Topic</span>
                        <span class="info-value">{{ $assignment->topic->name }}</span>
                    </div>
                    @endif

                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-alt"></i> Due Date</span>
                        <span class="info-value">
                            @if($assignment->due_date)
                                {{ $assignment->due_date->format('M d, Y') }}
                                @if($assignment->due_date->isPast())
                                    <span style="color: #f56565;">(Overdue)</span>
                                @endif
                            @else
                                No due date
                            @endif
                        </span>
                    </div>

                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-star"></i> Total Points</span>
                        <span class="info-value highlight">{{ $assignment->points }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-alt"></i> Available From</span>
                        <span class="info-value">
                            @if($assignment->available_from)
                                {{ $assignment->available_from->format('M d, Y') }}
                            @else
                                Immediately
                            @endif
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar-alt"></i> Available Until</span>
                        <span class="info-value">
                            @if($assignment->available_until)
                                {{ $assignment->available_until->format('M d, Y') }}
                            @else
                                No end date
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submissions Section --}}
        <div class="detail-section" style="margin-top: 2rem;">
            <h3 class="detail-section-title">
                <i class="fas fa-file-upload"></i> Student Submissions
                <span style="margin-left: auto; font-size: 0.75rem; color: #718096;">
                    {{ $totalSubmissions }} total
                </span>
            </h3>

            @php
                $submissions = $assignment->submissions()->with(['student', 'gradedBy'])->latest()->get();
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
                                    {{ strtoupper(substr($submission->student->f_name, 0, 1) . substr($submission->student->l_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600;">{{ $submission->student->full_name }}</div>
                                    <div class="submission-meta">
                                        <span><i class="fas fa-id-card"></i> {{ $submission->student->student_id ?? 'N/A' }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $submission->submitted_at->diffForHumans() }}</span>
                                        @if($submission->status == 'late')
                                            <span style="color: #f56565;"><i class="fas fa-exclamation-circle"></i> Late</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($submission->status == 'graded')
                                    <span class="graded-badge">
                                        <i class="fas fa-check-circle"></i> Graded
                                    </span>
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
                            <div style="margin-bottom: 1rem;">
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
                            <div style="display: flex; align-items: center; gap: 2rem; background: #f0fff4; padding: 1rem; border-radius: 8px;">
                                <div>
                                    <div style="font-size: 0.75rem; color: #718096;">Score</div>
                                    <div class="score-display">{{ $submission->score }}/{{ $assignment->points }}</div>
                                </div>
                                @if($submission->feedback)
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.75rem; color: #718096;">Feedback</div>
                                        <div style="font-size: 0.875rem;">{{ $submission->feedback }}</div>
                                    </div>
                                @endif
                                @if($submission->gradedBy)
                                    <div style="font-size: 0.75rem; color: #718096;">
                                        Graded by {{ $submission->gradedBy->full_name }}<br>
                                        {{ $submission->graded_at->format('M d, Y h:i A') }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="grade-form">
                                <form action="{{ route('admin.todo.submission.grade', $submission->id) }}" method="POST">
                                    @csrf
                                    <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 1rem; align-items: start;">
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
                                        <div style="flex: 1;">
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
</div>
@endsection
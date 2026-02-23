@extends('layouts.admin')

@section('title', 'Submission Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
<style>
    .submission-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .submission-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .submission-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .submission-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .submission-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .student-info-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .student-avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        flex-shrink: 0;
    }

    .student-details {
        flex: 1;
    }

    .student-name-large {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.25rem;
    }

    .student-email {
        color: #718096;
        margin-bottom: 0.5rem;
    }

    .student-badges {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-college {
        background: #ede9fe;
        color: #5b21b6;
    }

    .badge-program {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-year {
        background: #dcfce7;
        color: #15803d;
    }

    .submission-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .content-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
    }

    .content-title i {
        color: #667eea;
    }

    .answer-text {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        white-space: pre-line;
        font-size: 1rem;
        line-height: 1.8;
        color: #2d3748;
        border: 1px solid #e2e8f0;
    }

    .attachment-box {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .attachment-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .attachment-info {
        flex: 1;
    }

    .attachment-name {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.125rem;
    }

    .attachment-meta {
        font-size: 0.75rem;
        color: #718096;
    }

    .grade-section {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        border: 1px solid #e2e8f0;
        scroll-margin-top: 2rem;
    }

    .grade-display {
        background: linear-gradient(135deg, #f0fff4 0%, #e6fffa 100%);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #9ae6b4;
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .grade-score {
        font-size: 3rem;
        font-weight: 800;
        color: #22543d;
        line-height: 1;
    }

    .grade-details {
        flex: 1;
    }

    .grade-label {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.25rem;
    }

    .grade-value {
        font-weight: 600;
        color: #2d3748;
    }

    .grade-feedback {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        border-left: 4px solid #667eea;
    }

    .grade-form {
        margin-top: 2rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1rem;
        align-items: start;
    }

    .score-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .score-input {
        width: 100px;
        padding: 0.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        text-align: center;
    }

    .score-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .feedback-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        resize: vertical;
    }

    .feedback-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-submit {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    @media (max-width: 768px) {
        .student-info-card {
            flex-direction: column;
            text-align: center;
        }

        .grade-display {
            flex-direction: column;
            text-align: center;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .score-input-group {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="submission-container">
    {{-- Header --}}
    <div class="submission-header">
        <h1 class="submission-title">Submission Details</h1>
        <div class="submission-meta">
            <span class="meta-item">
                <i class="fas fa-file-alt"></i>
                {{ $submission->assignment->title }}
            </span>
            <span class="meta-item">
                <i class="fas fa-clock"></i>
                Submitted {{ $submission->submitted_at->diffForHumans() }}
            </span>
            <span class="meta-item">
                <i class="fas fa-hashtag"></i>
                ID: {{ $submission->id }}
            </span>
        </div>
    </div>

    {{-- Student Info --}}
    <div class="student-info-card">
        <div class="student-avatar-large">
            {{ strtoupper(substr($submission->student->f_name, 0, 1) . substr($submission->student->l_name, 0, 1)) }}
        </div>
        <div class="student-details">
            <div class="student-name-large">{{ $submission->student->full_name }}</div>
            <div class="student-email">{{ $submission->student->email }}</div>
            <div class="student-badges">
                @if($submission->student->student_id)
                    <span class="badge" style="background: #e2e8f0; color: #4a5568;">
                        <i class="fas fa-id-card"></i> {{ $submission->student->student_id }}
                    </span>
                @endif
                @if($submission->student->college)
                    <span class="badge badge-college">
                        <i class="fas fa-university"></i> {{ $submission->student->college->college_name }}
                    </span>
                @endif
                @if($submission->student->program)
                    <span class="badge badge-program">
                        <i class="fas fa-graduation-cap"></i> {{ $submission->student->program->program_name }}
                    </span>
                @endif
                @if($submission->student->college_year)
                    <span class="badge badge-year">
                        <i class="fas fa-calendar-alt"></i> Year {{ $submission->student->college_year }}
                    </span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.users.show', Crypt::encrypt($submission->student->id)) }}" 
           class="btn-secondary" 
           style="display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-user"></i>
            View Profile
        </a>
    </div>

    {{-- Answer Text --}}
    @if($submission->answer_text)
    <div class="submission-content">
        <h3 class="content-title">
            <i class="fas fa-pencil-alt"></i>
            Student's Answer
        </h3>
        <div class="answer-text">
            {{ $submission->answer_text }}
        </div>
    </div>
    @endif

    {{-- Attachment --}}
    @if($submission->attachment_path)
    <div class="submission-content">
        <h3 class="content-title">
            <i class="fas fa-paperclip"></i>
            Attached File
        </h3>
        <div class="attachment-box">
            <div class="attachment-icon">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="attachment-info">
                <div class="attachment-name">{{ basename($submission->attachment_path) }}</div>
                <div class="attachment-meta">
                    <i class="fas fa-file"></i> Student's submission
                </div>
            </div>
            <a href="{{ Storage::url($submission->attachment_path) }}" 
               target="_blank"
               class="btn-secondary" 
               style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-download"></i>
                Download
            </a>
        </div>
    </div>
    @endif

    {{-- Grade Section --}}
    <div class="grade-section" id="grade">
        <h3 class="content-title">
            <i class="fas fa-star"></i>
            Grade Assignment
        </h3>

        @if($submission->status === 'graded')
            {{-- Display Grade --}}
            <div class="grade-display">
                <div class="grade-score">{{ $submission->score }}/{{ $submission->assignment->points }}</div>
                <div class="grade-details">
                    <div class="grade-label">Percentage</div>
                    <div class="grade-value">{{ round(($submission->score / $submission->assignment->points) * 100) }}%</div>
                    <div class="grade-label" style="margin-top: 0.5rem;">Graded By</div>
                    <div class="grade-value">{{ $submission->gradedBy->full_name ?? 'Unknown' }}</div>
                    <div class="grade-label" style="margin-top: 0.5rem;">Graded At</div>
                    <div class="grade-value">{{ $submission->graded_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>

            @if($submission->feedback)
                <div class="grade-feedback">
                    <div style="font-weight: 600; margin-bottom: 0.5rem;">
                        <i class="fas fa-comment" style="color: #667eea;"></i>
                        Feedback
                    </div>
                    <div>{{ $submission->feedback }}</div>
                </div>
            @endif

            <div style="margin-top: 2rem; text-align: center;">
                <a href="{{ route('admin.assignments.show', Crypt::encrypt($submission->assignment->id)) }}" 
                   class="btn-secondary" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-arrow-left"></i>
                    Back to Assignment
                </a>
            </div>

        @else
            {{-- Grade Form --}}
            <form action="{{ route('admin.todo.submission.grade', $submission->id) }}" method="POST">
                @csrf
                
                <div class="form-grid">
                    <div>
                        <label style="font-size: 0.875rem; font-weight: 600; color: #4a5568; display: block; margin-bottom: 0.5rem;">
                            Score
                        </label>
                        <div class="score-input-group">
                            <input type="number" 
                                   name="score" 
                                   class="score-input" 
                                   min="0" 
                                   max="{{ $submission->assignment->points }}"
                                   required
                                   placeholder="0-{{ $submission->assignment->points }}">
                            <span style="color: #718096;">/ {{ $submission->assignment->points }}</span>
                        </div>
                    </div>

                    <div>
                        <label style="font-size: 0.875rem; font-weight: 600; color: #4a5568; display: block; margin-bottom: 0.5rem;">
                            Feedback (Optional)
                        </label>
                        <textarea name="feedback" 
                                  class="feedback-textarea" 
                                  rows="4"
                                  placeholder="Provide feedback to the student..."></textarea>
                    </div>

                    <div style="align-self: flex-end;">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check"></i>
                            Submit Grade
                        </button>
                    </div>
                </div>
            </form>

            <div style="margin-top: 2rem; text-align: center;">
                <a href="{{ route('admin.assignments.show', Crypt::encrypt($submission->assignment->id)) }}" 
                   class="btn-secondary" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-arrow-left"></i>
                    Back to Assignment
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to grade section if URL has #grade
        if (window.location.hash === '#grade') {
            document.getElementById('grade').scrollIntoView({ behavior: 'smooth' });
        }

        // Form submission confirmation
        const form = document.querySelector('form');
        if (form && !form.querySelector('.grade-display')) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const score = this.querySelector('input[name="score"]').value;
                const maxScore = {{ $submission->assignment->points }};
                
                Swal.fire({
                    title: 'Submit Grade?',
                    html: `You are about to assign <strong>${score}/${maxScore}</strong> to this student.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#48bb78',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Submit Grade',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                        submitBtn.disabled = true;
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
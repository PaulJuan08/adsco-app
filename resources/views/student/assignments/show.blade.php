@extends('layouts.student')

@section('title', $assignment->title . ' — Assignment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
<link rel="stylesheet" href="{{ asset('css/student-assignment-show.css') }}">
@endpush

@section('content')
<div class="form-container">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title-group">
            <i class="fas fa-file-alt card-icon"></i>
            <h2 class="card-title">{{ $assignment->title }}</h2>
        </div>
        <div class="top-actions">
            <a href="{{ route('student.assignments.index') }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Assignment Header --}}
        <div class="assignment-header-large">
            <div class="assignment-icon-large">
                <i class="fas fa-file-alt"></i>
            </div>
            <h1 class="assignment-title-large">{{ $assignment->title }}</h1>
            
            <div class="assignment-meta-badges">
                <span class="meta-badge">
                    <i class="fas fa-star"></i> {{ $assignment->points }} Points
                </span>
                @if($assignment->due_date)
                <span class="meta-badge">
                    <i class="fas fa-calendar-alt"></i> Due: {{ $assignment->due_date->format('M d, Y h:i A') }}
                    @if($assignment->isOverdue() && (!$submission || $submission->status == 'pending'))
                        <span class="overdue-badge-large">Overdue</span>
                    @endif
                </span>
                @endif
                <span class="meta-badge">
                    <i class="fas fa-book"></i> {{ $assignment->course->title ?? 'No Course' }}
                </span>
                @if($assignment->topic)
                <span class="meta-badge">
                    <i class="fas fa-tag"></i> {{ $assignment->topic->title }}
                </span>
                @endif
            </div>
        </div>

        {{-- Cannot Submit Message --}}
        @if(!$canSubmit && !$submission)
        <div class="cannot-submit-message">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Assignment Overdue</h4>
            <p>This assignment is overdue and can no longer be submitted. Please contact your instructor if you need an extension.</p>
        </div>
        @endif

        {{-- Two Column Layout --}}
        <div class="two-column-layout">
            {{-- Left Column - Assignment Details --}}
            <div class="form-column">
                {{-- Description Section --}}
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-align-left"></i> Description
                    </h3>
                    <div class="description-box rich-text">
                        {!! $assignment->description ?? 'No description provided.' !!}
                    </div>
                </div>

                {{-- Instructions Section --}}
                @if($assignment->instructions)
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-list-ol"></i> Instructions
                    </h3>
                    <div class="instructions-box rich-text">
                        {!! $assignment->instructions !!}
                    </div>
                </div>
                @endif

                {{-- Attachment Section --}}
                @if($assignment->attachment)
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-paperclip"></i> Assignment Materials
                    </h3>
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 12px;">
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

            {{-- Right Column - Submission Area --}}
            <div class="sidebar-column">
                @if($submission && $submission->status == 'graded')
                    {{-- Graded Submission View --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-star"></i> Your Grade
                        </h3>
                        
                        <div class="grade-result">
                            <div class="grade-score-large">
                                {{ $submission->score }}/{{ $assignment->points }}
                            </div>
                            <div style="font-size: 1rem; color: #22543d; font-weight: 600;">
                                {{ round(($submission->score / $assignment->points) * 100) }}%
                            </div>
                            
                            <div class="grade-details">
                                <div>
                                    <div style="font-size: 0.75rem; color: #718096;">Submitted</div>
                                    <div style="font-weight: 600;">{{ $submission->submitted_at->format('M d, Y') }}</div>
                                </div>
                                <div>
                                    <div style="font-size: 0.75rem; color: #718096;">Graded</div>
                                    <div style="font-weight: 600;">{{ $submission->graded_at->format('M d, Y') }}</div>
                                </div>
                            </div>

                            @if($submission->feedback)
                                <div class="feedback-box">
                                    <div style="font-weight: 600; margin-bottom: 0.5rem;">
                                        <i class="fas fa-comment" style="color: var(--primary);"></i> Feedback
                                    </div>
                                    <div style="white-space: pre-line;">{{ $submission->feedback }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Submission Form (only show if can submit or already submitted) --}}
                @if($canSubmit || $submission)
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">
                        <i class="fas fa-{{ $submission ? 'redo' : 'pencil-alt' }}"></i>
                        {{ $submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                    </h3>

                    @if($submission && $submission->status != 'graded')
                        <div class="submission-card">
                            <div class="submission-header">
                                <span class="submission-status-badge {{ $submission->status == 'late' ? 'status-late' : 'status-submitted' }}">
                                    <i class="fas fa-{{ $submission->status == 'late' ? 'exclamation-circle' : 'check-circle' }}"></i>
                                    Your Submission ({{ ucfirst($submission->status) }})
                                </span>
                                <span style="font-size: 0.75rem; color: #718096;">
                                    {{ $submission->submitted_at->diffForHumans() }}
                                </span>
                            </div>

                            @if($submission->answer_text)
                                <div class="submission-content">
                                    {!! nl2br(e($submission->answer_text)) !!}
                                </div>
                            @endif

                            @if($submission->attachment_path)
                                <a href="{{ Storage::url($submission->attachment_path) }}" 
                                   target="_blank"
                                   class="attachment-link">
                                    <i class="fas fa-paperclip"></i>
                                    {{ basename($submission->attachment_path) }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('student.assignments.submit', $encryptedId) }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="submissionForm">
                        @csrf
                        
                        <div class="form-group">
                            <label for="answer_text" class="form-label">
                                <i class="fas fa-pencil-alt"></i> Your Answer
                            </label>
                            <textarea name="answer_text" 
                                      id="answer_text" 
                                      rows="8" 
                                      class="answer-textarea @error('answer_text') error @enderror"
                                      placeholder="Write your answer here...">{{ old('answer_text', $submission->answer_text ?? '') }}</textarea>
                            @error('answer_text')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-paperclip"></i> Attachment (Optional)
                            </label>
                            <div class="file-upload-area" onclick="document.getElementById('attachment').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="file-upload-text">Click to upload or drag and drop</div>
                                <div class="file-upload-subtext">PDF, DOC, DOCX, TXT, Images (Max 10MB)</div>
                            </div>
                            <input type="file" 
                                   name="attachment" 
                                   id="attachment" 
                                   style="display: none;"
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                            
                            <div id="file-info" style="display: none; margin-top: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f0fdf4; border-radius: 8px;">
                                    <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                    <span style="flex: 1;" id="file-name">Selected file</span>
                                    <button type="button" onclick="clearFile()" class="btn-sm btn-sm-outline">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                            
                            @error('attachment')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                            <button type="submit" class="btn-submit" {{ !$canSubmit ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane"></i> 
                                {{ $submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                            </button>
                            <a href="{{ route('student.assignments.index') }}" class="btn-outline">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>

                        @if(!$canSubmit)
                            <p style="margin-top: 1rem; font-size: 0.875rem; color: #f56565; text-align: center;">
                                <i class="fas fa-exclamation-circle"></i> 
                                You cannot submit this assignment because it is overdue.
                            </p>
                        @endif
                    </form>
                </div>
                @endif

                {{-- Tips Card --}}
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title">
                        <i class="fas fa-lightbulb"></i> Tips
                    </h3>
                    
                    <div class="tips-grid">
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Write Clearly</div>
                                <div class="tip-description">Organize your answer with clear paragraphs</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Check File Format</div>
                                <div class="tip-description">Ensure your file is in an accepted format</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Watch Due Dates</div>
                                <div class="tip-description">Submit before the deadline</div>
                            </div>
                        </div>
                        
                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-save"></i>
                            </div>
                            <div class="tip-content">
                                <div class="tip-title">Save Locally</div>
                                <div class="tip-description">Keep a backup of your work</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const attachmentInput = document.getElementById('attachment');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const canSubmit = {{ $canSubmit ? 'true' : 'false' }};
        
        // File upload handling
        if (attachmentInput) {
            attachmentInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    fileName.textContent = file.name;
                    fileInfo.style.display = 'block';
                } else {
                    fileInfo.style.display = 'none';
                }
            });
        }

        // Drag and drop
        const uploadArea = document.querySelector('.file-upload-area');
        if (uploadArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.style.background = 'linear-gradient(135deg, #f8fafc 0%, #fff 100%)';
                    uploadArea.style.borderColor = '#f59e0b';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.style.background = '#f8fafc';
                    uploadArea.style.borderColor = '#e2e8f0';
                });
            });

            uploadArea.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (attachmentInput) {
                    attachmentInput.files = files;
                    if (files.length > 0) {
                        fileName.textContent = files[0].name;
                        fileInfo.style.display = 'block';
                    }
                }
            });
        }

        // Form submission validation
        const form = document.getElementById('submissionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!canSubmit) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Cannot Submit',
                        text: 'This assignment is overdue and can no longer be submitted.',
                        icon: 'error',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }
                
                e.preventDefault();
                
                const answerText = document.getElementById('answer_text')?.value.trim();
                const attachment = document.getElementById('attachment')?.files[0];
                
                if (!answerText && !attachment) {
                    Swal.fire({
                        title: 'No Content',
                        text: 'Please provide either an answer text or upload a file.',
                        icon: 'warning',
                        confirmButtonColor: '#f59e0b'
                    });
                    return;
                }
                
                Swal.fire({
                    title: '{{ $submission ? "Resubmit Assignment?" : "Submit Assignment?" }}',
                    text: 'Are you sure you want to {{ $submission ? "resubmit" : "submit" }} this assignment?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f59e0b',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, {{ $submission ? "Resubmit" : "Submit" }}',
                    cancelButtonText: 'Review'
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

    window.clearFile = function() {
        const attachmentInput = document.getElementById('attachment');
        const fileInfo = document.getElementById('file-info');
        attachmentInput.value = '';
        fileInfo.style.display = 'none';
    };
</script>
@endpush
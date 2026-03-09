@extends('layouts.student')

@section('title', $assignment->title . ' — Assignment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-show.css') }}">
@endpush

@section('content')
<div class="form-container">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon"><i class="fas fa-file-alt"></i></div>
            <div>
                <h1 class="card-title">{{ $assignment->title }}</h1>
                @if($assignment->due_date && $assignment->due_date->isPast())
                    <span class="card-status-badge draft">
                        <i class="fas fa-exclamation-circle"></i> Overdue
                    </span>
                @else
                    <span class="card-status-badge published">
                        <i class="fas fa-check-circle"></i> Open
                    </span>
                @endif
            </div>
        </div>
        <div class="top-actions">
            <a href="{{ route('student.todo.index') }}" class="top-action-btn">
                <i class="fas fa-arrow-left"></i> Back to To-Do
            </a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $error) {{ $error }} @endforeach
        </div>
        @endif

        <div class="two-column-layout">
            {{-- Left Column --}}
            <div class="form-column">

                {{-- Description --}}
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-align-left"></i> Description</h3>
                    <div class="description-box rich-text">
                        {!! $assignment->description ?? 'No description provided.' !!}
                    </div>
                </div>

                {{-- Instructions --}}
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-list-ol"></i> Instructions</h3>
                    <div class="instructions-box rich-text">
                        {!! $assignment->instructions ?? '<p style="color:#a0aec0;font-style:italic;">No specific instructions provided.</p>' !!}
                    </div>
                </div>

                {{-- Assignment Materials --}}
                @if($assignment->attachment)
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-paperclip"></i> Assignment Materials</h3>
                    <div style="display:flex;align-items:center;gap:1rem;padding:1rem;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0;">
                        <i class="fas fa-file-pdf" style="font-size:2rem;color:#f56565;"></i>
                        <div style="flex:1;">
                            <div style="font-weight:600;">{{ basename($assignment->attachment) }}</div>
                            <div style="font-size:0.75rem;color:#718096;">Assignment file</div>
                        </div>
                        <a href="{{ Storage::url($assignment->attachment) }}" target="_blank" class="btn-sm btn-sm-primary">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
                @endif

                {{-- Graded Result --}}
                @if($submission && $submission->status == 'graded')
                <div class="detail-section">
                    <h3 class="detail-section-title"><i class="fas fa-star"></i> Your Grade</h3>
                    <div style="background:linear-gradient(135deg,#f0fff4,#e6fffa);border:1px solid #9ae6b4;border-radius:12px;padding:1.5rem;">
                        <div style="display:flex;align-items:center;gap:1.5rem;margin-bottom:1rem;">
                            <div style="text-align:center;">
                                <div style="font-size:2.5rem;font-weight:800;color:#22543d;line-height:1;">
                                    {{ $submission->score }}<span style="font-size:1.2rem;color:#4a5568;">/{{ $assignment->points }}</span>
                                </div>
                                <div style="font-size:0.875rem;font-weight:600;color:#38a169;margin-top:0.25rem;">
                                    {{ round(($submission->score / $assignment->points) * 100) }}%
                                </div>
                            </div>
                            <div style="flex:1;border-left:2px solid #9ae6b4;padding-left:1.5rem;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                                    <div>
                                        <div style="font-size:0.7rem;color:#718096;text-transform:uppercase;font-weight:600;">Submitted</div>
                                        <div style="font-size:0.875rem;font-weight:600;color:#2d3748;">{{ $submission->submitted_at->format('M d, Y') }}</div>
                                    </div>
                                    <div>
                                        <div style="font-size:0.7rem;color:#718096;text-transform:uppercase;font-weight:600;">Graded</div>
                                        <div style="font-size:0.875rem;font-weight:600;color:#2d3748;">{{ $submission->graded_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($submission->feedback)
                        <div style="background:white;border-radius:8px;padding:1rem;border-left:4px solid #48bb78;">
                            <div style="font-size:0.75rem;font-weight:700;color:#718096;text-transform:uppercase;margin-bottom:0.4rem;">
                                <i class="fas fa-comment" style="color:#48bb78;"></i> Teacher Feedback
                            </div>
                            <div style="font-size:0.875rem;line-height:1.6;white-space:pre-line;">{{ $submission->feedback }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Previous Submission (pending/late) --}}
                @if($submission && $submission->status != 'graded')
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-history"></i> Your Submission
                        <span style="margin-left:auto;font-size:0.75rem;padding:0.2rem 0.6rem;border-radius:20px;
                            background:{{ $submission->status == 'late' ? 'rgba(245,101,101,0.15)' : 'rgba(72,187,120,0.15)' }};
                            color:{{ $submission->status == 'late' ? '#f56565' : '#38a169' }};">
                            <i class="fas fa-{{ $submission->status == 'late' ? 'exclamation-circle' : 'clock' }}"></i>
                            {{ ucfirst($submission->status) }} &middot; {{ $submission->submitted_at->diffForHumans() }}
                        </span>
                    </h3>
                    @if($submission->answer_text)
                    <div style="background:#f8fafc;border-radius:8px;padding:1rem;border:1px solid #e2e8f0;white-space:pre-line;font-size:0.9rem;line-height:1.6;">
                        {!! nl2br(e($submission->answer_text)) !!}
                    </div>
                    @endif
                    @if($submission->attachment_path)
                    <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:0.5rem;margin-top:0.75rem;padding:0.5rem 1rem;background:#eff6ff;color:#3b82f6;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;border:1px solid #dbeafe;">
                        <i class="fas fa-paperclip"></i> {{ basename($submission->attachment_path) }}
                    </a>
                    @endif
                </div>
                @endif

                {{-- Submit Form (in main body) --}}
                <div class="detail-section">
                    <h3 class="detail-section-title">
                        <i class="fas fa-{{ $submission ? 'redo' : 'paper-plane' }}"></i>
                        {{ $submission ? 'Resubmit Assignment' : 'Submit Assignment' }}
                    </h3>

                    <form action="{{ route('student.todo.assignment.submit', $encryptedId) }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="submissionForm">
                        @csrf

                        <div class="form-group" style="margin-bottom:1rem;">
                            <label class="form-label" style="font-size:0.78rem;font-weight:600;color:#4a5568;display:block;margin-bottom:0.4rem;">
                                <i class="fas fa-pencil-alt"></i> Your Answer
                            </label>
                            <textarea name="answer_text"
                                      id="answer_text"
                                      rows="8"
                                      style="width:100%;padding:0.875rem;border:2px solid #e2e8f0;border-radius:10px;font-size:0.9rem;line-height:1.6;resize:vertical;transition:border-color 0.2s;box-sizing:border-box;"
                                      placeholder="Write your answer here..."
                                      onfocus="this.style.borderColor='var(--primary)'"
                                      onblur="this.style.borderColor='#e2e8f0'">{{ old('answer_text', $submission->answer_text ?? '') }}</textarea>
                        </div>

                        <div class="form-group" style="margin-bottom:1.25rem;">
                            <label class="form-label" style="font-size:0.78rem;font-weight:600;color:#4a5568;display:block;margin-bottom:0.4rem;">
                                <i class="fas fa-paperclip"></i> Attachment <span style="color:#a0aec0;">(Optional)</span>
                            </label>
                            <div id="uploadArea"
                                 onclick="document.getElementById('attachment').click()"
                                 style="border:2px dashed #cbd5e0;border-radius:10px;padding:1.25rem;text-align:center;background:#f8fafc;cursor:pointer;transition:all 0.2s;">
                                <i class="fas fa-cloud-upload-alt" style="font-size:1.75rem;color:var(--primary);display:block;margin-bottom:0.4rem;"></i>
                                <div style="font-size:0.8rem;font-weight:600;color:#4a5568;">Click to upload or drag and drop</div>
                                <div style="font-size:0.72rem;color:#a0aec0;margin-top:0.2rem;">PDF, DOC, DOCX, TXT, Images · Max 10MB</div>
                            </div>
                            <input type="file" name="attachment" id="attachment" style="display:none;"
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                            <div id="file-info" style="display:none;margin-top:0.75rem;">
                                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0.875rem;background:#f0fdf4;border-radius:8px;border:1px solid #9ae6b4;">
                                    <i class="fas fa-check-circle" style="color:#48bb78;"></i>
                                    <span style="flex:1;font-size:0.8rem;font-weight:500;" id="file-name">Selected file</span>
                                    <button type="button" onclick="clearFile()" style="padding:0.2rem 0.5rem;border-radius:6px;border:1px solid #cbd5e0;background:white;font-size:0.72rem;cursor:pointer;color:#718096;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div style="display:flex;gap:0.75rem;">
                            <button type="submit" class="btn-sm btn-sm-primary" style="flex:1;padding:0.7rem;font-size:0.875rem;">
                                <i class="fas fa-paper-plane"></i>
                                {{ $submission ? 'Resubmit' : 'Submit Assignment' }}
                            </button>
                            <a href="{{ route('student.todo.index') }}" class="btn-sm btn-sm-outline">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Right Sidebar --}}
            <div class="sidebar-column">

                {{-- Assignment Details --}}
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title"><i class="fas fa-info-circle"></i> Assignment Details</h3>

                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-star"></i> Points</span>
                        <span class="val highlight">{{ $assignment->points }}</span>
                    </div>
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-trophy"></i> Passing Score</span>
                        <span class="val">{{ $assignment->passing_score ?? 70 }}%</span>
                    </div>
                    @if($assignment->course)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-book"></i> Course</span>
                        <span class="val">{{ Str::limit($assignment->course->title, 20) }}</span>
                    </div>
                    @endif
                    @if($assignment->topic)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-tag"></i> Topic</span>
                        <span class="val">{{ Str::limit($assignment->topic->title, 20) }}</span>
                    </div>
                    @endif
                    @if($assignment->due_date)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-calendar-alt"></i> Due Date</span>
                        <span class="val" style="color:{{ $assignment->due_date->isPast() ? '#f56565' : 'inherit' }};">
                            {{ $assignment->due_date->format('M d, Y') }}
                            <span style="display:block;font-size:0.68rem;color:#718096;">{{ $assignment->due_date->format('h:i A') }}</span>
                        </span>
                    </div>
                    @endif
                    @if($submission)
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-upload"></i> Your Status</span>
                        <span class="val" style="color:{{ $submission->status == 'graded' ? '#38a169' : ($submission->status == 'late' ? '#f56565' : '#ed8936') }};">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </div>
                    @if($submission->status == 'graded')
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-star"></i> Your Score</span>
                        <span class="val highlight">{{ $submission->score }}/{{ $assignment->points }}</span>
                    </div>
                    @endif
                    @endif
                </div>

                {{-- Tips --}}
                <div class="sidebar-card">
                    <h3 class="sidebar-card-title"><i class="fas fa-lightbulb"></i> Tips</h3>
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-pencil-alt"></i> Write Clearly</span>
                        <span class="val" style="font-size:0.72rem;color:#718096;text-align:right;">Use clear paragraphs</span>
                    </div>
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-paperclip"></i> File Format</span>
                        <span class="val" style="font-size:0.72rem;color:#718096;text-align:right;">PDF, DOC, images</span>
                    </div>
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-clock"></i> Submit Early</span>
                        <span class="val" style="font-size:0.72rem;color:#718096;text-align:right;">Avoid last-minute issues</span>
                    </div>
                    <div class="info-row-sm">
                        <span class="lbl"><i class="fas fa-save"></i> Save Locally</span>
                        <span class="val" style="font-size:0.72rem;color:#718096;text-align:right;">Keep a backup</span>
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
    const uploadArea = document.getElementById('uploadArea');

    if (attachmentInput) {
        attachmentInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) { fileName.textContent = file.name; fileInfo.style.display = 'block'; }
            else { fileInfo.style.display = 'none'; }
        });
    }

    if (uploadArea) {
        ['dragenter','dragover','dragleave','drop'].forEach(ev => uploadArea.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }));
        ['dragenter','dragover'].forEach(ev => uploadArea.addEventListener(ev, () => { uploadArea.style.borderColor = 'var(--primary)'; uploadArea.style.background = '#f0fdf4'; }));
        ['dragleave','drop'].forEach(ev => uploadArea.addEventListener(ev, () => { uploadArea.style.borderColor = '#cbd5e0'; uploadArea.style.background = '#f8fafc'; }));
        uploadArea.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (attachmentInput && files.length > 0) {
                attachmentInput.files = files;
                fileName.textContent = files[0].name;
                fileInfo.style.display = 'block';
            }
        });
    }

    const form = document.getElementById('submissionForm');
    if (form) {
        const hasExistingAttachment = {{ $submission && $submission->attachment_path ? 'true' : 'false' }};

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const answerText = document.getElementById('answer_text')?.value.trim();
            const attachment = document.getElementById('attachment')?.files[0];
            if (!answerText && !attachment && !hasExistingAttachment) {
                Swal.fire({ title: 'No Content', text: 'Please provide an answer or upload a file.', icon: 'warning', confirmButtonColor: 'var(--primary)' });
                return;
            }
            Swal.fire({
                title: '{{ $submission ? "Resubmit?" : "Submit Assignment?" }}',
                text: 'Are you sure you want to {{ $submission ? "resubmit" : "submit" }} this assignment?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: 'var(--primary)',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ $submission ? "Yes, Resubmit" : "Yes, Submit" }}',
                cancelButtonText: 'Review'
            }).then(result => {
                if (result.isConfirmed) {
                    const btn = form.querySelector('button[type="submit"]');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                    btn.disabled = true;
                    form.submit();
                }
            });
        });
    }
});

window.clearFile = function() {
    document.getElementById('attachment').value = '';
    document.getElementById('file-info').style.display = 'none';
};
</script>
@endpush

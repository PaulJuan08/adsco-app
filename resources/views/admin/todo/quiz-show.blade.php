@extends('layouts.admin')

@section('title', $quiz->title . ' - Quiz Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.todo.index') }}">To-Do</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('admin.todo.index', ['type' => 'quiz']) }}">Quizzes</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current">{{ Str::limit($quiz->title, 30) }}</span>
    </div>

    {{-- Main Container --}}
    <div class="form-container">
        {{-- Header with Publish/Unpublish Toggle --}}
        <div class="card-header">
            <div class="card-title-group">
                <div class="card-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h1 class="card-title">{{ $quiz->title }}</h1>
            </div>
            <div class="top-actions">
                {{-- Grant Access Button --}}
                <button type="button" 
                        class="top-action-btn" 
                        style="background: #48bb78; color: white; border: none;"
                        onclick="openAccessModal()">
                    <i class="fas fa-user-plus"></i> Grant Access
                </button>
                
                {{-- Publish/Unpublish Button --}}
                @if($quiz->is_published)
                    <button type="button" 
                            class="top-action-btn" 
                            style="background: #f56565; color: white; border: none;"
                            onclick="confirmUnpublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                @else
                    <button type="button" 
                            class="top-action-btn"
                            style="background: #48bb78; color: white; border: none;"
                            onclick="confirmPublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye"></i> Publish
                    </button>
                @endif
                
                <a href="{{ route('admin.quizzes.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="top-action-btn">
                    <i class="fas fa-chart-bar"></i> Progress
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

            {{-- Quiz Preview --}}
            <div class="quiz-preview">
                <div class="quiz-preview-avatar">
                    {{ strtoupper(substr($quiz->title, 0, 1)) }}
                </div>
                <div class="quiz-preview-content">
                    <h2 class="quiz-preview-title">{{ $quiz->title }}</h2>
                    <div class="quiz-preview-meta">
                        <span class="quiz-preview-badge {{ $quiz->is_published ? 'published' : 'draft' }}">
                            <i class="fas {{ $quiz->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            {{ $quiz->is_published ? 'Published' : 'Draft' }}
                        </span>
                        <span>
                            <i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions
                        </span>
                        <span>
                            <i class="fas fa-clock"></i> {{ $quiz->duration ? $quiz->duration . ' min' : 'No time limit' }}
                        </span>
                        <span>
                            <i class="fas fa-trophy"></i> {{ $quiz->passing_score }}% to pass
                        </span>
                        <span>
                            <i class="fas fa-star"></i> {{ $totalPoints }} points
                        </span>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $quiz->allowed_students_count ?? 0 }}</div>
                        <div class="stat-label">Allowed Students</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $quiz->attempts_count ?? 0 }}</div>
                        <div class="stat-label">Total Attempts</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $passCount }}</div>
                        <div class="stat-label">Passed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $failCount }}</div>
                        <div class="stat-label">Failed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $avgScore ? round($avgScore) . '%' : 'N/A' }}</div>
                        <div class="stat-label">Average Score</div>
                    </div>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="two-column-layout">
                {{-- Left Column - Quiz Details --}}
                <div class="form-column">
                    {{-- Description Section --}}
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        <div class="description-box">
                            {{ $quiz->description ?? 'No description provided.' }}
                        </div>
                    </div>

                    {{-- Availability Section --}}
                    @if($quiz->available_from || $quiz->available_until)
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-calendar-alt"></i> Availability
                        </h3>
                        <div class="info-grid">
                            @if($quiz->available_from)
                            <div class="info-item">
                                <div class="info-label">Available From</div>
                                <div class="info-value">{{ $quiz->available_from->format('M d, Y h:i A') }}</div>
                            </div>
                            @endif
                            @if($quiz->available_until)
                            <div class="info-item">
                                <div class="info-label">Available Until</div>
                                <div class="info-value">{{ $quiz->available_until->format('M d, Y h:i A') }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Questions Section --}}
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-question-circle"></i> Questions
                            <span style="margin-left: auto; font-size: 0.75rem; color: #718096;">
                                {{ $quiz->questions->count() }} total
                            </span>
                        </h3>

                        @forelse($quiz->questions as $index => $question)
                        <div class="question-card">
                            <div class="question-header">
                                <div>
                                    <span class="question-title">Question #{{ $index + 1 }}</span>
                                </div>
                                <span class="question-points">
                                    <i class="fas fa-star"></i> {{ $question->points ?? 1 }} point(s)
                                </span>
                            </div>
                            
                            <div class="question-text">
                                {!! nl2br(e($question->question)) !!}
                            </div>
                            
                            <div class="options-list">
                                @foreach($question->options as $option)
                                <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                    <div class="option-marker {{ $option->is_correct ? 'correct' : 'incorrect' }}">
                                        @if($option->is_correct)
                                        <i class="fas fa-check"></i>
                                        @else
                                        <i class="fas fa-times"></i>
                                        @endif
                                    </div>
                                    <div class="option-text">{{ $option->option_text }}</div>
                                </div>
                                @endforeach
                            </div>
                            
                            @if($question->explanation)
                            <div class="explanation-box">
                                <div class="explanation-title">
                                    <i class="fas fa-info-circle"></i> Explanation
                                </div>
                                <div class="explanation-text">
                                    {!! nl2br(e($question->explanation)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>No Questions Yet</h3>
                            <p>This quiz doesn't have any questions yet.</p>
                            <a href="{{ route('admin.quizzes.edit', $encryptedId) . '#questions-section' }}" 
                               style="display: inline-block; margin-top: 1rem; padding: 0.5rem 1.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; text-decoration: none; font-size: 0.8125rem; font-weight: 600;">
                                <i class="fas fa-plus" style="margin-right: 0.375rem;"></i> Add Questions
                            </a>
                        </div>
                        @endforelse
                    </div>

                    {{-- Recent Attempts Section --}}
                    @if($recentAttempts->isNotEmpty())
                    <div class="detail-section">
                        <h3 class="detail-section-title">
                            <i class="fas fa-history"></i> Recent Attempts
                            <span style="margin-left: auto; font-size: 0.75rem; color: #718096;">
                                Latest 10 attempts
                            </span>
                        </h3>
                        
                        @foreach($recentAttempts as $attempt)
                        <div class="attempt-card">
                            <div class="attempt-header">
                                <span class="student-name">
                                    @if($attempt->user)
                                        {{ $attempt->user->full_name }}
                                    @else
                                        <span style="color: #f56565;">[Deleted User]</span>
                                    @endif
                                </span>
                                <span class="attempt-score {{ $attempt->passed ? 'passed' : 'failed' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            </div>
                            <div class="attempt-meta">
                                <span><i class="fas fa-calendar"></i> {{ $attempt->completed_at->format('M d, Y h:i A') }}</span>
                                <span><i class="fas fa-clock"></i> {{ $attempt->completed_at->diffForHumans() }}</span>
                                <span><i class="fas fa-{{ $attempt->passed ? 'check-circle' : 'times-circle' }}"></i> {{ $attempt->passed ? 'Passed' : 'Failed' }}</span>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($quiz->attempts_count > 10)
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="{{ route('admin.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" 
                               class="btn-sm btn-sm-primary">
                                View All {{ $quiz->attempts_count }} Attempts
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Right Column - Quiz Info --}}
                <div class="sidebar-column">
                    {{-- Quiz Info Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Quiz Details
                        </h3>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hashtag"></i> Quiz ID</span>
                            <span class="val">#{{ $quiz->id }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color: {{ $quiz->is_published ? '#48bb78' : '#ed8936' }}">
                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-question-circle"></i> Questions</span>
                            <span class="val">{{ $quiz->questions->count() }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Duration</span>
                            <span class="val">{{ $quiz->duration ? $quiz->duration . ' min' : 'No limit' }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-trophy"></i> Passing Score</span>
                            <span class="val highlight">{{ $quiz->passing_score }}%</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-star"></i> Total Points</span>
                            <span class="val">{{ $totalPoints }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-users"></i> Allowed Students</span>
                            <span class="val highlight">{{ $quiz->allowed_students_count ?? 0 }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-paper-plane"></i> Total Attempts</span>
                            <span class="val">{{ $quiz->attempts_count ?? 0 }}</span>
                        </div>

                        {{-- Creator info --}}
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-circle"></i> Created By</span>
                            <span class="val">
                                @if($quiz->creator)
                                    {{ $quiz->creator->f_name }} {{ $quiz->creator->l_name }}
                                    <span style="display: block; font-size: 0.7rem; color: #718096;">
                                        ({{ $quiz->creator->role == 1 ? 'Admin' : ($quiz->creator->role == 3 ? 'Teacher' : 'User') }})
                                    </span>
                                @else
                                    <span style="color: #a0aec0;">System</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created At</span>
                            <span class="val">
                                {{ $quiz->created_at->format('M d, Y') }}
                                <span style="display: block; font-size: 0.7rem; color: #718096;">
                                    {{ $quiz->created_at->diffForHumans() }}
                                </span>
                            </span>
                        </div>
                        
                        @if($quiz->updated_at != $quiz->created_at)
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-edit"></i> Last Updated</span>
                            <span class="val">
                                {{ $quiz->updated_at->format('M d, Y') }}
                                <span style="display: block; font-size: 0.7rem; color: #718096;">
                                    {{ $quiz->updated_at->diffForHumans() }}
                                </span>
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Quick Actions Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </h3>
                        
                        <button onclick="openAccessModal()" class="quick-action-link" style="width: 100%; border: none; cursor: pointer; background: none; text-align: left;">
                            <i class="fas fa-user-plus"></i>
                            <span>Grant Student Access</span>
                        </button>
                        
                        <a href="{{ route('admin.quizzes.edit', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-edit"></i>
                            <span>Edit Quiz Details</span>
                        </a>
                        
                        <a href="{{ route('admin.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="quick-action-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Progress Reports</span>
                        </a>
                        
                        <a href="{{ route('admin.todo.index', ['type' => 'quiz']) }}" class="quick-action-link">
                            <i class="fas fa-list"></i>
                            <span>All Quizzes</span>
                        </a>
                    </div>

                    {{-- Help Card --}}
                    <div class="sidebar-card help-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i> Quick Tips
                        </h3>
                        
                        <div class="help-text">
                            <p style="margin-bottom: 0.75rem;">
                                <strong>Grant Access:</strong> Click the "Grant Access" button to manage which students can take this quiz.
                            </p>
                            <p style="margin-bottom: 0.75rem;">
                                <strong>Publishing:</strong> Students can only see published quizzes they have access to.
                            </p>
                            <p>
                                <strong>Attempts:</strong> View recent attempts and scores in the section above.
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
                Manage Student Access - {{ $quiz->title }}
            </h3>
            <button class="modal-close" onclick="closeAccessModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modal-body">
            {{-- Modal content will be loaded via AJAX --}}
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i>
                <p style="margin-top: 1rem; color: #718096;">Loading students...</p>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Forms --}}
<form id="publish-form" method="POST" action="{{ route('admin.quizzes.toggle-publish', $encryptedId) }}" style="display: none;">
    @csrf
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
        fetch('{{ route("admin.todo.quiz.access.modal", $encryptedId) }}')
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
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i>
                        <p style="margin-top: 1rem; color: #718096;">Loading students...</p>
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
            });
        }

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
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Create toast notification
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification';
                    toast.textContent = data.status === 'allowed' ? '✓ Access granted' : '✗ Access revoked';
                    toast.style.background = data.status === 'allowed' ? '#48bb78' : '#f56565';
                    document.body.appendChild(toast);
                    
                    // Update the data-status attribute for bulk actions
                    const row = self.closest('tr');
                    const checkbox = row.querySelector('.student-checkbox');
                    if (checkbox) {
                        checkbox.dataset.status = data.status;
                    }
                    
                    setTimeout(() => toast.remove(), 2500);
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
                
                // Fetch programs
                fetch(`{{ url('admin/todo/colleges') }}/${collegeId}/programs`)
                    .then(response => response.json())
                    .then(programs => {
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = program.program_name;
                            programFilter.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading programs:', error));
            });
        }

        // Form submission for bulk actions
        const bulkForm = document.getElementById('bulk-form');
        if (bulkForm) {
            bulkForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const action = e.submitter.formAction;
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
                
                // Confirm for revoke actions
                if (action.includes('revoke')) {
                    Swal.fire({
                        title: 'Revoke Access?',
                        text: `Are you sure you want to revoke access for ${studentIds.length} student(s)?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f56565',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, Revoke'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBulkForm(action, studentIds);
                        }
                    });
                } else {
                    submitBulkForm(action, studentIds);
                }
            });
        }

        function submitBulkForm(action, studentIds) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            studentIds.forEach(id => formData.append('student_ids[]', id));
            
            fetch(action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload modal content
                    openAccessModal();
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to process request.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    }

    // Publish/Unpublish functions
    window.confirmPublish = function(encryptedId) {
        Swal.fire({
            title: 'Publish Quiz?',
            text: 'Once published, this quiz will be visible to students who have access.',
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
            title: 'Unpublish Quiz?',
            text: 'This quiz will be hidden from students until you publish it again.',
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
@extends('layouts.teacher')

@section('title', $quiz->title . ' - Quiz Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/quiz-show.css') }}">
@endpush

@section('content')
<div class="quiz-container">
    <div class="form-container">
        <!-- Header -->
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-brain card-icon"></i>
                <h1 class="card-title">{{ $quiz->title }}</h1>
            </div>
            <div class="top-actions">
                <button type="button" class="top-action-btn" onclick="openAccessModal()">
                    <i class="fas fa-user-plus"></i> Grant Access
                </button>
                
                @if($quiz->is_published)
                    <button type="button" class="top-action-btn" onclick="confirmUnpublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye-slash"></i> Unpublish
                    </button>
                @else
                    <button type="button" class="top-action-btn" onclick="confirmPublish('{{ $encryptedId }}')">
                        <i class="fas fa-eye"></i> Publish
                    </button>
                @endif
                
                <a href="{{ route('teacher.quizzes.edit', $encryptedId) }}" class="top-action-btn">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="top-action-btn">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                <a href="{{ route('teacher.todo.index', ['type' => 'quiz']) }}" class="top-action-btn">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Success Alert -->
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif

            <!-- Error Alert -->
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif

            <!-- Quiz Preview -->
            <div class="quiz-preview">
                <div class="quiz-preview-avatar">
                    {{ strtoupper(substr($quiz->title, 0, 1)) }}
                </div>
                <div class="quiz-preview-content">
                    <div class="quiz-preview-title">{{ $quiz->title }}</div>
                    <div class="quiz-preview-meta">
                        <span class="quiz-preview-badge {{ $quiz->is_published ? 'published' : 'draft' }}">
                            <i class="fas {{ $quiz->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                            {{ $quiz->is_published ? 'Published' : 'Draft' }}
                        </span>
                        <span><i class="fas fa-question-circle"></i> {{ $quiz->questions->count() }} Questions</span>
                        <span><i class="fas fa-clock"></i> {{ $quiz->duration ? $quiz->duration . ' min' : 'No time limit' }}</span>
                        <span><i class="fas fa-trophy"></i> {{ $quiz->passing_score }}% to pass</span>
                        <span><i class="fas fa-star"></i> {{ $totalPoints }} points</span>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
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
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $avgScore ? round($avgScore) . '%' : 'N/A' }}</div>
                        <div class="stat-label">Average Score</div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column - Main Content -->
                <div class="form-column">
                    <!-- Description Section -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-align-left"></i> Description
                        </div>
                        <div class="description-box">
                            {{ $quiz->description ?? 'No description provided.' }}
                        </div>
                    </div>

                    <!-- Availability Section -->
                    @if($quiz->available_from || $quiz->available_until)
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-calendar-alt"></i> Availability
                        </div>
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

                    <!-- Questions Section -->
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-question-circle"></i> Questions
                            <span style="margin-left: auto;">{{ $quiz->questions->count() }} total</span>
                        </div>

                        @forelse($quiz->questions as $index => $question)
                        <div class="question-card">
                            <div class="question-header">
                                <span class="question-title">Question #{{ $index + 1 }}</span>
                                <span class="question-points">{{ $question->points ?? 1 }} point(s)</span>
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
                            <a href="{{ route('teacher.quizzes.edit', $encryptedId) }}" class="btn-sm btn-sm-primary">
                                <i class="fas fa-plus"></i> Add Questions
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Recent Attempts Section -->
                    @if($recentAttempts->isNotEmpty())
                    <div class="detail-section">
                        <div class="detail-section-title">
                            <i class="fas fa-history"></i> Recent Attempts
                            <span style="margin-left: auto;">Latest 10 attempts</span>
                        </div>
                        
                        @foreach($recentAttempts as $attempt)
                        <div class="attempt-card">
                            <div class="attempt-header">
                                <span class="student-name">
                                    @if($attempt->user)
                                        {{ $attempt->user->full_name }}
                                    @else
                                        <span style="color: var(--danger);">[Deleted User]</span>
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
                            <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="btn-sm btn-sm-primary">
                                View All {{ $quiz->attempts_count }} Attempts
                            </a>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Right Column - Sidebar -->
                <div class="sidebar-column">
                    <!-- Quiz Info Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Quiz Details
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-hashtag"></i> Quiz ID</span>
                            <span class="val">#{{ $quiz->id }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="val" style="color: {{ $quiz->is_published ? 'var(--success)' : 'var(--warning)' }}">
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

                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-user-circle"></i> Created By</span>
                            <span class="val">
                                @if($quiz->creator)
                                    {{ $quiz->creator->f_name }} {{ $quiz->creator->l_name }}
                                    @if($quiz->creator->role == 1)
                                        <span style="color: var(--gray-500);">(Admin)</span>
                                    @endif
                                @else
                                    <span style="color: var(--gray-500);">System</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-calendar-alt"></i> Created</span>
                            <span class="val">{{ $quiz->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <div class="info-row-sm">
                            <span class="lbl"><i class="fas fa-clock"></i> Last Updated</span>
                            <span class="val">{{ $quiz->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <button onclick="openAccessModal()" class="quick-action-link" style="width: 100%; border: none; cursor: pointer; background: var(--gray-100); text-align: left;">
                            <i class="fas fa-user-plus"></i>
                            <span>Grant Student Access</span>
                        </button>
                        
                        <a href="{{ route('teacher.quizzes.edit', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-edit"></i>
                            <span>Edit Quiz Details</span>
                        </a>
                        
                        <a href="{{ route('teacher.quizzes.results', $encryptedId) }}" class="quick-action-link">
                            <i class="fas fa-chart-bar"></i>
                            <span>View Results & Analytics</span>
                        </a>
                        
                        <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="quick-action-link">
                            <i class="fas fa-list"></i>
                            <span>View Progress Reports</span>
                        </a>
                        
                        <a href="{{ route('teacher.todo.index', ['type' => 'quiz']) }}" class="quick-action-link">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back to To-Do</span>
                        </a>
                    </div>

                    <!-- Help Card -->
                    <div class="sidebar-card help-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i> Quick Tips
                        </div>
                        
                        <div class="help-text">
                            <p><i class="fas fa-check-circle" style="color: var(--success);"></i> <strong>Grant Access:</strong> Manage which students can take this quiz</p>
                            <p><i class="fas fa-eye" style="color: var(--primary);"></i> <strong>Publishing:</strong> Students only see published quizzes</p>
                            <p><i class="fas fa-chart-bar" style="color: var(--warning);"></i> <strong>Progress:</strong> Track student performance in reports</p>
                            <p><i class="fas fa-users" style="color: var(--info);"></i> <strong>Results:</strong> View detailed quiz results and analytics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Access Management Modal -->
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
            <div style="text-align: center; padding: 2rem;">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary);"></i>
                <p style="margin-top: 1rem; color: var(--gray-600);">Loading students...</p>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="publish-form" method="POST" action="{{ route('teacher.quizzes.toggle-publish', $encryptedId) }}" style="display: none;">
    @csrf
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openAccessModal() {
        const modal = document.getElementById('accessModal');
        modal.classList.add('show');
        
        fetch('{{ route("teacher.todo.quiz.access.modal", $encryptedId) }}')
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
        // Reset modal body after close
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

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('accessModal');
        if (event.target === modal) {
            closeAccessModal();
        }
    });

    function confirmPublish() {
        Swal.fire({
            title: 'Publish Quiz?',
            text: 'This quiz will be visible to students who have access.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#48bb78',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Publish'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('publish-form').submit();
            }
        });
    }

    function confirmUnpublish() {
        Swal.fire({
            title: 'Unpublish Quiz?',
            text: 'This quiz will be hidden from students.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f56565',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Unpublish'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('publish-form').submit();
            }
        });
    }

    function initializeModalScripts() {
        // Select All functionality
        const selectAll = document.getElementById('select-all');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                studentCheckboxes.forEach(cb => cb.checked = this.checked);
            });
        }
        
        // Initialize toggle functionality
        const toggleButtons = document.querySelectorAll('.toggle-access');
        toggleButtons.forEach(btn => {
            btn.addEventListener('change', function() {
                const url = this.dataset.url;
                const checked = this.checked;
                const self = this;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                        title: data.message || 'Access updated',
                        showConfirmButton: false,
                        timer: 3000
                    });
                })
                .catch(error => {
                    self.checked = !checked; // Revert on error
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Failed to update access',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
            });
        });

        // Grant selected students
        const grantBtn = document.getElementById('grant-selected');
        if (grantBtn) {
            grantBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
                
                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("teacher.todo.quiz.grant", $encryptedId) }}';
                
                const csrf = document.createElement('input');
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.name = 'student_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Revoke selected students
        const revokeBtn = document.getElementById('revoke-selected');
        if (revokeBtn) {
            revokeBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
                
                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("teacher.todo.quiz.revoke", $encryptedId) }}';
                
                const csrf = document.createElement('input');
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.name = 'student_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            });
        }

        // Filter form AJAX submission
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
                });
            });
        }

        // Clear filter link
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
                });
            });
        }
    }
</script>
@endpush
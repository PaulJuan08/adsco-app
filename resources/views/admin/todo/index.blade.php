@extends('layouts.admin')

@section('title', 'To-Do — Quizzes & Assignments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">
    {{-- Delete Confirmation Modal --}}
    <div class="delete-modal" id="deleteModal">
        <div class="delete-modal-content">
            <div class="delete-modal-header">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="delete-modal-title">Confirm Deletion</h3>
            </div>
            <div class="delete-modal-body">
                <p id="deleteModalMessage">Are you sure you want to delete this item?</p>
                <div class="delete-modal-item" id="deleteModalItem"></div>
                <p style="margin-top: 1rem; font-size: 0.875rem; color: #dc2626;">
                    <i class="fas fa-info-circle"></i> This action cannot be undone. All associated data (submissions, attempts, etc.) will be permanently removed.
                </p>
            </div>
            <div class="delete-modal-actions">
                <button type="button" class="delete-modal-btn cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <form id="deleteForm" method="POST" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-modal-btn delete" id="confirmDeleteBtn">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="greeting-text">
                    <h1 class="welcome-title">To-Do Management</h1>
                    <p class="welcome-subtitle">
                        <i class="fas fa-tasks"></i> Manage quizzes & assignments — control student access
                    </p>
                </div>
            </div>
        </div>
    </div>

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

    {{-- Summary Stats --}}
    <div class="stats-grid-compact">
        <div class="stat-card stat-card-primary clickable-card" onclick="window.location.href='{{ route('admin.todo.index', ['type' => 'quiz']) }}'">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Quizzes</div>
                    <div class="stat-number">{{ number_format($totalQuizzes) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
            <div class="stat-link">
                View all quizzes <i class="fas fa-arrow-right"></i>
            </div>
        </div>
        
        <div class="stat-card stat-card-success clickable-card" onclick="window.location.href='{{ route('admin.todo.index', ['type' => 'assignment']) }}'">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Assignments</div>
                    <div class="stat-number">{{ number_format($totalAssignments) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-link">
                View all assignments <i class="fas fa-arrow-right"></i>
            </div>
        </div>
        
        <div class="stat-card stat-card-info clickable-card" onclick="window.location.href='{{ route('admin.todo.progress') }}'">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Active Access Grants</div>
                    <div class="stat-number">{{ number_format($totalAccess) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-link">
                View progress <i class="fas fa-arrow-right"></i>
            </div>
        </div>

        <div class="stat-card stat-card-warning clickable-card" onclick="window.location.href='{{ route('admin.todo.progress', ['type' => 'quiz']) }}'">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pending Reviews</div>
                    <div class="stat-number">{{ number_format($pendingReviews ?? 0) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-link">
                Review submissions <i class="fas fa-arrow-right"></i>
            </div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="header-actions" style="justify-content: space-between; margin-bottom: 1.5rem;">
        <div class="todo-tabs">
            <a href="{{ route('admin.todo.index') }}" 
               class="todo-tab {{ $type === 'all' ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> All
            </a>
            <a href="{{ route('admin.todo.index', ['type' => 'quiz']) }}" 
               class="todo-tab {{ $type === 'quiz' ? 'active' : '' }}">
                <i class="fas fa-brain"></i> Quizzes
            </a>
            <a href="{{ route('admin.todo.index', ['type' => 'assignment']) }}" 
               class="todo-tab {{ $type === 'assignment' ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Assignments
            </a>
        </div>
        
        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.todo.index') }}" style="display: flex; gap: 0.5rem;">
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Search by title..." 
                           class="search-input">
                </div>
                <button type="submit" class="btn-xs btn-xs-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                @if($search)
                <a href="{{ route('admin.todo.index', ['type' => $type]) }}" class="btn-xs btn-xs-outline">
                    <i class="fas fa-times"></i> Clear
                </a>
                @endif
            </form>
            
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.quizzes.create') }}" class="btn-xs btn-xs-primary">
                    <i class="fas fa-plus-circle"></i> New Quiz
                </a>
                <a href="{{ route('admin.assignments.create') }}" class="btn-xs btn-xs-success">
                    <i class="fas fa-plus-circle"></i> New Assignment
                </a>
                <a href="{{ route('admin.todo.progress') }}" class="btn-xs btn-xs-outline">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
            </div>
        </div>
    </div>

    {{-- Quizzes Section --}}
    @if($quizzes->isNotEmpty())
    <div class="section-heading">
        <i class="fas fa-brain"></i> Quizzes 
        <span class="badge badge-gray" style="margin-left: 0.5rem;">
            {{ $quizzes->count() }}
        </span>
    </div>

    <div class="todo-grid">
        @foreach($quizzes as $quiz)
        @php
            // Get recent quiz attempts with student info
            $recentAttempts = $quiz->attempts()
                ->with('user')
                ->whereNotNull('completed_at')
                ->latest()
                ->take(5)
                ->get();
                
            $attemptCount = $quiz->attempts_count ?? 0;
            $studentCount = $quiz->attempts()->distinct('user_id')->count('user_id');
            $passedCount = $quiz->attempts()->where('passed', 1)->count();
            $failedCount = $quiz->attempts()->where('passed', 0)->whereNotNull('completed_at')->count();
            $avgScore = $quiz->attempts()->whereNotNull('percentage')->avg('percentage');
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('admin.todo.quiz.show', Crypt::encrypt($quiz->id)) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon quiz">
                    <i class="fas fa-brain"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="todo-card-title">{{ $quiz->title }}</div>
                    <div class="todo-card-desc">
                        {{ Str::limit($quiz->description ?? 'No description', 60) }}
                    </div>
                    {{-- Creator info --}}
                    <div class="creator-info">
                        <i class="fas fa-user-circle"></i> 
                        <span class="creator-name">
                            @if($quiz->creator)
                                <span style="color: {{ $quiz->creator->role == 1 ? '#f59e0b' : ($quiz->creator->role == 3 ? '#48bb78' : 'inherit') }}; font-weight: 500;">
                                    {{ $quiz->creator->f_name }} {{ $quiz->creator->l_name }}
                                </span>
                                <span class="creator-badge" style="background: {{ $quiz->creator->role == 1 ? '#f59e0b' : ($quiz->creator->role == 3 ? '#48bb78' : '#718096') }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 6px;">
                                    {{ $quiz->creator->role == 1 ? 'Admin' : ($quiz->creator->role == 3 ? 'Teacher' : 'Staff') }}
                                </span>
                            @else
                                <span style="color: #a0aec0; font-style: italic;">System</span>
                                <span class="creator-badge" style="background: #a0aec0; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 6px;">
                                    Auto-generated
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="todo-card-meta">
                <span class="badge {{ $quiz->is_published ? 'badge-success' : 'badge-warning' }}">
                    <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                    {{ $quiz->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="badge badge-info">
                    <i class="fas fa-users"></i> {{ $quiz->allowed_students_count ?? 0 }} allowed
                </span>
                <span class="badge badge-gray">
                    <i class="fas fa-paper-plane"></i> {{ $attemptCount }} attempts
                </span>
                <span class="badge badge-gray">
                    <i class="fas fa-user-graduate"></i> {{ $studentCount }} students
                </span>
            </div>

            {{-- Students Preview --}}
            @if($recentAttempts->isNotEmpty())
            <div class="students-preview">
                <div class="students-preview-title">
                    <i class="fas fa-user-check"></i> Recent Attempts
                </div>
                <div class="students-grid">
                    @foreach($recentAttempts as $attempt)
                        @if($attempt->user)
                            <div class="student-chip" title="{{ $attempt->user->full_name ?? 'Unknown Student' }} - {{ $attempt->percentage }}%">
                                <span class="student-avatar-mini">
                                    {{ strtoupper(substr($attempt->user->f_name ?? '?', 0, 1)) }}
                                </span>
                                <span>{{ Str::limit($attempt->user->f_name ?? 'Unknown', 8) }}</span>
                                <span class="submission-info">
                                    {{ $attempt->percentage }}%
                                    <span class="status-indicator {{ $attempt->passed ? 'status-graded' : 'status-late' }}"></span>
                                </span>
                            </div>
                        @else
                            <div class="student-chip" title="Deleted Student - {{ $attempt->percentage }}%">
                                <span class="student-avatar-mini">
                                    <i class="fas fa-user-slash" style="font-size: 0.5rem;"></i>
                                </span>
                                <span>Deleted</span>
                                <span class="submission-info">
                                    {{ $attempt->percentage }}%
                                    <span class="status-indicator {{ $attempt->passed ? 'status-graded' : 'status-late' }}"></span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                @if($attemptCount > 5)
                <a href="{{ route('admin.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" 
                class="view-all-link" 
                onclick="event.stopPropagation()">
                    View all {{ $attemptCount }} attempts <i class="fas fa-arrow-right"></i>
                </a>
                @endif

                <div class="stats-mini">
                    <div class="stat-mini-item">
                        <i class="fas fa-check-circle"></i>
                        Passed: <span class="stat-mini-value">{{ $passedCount }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-times-circle"></i>
                        Failed: <span class="stat-mini-value">{{ $failedCount }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-chart-line"></i>
                        Avg: <span class="stat-mini-value">{{ $avgScore ? round($avgScore) . '%' : 'N/A' }}</span>
                    </div>
                </div>
            </div>
            @else
            <div class="students-preview" style="background: #f8fafc; text-align: center; padding: 1rem;">
                <i class="fas fa-user-clock" style="color: #cbd5e0; font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                <p style="font-size: 0.75rem; color: #718096;">No attempts yet</p>
            </div>
            @endif
            
            <div class="todo-card-actions" onclick="event.stopPropagation()">
                <a href="{{ route('admin.todo.quiz.show', Crypt::encrypt($quiz->id)) }}" 
                class="btn-xs btn-xs-primary">
                    <i class="fas fa-user-shield"></i> Manage Access
                </a>
                <a href="{{ route('admin.quizzes.edit', Crypt::encrypt($quiz->id)) }}" 
                class="btn-xs btn-xs-outline">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" 
                class="btn-xs btn-xs-outline">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                {{-- Delete Quiz Button --}}
                <button type="button" 
                        class="btn-xs btn-xs-danger" 
                        onclick="event.stopPropagation(); showDeleteModal('quiz', '{{ addslashes($quiz->title) }}', '{{ route('admin.quizzes.destroy', Crypt::encrypt($quiz->id)) }}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Assignments Section --}}
    @if($assignments->isNotEmpty())
    <div class="section-heading">
        <i class="fas fa-file-alt"></i> Assignments 
        <span class="badge badge-gray" style="margin-left: 0.5rem;">
            {{ $assignments->count() }}
        </span>
    </div>

    <div class="todo-grid">
        @foreach($assignments as $assignment)
        @php
            // Get recent submissions with student info
            $recentSubmissions = $assignment->submissions()
                ->with('student')
                ->latest()
                ->take(5)
                ->get();
                
            $submissionCount = $assignment->submissions_count ?? 0;
            $gradedCount = $assignment->submissions()->where('status', 'graded')->count();
            $pendingCount = $assignment->submissions()->whereIn('status', ['submitted', 'late'])->count();
            
            $avgScore = $assignment->submissions()
                ->whereNotNull('score')
                ->avg('score');
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('admin.todo.assignment.show', Crypt::encrypt($assignment->id)) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon assignment">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="todo-card-title">{{ $assignment->title }}</div>
                    <div class="todo-card-desc">
                        @if($assignment->course)
                            <span class="course-badge">
                                <i class="fas fa-book"></i> {{ $assignment->course->course_code ?? $assignment->course->name }}
                            </span>
                        @endif
                        @if($assignment->due_date)
                            <span style="margin-left: 0.5rem;">
                                <i class="fas fa-calendar"></i> Due {{ $assignment->due_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                    {{-- Creator info --}}
                    <div class="creator-info">
                        <i class="fas fa-user-circle"></i> 
                        <span class="creator-name">
                            @if($assignment->creator)
                                <span style="color: {{ $assignment->creator->role == 1 ? '#f59e0b' : ($assignment->creator->role == 3 ? '#48bb78' : 'inherit') }}; font-weight: 500;">
                                    {{ $assignment->creator->f_name }} {{ $assignment->creator->l_name }}
                                </span>
                                <span class="creator-badge" style="background: {{ $assignment->creator->role == 1 ? '#f59e0b' : ($assignment->creator->role == 3 ? '#48bb78' : '#718096') }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 6px;">
                                    {{ $assignment->creator->role == 1 ? 'Admin' : ($assignment->creator->role == 3 ? 'Teacher' : 'Staff') }}
                                </span>
                            @else
                                <span style="color: #a0aec0; font-style: italic;">System</span>
                                <span class="creator-badge" style="background: #a0aec0; color: white; padding: 2px 8px; border-radius: 12px; font-size: 10px; margin-left: 6px;">
                                    Auto-generated
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="todo-card-meta">
                <span class="badge {{ $assignment->is_published ? 'badge-success' : 'badge-warning' }}">
                    <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                    {{ $assignment->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="badge badge-info">
                    <i class="fas fa-users"></i> {{ $assignment->allowed_students_count ?? 0 }} allowed
                </span>
                <span class="badge badge-gray">
                    <i class="fas fa-upload"></i> {{ $submissionCount }} submitted
                </span>
                <span class="badge badge-primary">
                    <i class="fas fa-star"></i> {{ $assignment->points }} pts
                </span>
            </div>

            {{-- Students Preview --}}
            @if($recentSubmissions->isNotEmpty())
            <div class="students-preview">
                <div class="students-preview-title">
                    <i class="fas fa-user-check"></i> Recent Submissions
                </div>
                <div class="students-grid">
                    @foreach($recentSubmissions as $submission)
                        @php
                            $statusColor = match($submission->status) {
                                'graded' => 'status-graded',
                                'late' => 'status-late',
                                'submitted' => 'status-submitted',
                                default => 'status-pending'
                            };
                            $statusIcon = match($submission->status) {
                                'graded' => 'fa-check-circle',
                                'late' => 'fa-exclamation-circle',
                                'submitted' => 'fa-clock',
                                default => 'fa-hourglass'
                            };
                        @endphp
                        @if($submission->student)
                            <div class="student-chip" title="{{ $submission->student->full_name ?? 'Unknown Student' }} - {{ $submission->status }}">
                                <span class="student-avatar-mini">
                                    {{ strtoupper(substr($submission->student->f_name ?? '?', 0, 1)) }}
                                </span>
                                <span>{{ Str::limit($submission->student->f_name ?? 'Unknown', 8) }}</span>
                                <span class="submission-info">
                                    @if($submission->score)
                                        <i class="fas fa-star" style="color: #fbbf24;"></i> {{ $submission->score }}/{{ $assignment->points }}
                                    @endif
                                    <i class="fas {{ $statusIcon }}" style="font-size: 0.5rem;"></i>
                                    <span class="status-indicator {{ $statusColor }}"></span>
                                </span>
                            </div>
                        @else
                            <div class="student-chip" title="Deleted Student - {{ $submission->status }}">
                                <span class="student-avatar-mini">
                                    <i class="fas fa-user-slash" style="font-size: 0.5rem;"></i>
                                </span>
                                <span>Deleted</span>
                                <span class="submission-info">
                                    @if($submission->score)
                                        <i class="fas fa-star" style="color: #fbbf24;"></i> {{ $submission->score }}/{{ $assignment->points }}
                                    @endif
                                    <span class="status-indicator {{ $statusColor }}"></span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                @if($submissionCount > 5)
                <a href="{{ route('admin.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" 
                class="view-all-link"
                onclick="event.stopPropagation()">
                    View all {{ $submissionCount }} submissions <i class="fas fa-arrow-right"></i>
                </a>
                @endif

                <div class="stats-mini">
                    <div class="stat-mini-item">
                        <i class="fas fa-check-circle"></i>
                        Graded: <span class="stat-mini-value">{{ $gradedCount }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-clock"></i>
                        Pending: <span class="stat-mini-value">{{ $pendingCount }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-star"></i>
                        Avg Score: 
                        <span class="stat-mini-value">
                            {{ $avgScore ? round($avgScore, 1) : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
            @else
            <div class="students-preview" style="background: #f8fafc; text-align: center; padding: 1rem;">
                <i class="fas fa-user-clock" style="color: #cbd5e0; font-size: 1.5rem; margin-bottom: 0.25rem;"></i>
                <p style="font-size: 0.75rem; color: #718096;">No submissions yet</p>
            </div>
            @endif
            
            <div class="todo-card-actions" onclick="event.stopPropagation()">
                <a href="{{ route('admin.todo.assignment.show', Crypt::encrypt($assignment->id)) }}" 
                class="btn-xs btn-xs-primary">
                    <i class="fas fa-user-shield"></i> Manage Access
                </a>
                <a href="{{ route('admin.assignments.edit', Crypt::encrypt($assignment->id)) }}" 
                class="btn-xs btn-xs-outline">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" 
                class="btn-xs btn-xs-outline">
                    <i class="fas fa-chart-bar"></i> Progress
                </a>
                {{-- Delete Assignment Button --}}
                <button type="button" 
                        class="btn-xs btn-xs-danger" 
                        onclick="event.stopPropagation(); showDeleteModal('assignment', '{{ addslashes($assignment->title) }}', '{{ route('admin.assignments.destroy', Crypt::encrypt($assignment->id)) }}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Empty State --}}
    @if($quizzes->isEmpty() && $assignments->isEmpty())
    <div class="empty-todo">
        <i class="fas fa-clipboard-list"></i>
        <p class="empty-title">No items found</p>
        <p class="empty-text">
            {{ $search ? 'No results match your search criteria.' : 'Create a quiz or assignment to get started.' }}
        </p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <a href="{{ route('admin.quizzes.create') }}" class="btn-xs btn-xs-primary">
                <i class="fas fa-plus-circle"></i> New Quiz
            </a>
            <a href="{{ route('admin.assignments.create') }}" class="btn-xs btn-xs-success">
                <i class="fas fa-plus-circle"></i> New Assignment
            </a>
            @if($search)
            <a href="{{ route('admin.todo.index', ['type' => $type]) }}" class="btn-xs btn-xs-outline">
                <i class="fas fa-times"></i> Clear Search
            </a>
            @endif
        </div>
    </div>
    @endif

    {{-- Pagination --}}
    @if(method_exists($quizzes, 'links') || method_exists($assignments, 'links'))
    <div class="pagination-container" style="margin-top: 2rem;">
        @if($type === 'quiz' && method_exists($quizzes, 'links'))
            {{ $quizzes->links() }}
        @elseif($type === 'assignment' && method_exists($assignments, 'links'))
            {{ $assignments->links() }}
        @elseif($type === 'all')
            {{-- Handle pagination for both if needed --}}
        @endif
    </div>
    @endif

    {{-- Footer --}}
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            To-Do Management • Updated {{ now()->format('M d, Y h:i A') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    // Delete Modal functionality
    function showDeleteModal(type, title, deleteUrl) {
        const modal = document.getElementById('deleteModal');
        const message = document.getElementById('deleteModalMessage');
        const itemDisplay = document.getElementById('deleteModalItem');
        const deleteForm = document.getElementById('deleteForm');
        
        // Set message based on type
        message.textContent = `Are you sure you want to delete this ${type}?`;
        
        // Set icon based on type
        const icon = type === 'quiz' ? 'brain' : 'file-alt';
        itemDisplay.innerHTML = `<i class="fas fa-${icon}"></i> ${title}`;
        
        // Set form action
        deleteForm.action = deleteUrl;
        
        // Show modal
        modal.classList.add('active');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }
    
    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('active');
        
        // Restore body scrolling
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeDeleteModal();
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeDeleteModal();
            }
        });
        
        // Make stat cards clickable
        const clickableCards = document.querySelectorAll('.clickable-card');
        clickableCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on a link inside the card
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    return;
                }
                
                const link = this.querySelector('.stat-link');
                if (link && link.closest('a')) {
                    window.location.href = link.closest('a').href;
                } else if (this.dataset.href) {
                    window.location.href = this.dataset.href;
                }
            });
        });

        // Prevent event bubbling on action buttons and view-all links
        const actionButtons = document.querySelectorAll('.todo-card-actions a, .view-all-link, .todo-card-actions .btn-xs');
        actionButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Auto-submit search on Enter
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.form.submit();
                }
            });
        }

        // Add loading state to filter buttons
        const filterButtons = document.querySelectorAll('.btn-xs[type="submit"]');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
                this.disabled = true;
                
                // Re-enable after form submission (will be redirected)
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });

        // Add hover effects for todo cards
        const todoCards = document.querySelectorAll('.todo-card');
        todoCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)';
            });
        });

        // Show modal if there's a session flag (for assignment access)
        @if(session('open_access_modal'))
            const accessModal = document.getElementById('accessModal');
            if (accessModal) {
                accessModal.classList.add('active');
            }
        @endif
    });
</script>
@endpush
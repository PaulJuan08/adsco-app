@extends('layouts.admin')

@section('title', 'To-Do — Quizzes & Assignments')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
<style>
    .todo-card {
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .todo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    
    .todo-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .todo-card:hover::after {
        opacity: 0.03;
    }
    
    .students-preview {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid #edf2f7;
    }
    
    .students-preview-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .students-preview-title i {
        color: var(--primary);
    }
    
    .students-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .student-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 0.75rem;
        color: #2d3748;
        transition: all 0.2s;
    }
    
    .student-chip:hover {
        border-color: var(--primary);
        background: #fff3e0;
    }
    
    .student-chip i {
        color: var(--primary);
        font-size: 0.625rem;
    }
    
    .student-chip .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.25rem;
    }
    
    .status-submitted {
        background: #48bb78;
    }
    
    .status-graded {
        background: #667eea;
    }
    
    .status-late {
        background: #f56565;
    }
    
    .status-pending {
        background: #cbd5e0;
    }
    
    .view-all-link {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: gap 0.2s;
    }
    
    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }
    
    .student-avatar-mini {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        font-weight: 600;
        margin-right: 0.375rem;
    }
    
    .submission-info {
        font-size: 0.6875rem;
        color: #718096;
        margin-left: 0.5rem;
    }
    
    .stats-mini {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed #e2e8f0;
    }
    
    .stat-mini-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.6875rem;
        color: #718096;
    }
    
    .stat-mini-item i {
        color: var(--primary);
    }
    
    .stat-mini-value {
        font-weight: 600;
        color: #2d3748;
        margin-left: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-container">
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
    </div>

    {{-- Action Bar --}}
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
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
        
        <div class="header-actions">
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
            </form>
            
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

    {{-- Quizzes Section --}}
    @if($quizzes->isNotEmpty())
    <div class="section-heading">
        <i class="fas fa-brain"></i> Quizzes 
        <span style="font-size: 0.75rem; color: var(--gray-500); font-weight: 400; margin-left: 0.5rem;">
            ({{ $quizzes->count() }})
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
            $creator = $quiz->creator;
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('admin.todo.quiz.access', Crypt::encrypt($quiz->id)) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon quiz">
                    <i class="fas fa-brain"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="todo-card-title">{{ $quiz->title }}</div>
                    <div class="todo-card-desc">
                        {{ Str::limit($quiz->description, 60) }}
                    </div>
                    {{-- Creator info --}}
                    <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem;">
                        <i class="fas fa-user-circle"></i> Created by: 
                        @if($creator)
                            <span style="font-weight: 500;">{{ $creator->f_name }} {{ $creator->l_name }}</span>
                            <span style="color: #a0aec0;">({{ $creator->role == 1 ? 'Admin' : ($creator->role == 3 ? 'Teacher' : 'Unknown') }})</span>
                        @else
                            <span style="font-style: italic;">System / Unknown</span>
                        @endif
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
                        <div class="student-chip" title="{{ $attempt->user->full_name }} - {{ $attempt->percentage }}%">
                            <span class="student-avatar-mini">
                                {{ strtoupper(substr($attempt->user->f_name, 0, 1)) }}
                            </span>
                            <span>{{ Str::limit($attempt->user->f_name, 10) }}</span>
                            <span class="submission-info">
                                {{ $attempt->percentage }}%
                                <span class="status-indicator {{ $attempt->passed ? 'status-graded' : 'status-late' }}"></span>
                            </span>
                        </div>
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
                        Passed: <span class="stat-mini-value">{{ $quiz->attempts()->where('passed', 1)->count() }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-times-circle"></i>
                        Failed: <span class="stat-mini-value">{{ $quiz->attempts()->where('passed', 0)->whereNotNull('completed_at')->count() }}</span>
                    </div>
                    <div class="stat-mini-item">
                        <i class="fas fa-chart-line"></i>
                        Avg: <span class="stat-mini-value">{{ round($quiz->attempts()->whereNotNull('percentage')->avg('percentage')) }}%</span>
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
                <a href="{{ route('admin.todo.quiz.access', Crypt::encrypt($quiz->id)) }}" 
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
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Assignments Section --}}
    @if($assignments->isNotEmpty())
    <div class="section-heading">
        <i class="fas fa-file-alt"></i> Assignments 
        <span style="font-size: 0.75rem; color: var(--gray-500); font-weight: 400; margin-left: 0.5rem;">
            ({{ $assignments->count() }})
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
            $creator = $assignment->creator;
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('admin.todo.assignment.access', Crypt::encrypt($assignment->id)) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon assignment">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div class="todo-card-title">{{ $assignment->title }}</div>
                    <div class="todo-card-desc">
                        {{ $assignment->course?->course_name ?? 'No course' }}
                        @if($assignment->due_date)
                        · Due {{ $assignment->due_date->format('M d, Y') }}
                        @endif
                    </div>
                    {{-- Creator info --}}
                    <div style="font-size: 0.7rem; color: #718096; margin-top: 0.25rem;">
                        <i class="fas fa-user-circle"></i> Created by: 
                        @if($creator)
                            <span style="font-weight: 500;">{{ $creator->f_name }} {{ $creator->l_name }}</span>
                            <span style="color: #a0aec0;">({{ $creator->role == 1 ? 'Admin' : ($creator->role == 3 ? 'Teacher' : 'Unknown') }})</span>
                        @else
                            <span style="font-style: italic;">System / Unknown</span>
                        @endif
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
                <span class="badge badge-gray">
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
                        @endphp
                        <div class="student-chip" title="{{ $submission->student->full_name }} - {{ $submission->status }}">
                            <span class="student-avatar-mini">
                                {{ strtoupper(substr($submission->student->f_name, 0, 1)) }}
                            </span>
                            <span>{{ Str::limit($submission->student->f_name, 10) }}</span>
                            <span class="submission-info">
                                @if($submission->score)
                                    {{ $submission->score }}/{{ $assignment->points }}
                                @endif
                                <span class="status-indicator {{ $statusColor }}"></span>
                            </span>
                        </div>
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
                            @php
                                $avgScore = $assignment->submissions()
                                    ->whereNotNull('score')
                                    ->avg('score');
                            @endphp
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
                <a href="{{ route('admin.todo.assignment.access', Crypt::encrypt($assignment->id)) }}" 
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
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Empty State --}}
    @if($quizzes->isEmpty() && $assignments->isEmpty())
    <div class="empty-todo">
        <i class="fas fa-clipboard-list"></i>
        <p style="font-size: 1rem; font-weight: 600; color: var(--gray-700);">No items found</p>
        <p style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 1.5rem;">
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

    {{-- Footer --}}
    <footer class="dashboard-footer">
        <p>© {{ date('Y') }} School Management System. All rights reserved.</p>
        <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--space-2);">
            To-Do Management • Updated {{ now()->format('M d, Y') }}
        </p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make stat cards clickable
        const clickableCards = document.querySelectorAll('.clickable-card');
        clickableCards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on a link inside the card
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    return;
                }
                
                const link = this.querySelector('.stat-link');
                if (link) {
                    window.location.href = link.closest('a')?.href || link.href;
                }
            });
        });

        // Prevent event bubbling on action buttons
        const actionButtons = document.querySelectorAll('.todo-card-actions a, .view-all-link');
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
                    this.form.submit();
                }
            });
        }
    });
</script>
@endpush
@extends('layouts.teacher')

@section('title', 'Quizzes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/todo-index.css') }}">
@endpush

@section('content')
<div class="dashboard-container">

    {{-- Shared delete form --}}
    <form id="itemDeleteForm" method="POST" style="display:none;">
        @csrf @method('DELETE')
    </form>

    {{-- Header --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="user-greeting">
                <div class="user-avatar"><i class="fas fa-brain"></i></div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Quizzes</h1>
                    <p class="welcome-subtitle"><i class="fas fa-brain"></i> Manage your quizzes and student access</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid-compact">
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div><div class="stat-label">Total Quizzes</div><div class="stat-number">{{ number_format($totalQuizzes) }}</div></div>
                <div class="stat-icon"><i class="fas fa-brain"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div><div class="stat-label">Published</div><div class="stat-number">{{ number_format($publishedCount) }}</div></div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div><div class="stat-label">Drafts</div><div class="stat-number">{{ number_format($totalQuizzes - $publishedCount) }}</div></div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-info clickable-card" onclick="window.location.href='{{ route('teacher.todo.progress', ['type' => 'quiz']) }}'">
            <div class="stat-header">
                <div><div class="stat-label">View Progress</div><div class="stat-number"><i class="fas fa-chart-bar" style="font-size:1.2rem;"></i></div></div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="stat-link">Student attempts & results <i class="fas fa-arrow-right"></i></div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="header-actions" style="justify-content:space-between;margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('teacher.quizzes.index') }}" style="display:flex;gap:.5rem;">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search quizzes..." class="search-input" data-client-filter=".todo-card">
            </div>
            @if($search)
            <a href="{{ route('teacher.quizzes.index') }}" class="btn-xs btn-xs-outline"><i class="fas fa-times"></i> Clear</a>
            @endif
        </form>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <a href="{{ route('teacher.quizzes.create') }}" class="btn-xs btn-xs-primary">
                <i class="fas fa-plus-circle"></i> New Quiz
            </a>
            <a href="{{ route('teacher.todo.progress', ['type' => 'quiz']) }}" class="btn-xs btn-xs-outline">
                <i class="fas fa-chart-bar"></i> Progress
            </a>
            <a href="{{ route('teacher.todo.index') }}" class="btn-xs btn-xs-outline">
                <i class="fas fa-layer-group"></i> Overview
            </a>
        </div>
    </div>

    {{-- Quiz Grid --}}
    @if($quizzes->isNotEmpty())
    <div class="todo-grid">
        @foreach($quizzes as $quiz)
        @php
            $recentAttempts = $quiz->attempts()->with('user')->whereNotNull('completed_at')->latest()->take(5)->get();
            $attemptCount   = $quiz->attempts_count ?? 0;
            $studentCount   = $quiz->attempts()->distinct('user_id')->count('user_id');
            $passedCount    = $quiz->attempts()->where('passed', 1)->count();
            $failedCount    = $quiz->attempts()->where('passed', 0)->whereNotNull('completed_at')->count();
            $avgScore       = $quiz->attempts()->whereNotNull('percentage')->avg('percentage');
            $encId          = \Illuminate\Support\Facades\Crypt::encrypt($quiz->id);
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('teacher.todo.quiz.show', $encId) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon quiz"><i class="fas fa-brain"></i></div>
                <div style="flex:1;min-width:0;">
                    <div class="todo-card-title">{{ $quiz->title }}</div>
                    <div class="todo-card-desc">{{ Str::limit($quiz->description ?? 'No description', 60) }}</div>
                    @if($quiz->creator)
                    <div class="creator-info">
                        <i class="fas fa-user-circle"></i>
                        <span class="creator-name" style="color:#48bb78;font-weight:500;">
                            {{ $quiz->creator->f_name }} {{ $quiz->creator->l_name }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="todo-card-meta">
                <span class="badge {{ $quiz->is_published ? 'badge-success' : 'badge-warning' }}">
                    <i class="fas fa-circle" style="font-size:.4rem;"></i>
                    {{ $quiz->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="badge badge-info"><i class="fas fa-users"></i> {{ $quiz->allowed_students_count ?? 0 }} allowed</span>
                <span class="badge badge-gray"><i class="fas fa-paper-plane"></i> {{ $attemptCount }} attempts</span>
                <span class="badge badge-gray"><i class="fas fa-user-graduate"></i> {{ $studentCount }} students</span>
            </div>

            @if($recentAttempts->isNotEmpty())
            <div class="students-preview">
                <div class="students-preview-title"><i class="fas fa-user-check"></i> Recent Attempts</div>
                <div class="students-grid">
                    @foreach($recentAttempts as $attempt)
                    @if($attempt->user)
                    <div class="student-chip" title="{{ $attempt->user->f_name }} {{ $attempt->user->l_name }} – {{ $attempt->percentage }}%">
                        <span class="student-avatar-mini">{{ strtoupper(substr($attempt->user->f_name ?? '?', 0, 1)) }}</span>
                        <span>{{ Str::limit($attempt->user->f_name ?? 'Unknown', 8) }}</span>
                        <span class="submission-info">{{ $attempt->percentage }}%<span class="status-indicator {{ $attempt->passed ? 'status-graded' : 'status-late' }}"></span></span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @if($attemptCount > 5)
                <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="view-all-link" onclick="event.stopPropagation()">
                    View all {{ $attemptCount }} attempts <i class="fas fa-arrow-right"></i>
                </a>
                @endif
                <div class="stats-mini">
                    <div class="stat-mini-item"><i class="fas fa-check-circle"></i> Passed: <span class="stat-mini-value">{{ $passedCount }}</span></div>
                    <div class="stat-mini-item"><i class="fas fa-times-circle"></i> Failed: <span class="stat-mini-value">{{ $failedCount }}</span></div>
                    <div class="stat-mini-item"><i class="fas fa-chart-line"></i> Avg: <span class="stat-mini-value">{{ $avgScore ? round($avgScore) . '%' : 'N/A' }}</span></div>
                </div>
            </div>
            @else
            <div class="students-preview" style="background:#f8fafc;text-align:center;padding:1rem;">
                <i class="fas fa-user-clock" style="color:#cbd5e0;font-size:1.5rem;margin-bottom:.25rem;"></i>
                <p style="font-size:.75rem;color:#718096;">No attempts yet</p>
            </div>
            @endif

            <div class="card-footer-actions" onclick="event.stopPropagation()">
                <form method="POST" action="{{ route('teacher.quizzes.toggle-publish', $encId) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-toggle-status {{ $quiz->is_published ? 'published' : 'draft' }}">
                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                        <span class="toggle-label">{{ $quiz->is_published ? 'Published' : 'Draft' }}</span>
                    </button>
                </form>
                <div class="action-dropdown-wrapper">
                    <button class="btn-action-dots" onclick="toggleActionDropdown(this)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="action-dropdown-menu">
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.todo.quiz.access.modal', $encId) }}', 'Students — {{ addslashes($quiz->title) }}', '960px')" class="dropdown-item">
                            <i class="fas fa-users"></i> View Students
                        </button>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.todo.quiz.access.modal', $encId) }}', 'Grant Access — {{ addslashes($quiz->title) }}', '960px')" class="dropdown-item">
                            <i class="fas fa-key"></i> Grant Access
                        </button>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.quizzes.edit', $encId) }}', 'Edit Quiz')" class="dropdown-item">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="{{ route('teacher.todo.quiz.show', $encId) }}" class="dropdown-item" onclick="closeAllDropdowns()">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('teacher.todo.progress', ['type' => 'quiz', 'item_id' => $quiz->id]) }}" class="dropdown-item" onclick="closeAllDropdowns()">
                            <i class="fas fa-chart-bar"></i> Progress
                        </a>
                        <div class="dropdown-divider"></div>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); confirmDeleteItem('{{ $encId }}', '{{ addslashes($quiz->title) }}')" class="dropdown-item text-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-todo">
        <i class="fas fa-brain"></i>
        <p class="empty-title">No quizzes found</p>
        <p class="empty-text">{{ $search ? 'No results match your search.' : 'Create your first quiz to get started.' }}</p>
        <div style="display:flex;gap:.75rem;justify-content:center;">
            <a href="{{ route('teacher.quizzes.create') }}" class="btn-xs btn-xs-primary"><i class="fas fa-plus-circle"></i> New Quiz</a>
            @if($search)
            <a href="{{ route('teacher.quizzes.index') }}" class="btn-xs btn-xs-outline"><i class="fas fa-times"></i> Clear Search</a>
            @endif
        </div>
    </div>
    @endif

    @if($quizzes->hasPages())
    <div class="pagination-container" style="margin-top:2rem;">{{ $quizzes->appends(['search' => $search])->links() }}</div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function closeAllDropdowns() {
    document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
}
function toggleActionDropdown(btn) {
    if (!btn._menu) btn._menu = btn.nextElementSibling;
    var menu = btn._menu;
    var isOpen = menu.classList.contains('open');
    document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
    if (!isOpen) {
        if (menu.parentNode !== document.body) document.body.appendChild(menu);
        var rect = btn.getBoundingClientRect();
        menu.style.left = 'auto';
        menu.style.right = (window.innerWidth - rect.right) + 'px';
        if (rect.top > 130) {
            menu.style.top = 'auto';
            menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
        } else {
            menu.style.top = (rect.bottom + 4) + 'px';
            menu.style.bottom = 'auto';
        }
        menu.classList.add('open');
    }
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-dropdown-wrapper'))
        document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
});
window.addEventListener('scroll', function() {
    document.querySelectorAll('.action-dropdown-menu.open').forEach(function(d) { d.classList.remove('open'); });
}, true);
function confirmDeleteItem(encId, title) {
    var doDelete = function () { ajaxDelete('{{ url("teacher/quizzes") }}/' + encId); };
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Quiz?',
            html: `<strong>"${title}"</strong> and all its attempts will be permanently deleted.`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Yes, Delete',
        }).then(function (r) { if (r.isConfirmed) doDelete(); });
    } else if (confirm('Delete "' + title + '"? This cannot be undone.')) { doDelete(); }
}
</script>
@endpush

@extends('layouts.teacher')

@section('title', 'Assignments')

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
                <div class="user-avatar"><i class="fas fa-file-alt"></i></div>
                <div class="greeting-text">
                    <h1 class="welcome-title">My Assignments</h1>
                    <p class="welcome-subtitle"><i class="fas fa-file-alt"></i> Manage assignments and review submissions</p>
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
    <div class="stats-grid stats-grid-compact">
        <div class="stat-card stat-card-success">
            <div class="stat-header">
                <div><div class="stat-label">Total Assignments</div><div class="stat-number">{{ number_format($totalAssignments) }}</div></div>
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-primary">
            <div class="stat-header">
                <div><div class="stat-label">Published</div><div class="stat-number">{{ number_format($publishedCount) }}</div></div>
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-header">
                <div><div class="stat-label">Pending Reviews</div><div class="stat-number">{{ number_format($pendingReviews) }}</div></div>
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
        <div class="stat-card stat-card-info clickable-card" onclick="window.location.href='{{ route('teacher.todo.progress', ['type' => 'assignment']) }}'">
            <div class="stat-header">
                <div><div class="stat-label">View Progress</div><div class="stat-number"><i class="fas fa-chart-bar" style="font-size:1.2rem;"></i></div></div>
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="stat-link">Submissions & grades <i class="fas fa-arrow-right"></i></div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="header-actions" style="justify-content:space-between;margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('teacher.assignments.index') }}" style="display:flex;gap:.5rem;">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search assignments..." class="search-input" data-client-filter=".todo-card">
            </div>
            @if($search)
            <a href="{{ route('teacher.assignments.index') }}" class="btn-xs btn-xs-outline"><i class="fas fa-times"></i> Clear</a>
            @endif
        </form>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <button onclick="openCrudModal('{{ route('teacher.assignments.create') }}', 'New Assignment')" class="btn-xs btn-xs-success">
                <i class="fas fa-plus-circle"></i> New Assignment
            </button>
            <a href="{{ route('teacher.todo.progress', ['type' => 'assignment']) }}" class="btn-xs btn-xs-outline">
                <i class="fas fa-chart-bar"></i> Progress
            </a>
            <a href="{{ route('teacher.todo.index') }}" class="btn-xs btn-xs-outline">
                <i class="fas fa-layer-group"></i> Overview
            </a>
        </div>
    </div>

    {{-- Assignment Grid --}}
    @if($assignments->isNotEmpty())
    <div class="todo-grid">
        @foreach($assignments as $assignment)
        @php
            $recentSubmissions = $assignment->submissions()->with('student')->latest()->take(5)->get();
            $submissionCount   = $assignment->submissions_count ?? 0;
            $gradedCount       = $assignment->submissions()->where('status', 'graded')->count();
            $pendingCount      = $assignment->submissions()->whereIn('status', ['submitted', 'late'])->count();
            $avgScore          = $assignment->submissions()->whereNotNull('score')->avg('score');
            $encId             = \Illuminate\Support\Facades\Crypt::encrypt($assignment->id);
        @endphp
        <div class="todo-card" onclick="window.location.href='{{ route('teacher.todo.assignment.show', $encId) }}'">
            <div class="todo-card-header">
                <div class="todo-card-icon assignment"><i class="fas fa-file-alt"></i></div>
                <div style="flex:1;min-width:0;">
                    <div class="todo-card-title">{{ $assignment->title }}</div>
                    <div class="todo-card-desc">
                        @if($assignment->course)
                        <span class="course-badge"><i class="fas fa-book"></i> {{ $assignment->course->course_code ?? $assignment->course->title }}</span>
                        @endif
                        @if($assignment->due_date)
                        <span style="margin-left:.5rem;"><i class="fas fa-calendar"></i> Due {{ $assignment->due_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                    @if($assignment->creator)
                    <div class="creator-info">
                        <i class="fas fa-user-circle"></i>
                        <span class="creator-name" style="color:#48bb78;font-weight:500;">
                            {{ $assignment->creator->f_name }} {{ $assignment->creator->l_name }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="todo-card-meta">
                <span class="badge {{ $assignment->is_published ? 'badge-success' : 'badge-warning' }}">
                    <i class="fas fa-circle" style="font-size:.4rem;"></i>
                    {{ $assignment->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="badge badge-info"><i class="fas fa-users"></i> {{ $assignment->allowed_students_count ?? 0 }} allowed</span>
                <span class="badge badge-gray"><i class="fas fa-upload"></i> {{ $submissionCount }} submitted</span>
                <span class="badge badge-primary"><i class="fas fa-star"></i> {{ $assignment->points }} pts</span>
            </div>

            @if($recentSubmissions->isNotEmpty())
            <div class="students-preview">
                <div class="students-preview-title"><i class="fas fa-user-check"></i> Recent Submissions</div>
                <div class="students-grid">
                    @foreach($recentSubmissions as $submission)
                    @php
                        $statusClass = match($submission->status) { 'graded' => 'status-graded', 'late' => 'status-late', 'submitted' => 'status-submitted', default => 'status-pending' };
                    @endphp
                    @if($submission->student)
                    <div class="student-chip" title="{{ $submission->student->f_name }} {{ $submission->student->l_name }} – {{ $submission->status }}">
                        <span class="student-avatar-mini">{{ strtoupper(substr($submission->student->f_name ?? '?', 0, 1)) }}</span>
                        <span>{{ Str::limit($submission->student->f_name ?? 'Unknown', 8) }}</span>
                        <span class="submission-info">
                            @if($submission->score)<i class="fas fa-star" style="color:#fbbf24;"></i> {{ $submission->score }}/{{ $assignment->points }}@endif
                            <span class="status-indicator {{ $statusClass }}"></span>
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @if($submissionCount > 5)
                <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="view-all-link" onclick="event.stopPropagation()">
                    View all {{ $submissionCount }} submissions <i class="fas fa-arrow-right"></i>
                </a>
                @endif
                <div class="stats-mini">
                    <div class="stat-mini-item"><i class="fas fa-check-circle"></i> Graded: <span class="stat-mini-value">{{ $gradedCount }}</span></div>
                    <div class="stat-mini-item"><i class="fas fa-clock"></i> Pending: <span class="stat-mini-value">{{ $pendingCount }}</span></div>
                    <div class="stat-mini-item"><i class="fas fa-star"></i> Avg: <span class="stat-mini-value">{{ $avgScore ? round($avgScore, 1) : 'N/A' }}</span></div>
                </div>
            </div>
            @else
            <div class="students-preview" style="background:#f8fafc;text-align:center;padding:1rem;">
                <i class="fas fa-user-clock" style="color:#cbd5e0;font-size:1.5rem;margin-bottom:.25rem;"></i>
                <p style="font-size:.75rem;color:#718096;">No submissions yet</p>
            </div>
            @endif

            <div class="card-footer-actions" onclick="event.stopPropagation()">
                <form method="POST" action="{{ route('teacher.assignments.publish', $encId) }}" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-toggle-status {{ $assignment->is_published ? 'published' : 'draft' }}">
                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                        <span class="toggle-label">{{ $assignment->is_published ? 'Published' : 'Draft' }}</span>
                    </button>
                </form>
                <div class="action-dropdown-wrapper">
                    <button class="btn-action-dots" onclick="toggleActionDropdown(this)">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="action-dropdown-menu">
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.todo.assignment.access.modal', $encId) }}', 'Students — {{ addslashes($assignment->title) }}', '960px')" class="dropdown-item">
                            <i class="fas fa-users"></i> View Students
                        </button>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.todo.assignment.access.modal', $encId) }}', 'Grant Access — {{ addslashes($assignment->title) }}', '960px')" class="dropdown-item">
                            <i class="fas fa-key"></i> Grant Access
                        </button>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); openCrudModal('{{ route('teacher.assignments.edit', $encId) }}', 'Edit Assignment')" class="dropdown-item">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="{{ route('teacher.todo.assignment.show', $encId) }}" class="dropdown-item" onclick="closeAllDropdowns()">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="dropdown-item" onclick="closeAllDropdowns()">
                            <i class="fas fa-chart-bar"></i> Progress
                        </a>
                        <div class="dropdown-divider"></div>
                        <button onclick="event.stopPropagation(); closeAllDropdowns(); confirmDeleteItem('{{ route('teacher.assignments.destroy', $encId) }}', '{{ addslashes($assignment->title) }}')" class="dropdown-item text-danger">
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
        <i class="fas fa-file-alt"></i>
        <p class="empty-title">No assignments found</p>
        <p class="empty-text">{{ $search ? 'No results match your search.' : 'Create your first assignment to get started.' }}</p>
        <div style="display:flex;gap:.75rem;justify-content:center;">
            <button onclick="openCrudModal('{{ route('teacher.assignments.create') }}', 'New Assignment')" class="btn-xs btn-xs-success">
                <i class="fas fa-plus-circle"></i> New Assignment
            </button>
            @if($search)
            <a href="{{ route('teacher.assignments.index') }}" class="btn-xs btn-xs-outline"><i class="fas fa-times"></i> Clear Search</a>
            @endif
        </div>
    </div>
    @endif

    @if($assignments->hasPages())
    <div class="pagination-container" style="margin-top:2rem;">{{ $assignments->appends(['search' => $search])->links() }}</div>
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
function confirmDeleteItem(deleteUrl, title) {
    var doDelete = function () { ajaxDelete(deleteUrl); };
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Assignment?',
            html: `<strong>"${title}"</strong> and all its submissions will be permanently deleted.`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Yes, Delete',
        }).then(function (r) { if (r.isConfirmed) doDelete(); });
    } else if (confirm('Delete "' + title + '"? This cannot be undone.')) { doDelete(); }
}
</script>
@endpush

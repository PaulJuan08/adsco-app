@extends('layouts.admin')

@section('title', 'Assignments Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-index.css') }}">
<style>
    /* Additional custom styles if needed */
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --primary-light: rgba(245, 158, 11, 0.1);
    }
</style>
@endpush

@section('content')
<div class="form-container">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <h1 class="card-title">Assignments Management</h1>
        </div>
        <div class="header-actions">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <form method="GET" action="{{ route('admin.assignments.index') }}" style="display: inline;">
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Search assignments..." 
                           value="{{ request('search') }}">
                </form>
            </div>
            <a href="{{ route('admin.assignments.create') }}" class="top-action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Create Assignment</span>
            </a>
        </div>
    </div>

    {{-- Body --}}
    <div class="card-body">
        {{-- Stats Cards --}}
        <div class="stats-grid-compact">
            <div class="stat-card clickable-card" onclick="window.location.href='{{ route('admin.assignments.index') }}'">
                <div class="stat-header">
                    <span class="stat-label">Total Assignments</span>
                    <i class="fas fa-tasks stat-icon" style="color: #f59e0b;"></i>
                </div>
                <div class="stat-number">{{ $assignments->total() }}</div>
                <div class="stat-link">
                    <span>View all</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="stat-card clickable-card" onclick="window.location.href='{{ route('admin.assignments.index', ['status' => 'published']) }}'">
                <div class="stat-header">
                    <span class="stat-label">Published</span>
                    <i class="fas fa-check-circle stat-icon" style="color: #48bb78;"></i>
                </div>
                <div class="stat-number">{{ $assignments->where('is_published', true)->count() }}</div>
                <div class="stat-link">
                    <span>View published</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="stat-card clickable-card" onclick="window.location.href='{{ route('admin.assignments.index', ['status' => 'draft']) }}'">
                <div class="stat-header">
                    <span class="stat-label">Drafts</span>
                    <i class="fas fa-pen-square stat-icon" style="color: #f56565;"></i>
                </div>
                <div class="stat-number">{{ $assignments->where('is_published', false)->count() }}</div>
                <div class="stat-link">
                    <span>View drafts</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>

            <div class="stat-card clickable-card" onclick="window.location.href='{{ route('admin.todo.progress', ['type' => 'assignment']) }}'">
                <div class="stat-header">
                    <span class="stat-label">Submissions</span>
                    <i class="fas fa-file-upload stat-icon" style="color: #667eea;"></i>
                </div>
                <div class="stat-number">{{ \App\Models\AssignmentSubmission::count() }}</div>
                <div class="stat-link">
                    <span>View progress</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </div>
        </div>

        {{-- Filter Badge if search is active --}}
        @if(request('search'))
            <div style="margin-top: 1rem; display: flex; align-items: center;">
                <span class="filter-badge">
                    <i class="fas fa-search"></i>
                    Search: "{{ request('search') }}"
                </span>
                <a href="{{ route('admin.assignments.index') }}" style="color: #718096; font-size: 0.875rem;">
                    <i class="fas fa-times-circle"></i> Clear
                </a>
            </div>
        @endif

        {{-- Assignments Table --}}
        <div class="table-responsive" style="margin-top: 1.5rem;">
            <table class="assignment-table">
                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>Course / Topic</th>
                        <th>Due Date</th>
                        <th>Points</th>
                        <th>Status</th>
                        <th>Submissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr class="clickable-row" onclick="window.location.href='{{ route('admin.assignments.show', Crypt::encrypt($assignment->id)) }}'">
                            <td>
                                <div class="assignment-info-cell">
                                    <div class="assignment-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="assignment-details">
                                        <div class="assignment-title">{{ $assignment->title }}</div>
                                        <div class="assignment-desc">{{ Str::limit($assignment->description, 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($assignment->course)
                                    <span class="course-badge">
                                        <i class="fas fa-book"></i>
                                        {{ $assignment->course->course_name ?? 'N/A' }}
                                    </span>
                                @endif
                                @if($assignment->topic)
                                    <span class="topic-badge" style="margin-top: 0.25rem; display: inline-block;">
                                        <i class="fas fa-tag"></i>
                                        {{ $assignment->topic->name ?? 'N/A' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($assignment->due_date)
                                    <div style="font-weight: 600; font-size: 0.875rem; color: #2d3748;">
                                        {{ $assignment->due_date->format('M d, Y') }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #718096;">
                                        {{ $assignment->due_date->diffForHumans() }}
                                    </div>
                                @else
                                    <span style="color: #a0aec0;">No due date</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #f59e0b; font-size: 1rem;">
                                    {{ $assignment->points }}
                                </span>
                                <span style="font-size: 0.75rem; color: #718096;">pts</span>
                            </td>
                            <td>
                                @if($assignment->is_published)
                                    <span class="status-indicator status-published">
                                        <i class="fas fa-check-circle"></i>
                                        Published
                                    </span>
                                @else
                                    <span class="status-indicator status-draft">
                                        <i class="fas fa-pen"></i>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 600; font-size: 0.875rem; color: #2d3748;">
                                    {{ $assignment->submissions_count ?? 0 }}
                                </div>
                                <div style="font-size: 0.75rem; color: #718096;">
                                    {{ $assignment->allowed_students_count ?? 0 }} allowed
                                </div>
                            </td>
                            <td onclick="event.stopPropagation();">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.assignments.show', Crypt::encrypt($assignment->id)) }}" 
                                       class="btn-icon" 
                                       style="background: #667eea; color: white;"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.assignments.edit', Crypt::encrypt($assignment->id)) }}" 
                                       class="btn-icon" 
                                       style="background: #48bb78; color: white;"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.todo.assignment.access', Crypt::encrypt($assignment->id)) }}" 
                                       class="btn-icon" 
                                       style="background: #f59e0b; color: white;"
                                       title="Manage Access">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <button type="button"
                                            class="btn-icon" 
                                            style="background: #f56565; color: white;"
                                            onclick="confirmDelete('{{ Crypt::encrypt($assignment->id) }}')"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <h3>No assignments found</h3>
                                    <p>Get started by creating your first assignment</p>
                                    <a href="{{ route('admin.assignments.create') }}" class="top-action-btn" style="display: inline-flex;">
                                        <i class="fas fa-plus-circle"></i>
                                        Create Assignment
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination">
            {{ $assignments->links() }}
        </div>
    </div>
</div>

{{-- Delete Form --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function confirmDelete(encryptedId) {
    if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ url("admin/assignments") }}/' + encryptedId;
        form.submit();
    }
}

// Auto-submit search on input with debounce
let searchTimeout;
document.querySelector('.search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
@endpush
@extends('layouts.teacher')

@section('title', 'Edit Assignment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-form.css') }}">
<style>
    :root {
        --primary: #f59e0b;
        --primary-dark: #d97706;
        --primary-light: rgba(245, 158, 11, 0.1);
    }
    
    .due-date-warning {
        background: #fff3e0;
        border-left: 4px solid var(--primary);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: #744210;
    }
    
    .overdue-badge {
        background: #f56565;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="form-container">
    {{-- Header --}}
    <div class="card-header">
        <div class="card-title-group">
            <div class="card-icon">
                <i class="fas fa-edit"></i>
            </div>
            <h1 class="card-title">Edit Assignment: {{ $assignment->title }}</h1>
        </div>
        <a href="{{ route('teacher.todo.assignment.show', $encryptedId) }}" class="view-all-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Assignment</span>
        </a>
    </div>

    {{-- Body --}}
    <form method="POST" action="{{ route('teacher.assignments.update', $encryptedId) }}">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            {{-- Error Alert --}}
            @if($errors->any())
                <div class="error-alert">
                    <div class="error-alert-header">
                        <i class="fas fa-exclamation-circle"></i>
                        Please fix the following errors:
                    </div>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($assignment->isOverdue())
            <div class="due-date-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>This assignment is currently overdue.</strong> 
                Students cannot submit until you extend the due date. 
                <a href="#due-date" style="color: var(--primary-dark); font-weight: 600; text-decoration: underline;">Update due date below</a> to allow submissions.
            </div>
            @endif

            <div class="due-date-warning">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Once the due date passes, students will no longer be able to submit this assignment unless you extend the due date.
            </div>

            <div class="two-column-layout">
                {{-- Main Form Column --}}
                <div class="form-column">
                    {{-- Basic Information Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Basic Information
                        </h3>

                        <div class="form-group">
                            <label for="title" class="form-label required">Assignment Title</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   class="form-input @error('title') error @enderror" 
                                   value="{{ old('title', $assignment->title) }}"
                                   placeholder="e.g., Chapter 5 Research Paper"
                                   required>
                            @error('title')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      class="form-textarea @error('description') error @enderror" 
                                      placeholder="Provide a brief description of the assignment..."
                                      rows="4">{{ old('description', $assignment->description) }}</textarea>
                            @error('description')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="instructions" class="form-label">Instructions</label>
                            <textarea id="instructions" 
                                      name="instructions" 
                                      class="form-textarea @error('instructions') error @enderror" 
                                      placeholder="Detailed instructions for students..."
                                      rows="6">{{ old('instructions', $assignment->instructions) }}</textarea>
                            <span class="form-help">
                                <i class="fas fa-info-circle"></i>
                                Include step-by-step instructions, formatting requirements, etc.
                            </span>
                            @error('instructions')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    {{-- Assignment Details Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-cog"></i>
                            Assignment Settings
                        </h3>

                        <div class="form-grid">
                            <div class="form-group" id="due-date">
                                <label for="due_date" class="form-label">Due Date</label>
                                <div class="date-input-group">
                                    <input type="datetime-local" 
                                           id="due_date" 
                                           name="due_date" 
                                           class="form-input @error('due_date') error @enderror" 
                                           value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i>
                                    If no due date is set, the assignment will always be available for submission.
                                </span>
                                @error('due_date')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="points" class="form-label required">Total Points</label>
                                <input type="number" 
                                       id="points" 
                                       name="points" 
                                       class="form-input @error('points') error @enderror" 
                                       value="{{ old('points', $assignment->points) }}"
                                       min="1"
                                       required>
                                @error('points')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" 
                                   id="is_published" 
                                   name="is_published" 
                                   class="form-checkbox" 
                                   value="1"
                                   {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                            <label for="is_published" class="checkbox-label">
                                Published (available to students)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Sidebar Column --}}
                <div class="sidebar-column">
                    {{-- Preview Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-eye"></i>
                            Preview
                        </h3>
                        <div class="assignment-preview">
                            <div class="assignment-preview-icon" id="preview-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="assignment-preview-title" id="preview-title">
                                {{ old('title', $assignment->title) }}
                            </div>
                            <div class="assignment-preview-meta">
                                <span class="assignment-preview-badge {{ $assignment->is_published ? 'published' : 'draft' }}" id="preview-status">
                                    @if($assignment->is_published)
                                        <i class="fas fa-check-circle"></i> Published
                                    @else
                                        <i class="fas fa-pen"></i> Draft
                                    @endif
                                </span>
                            </div>
                            <div class="assignment-preview-meta" style="margin-top: 0.5rem;">
                                <span class="assignment-preview-badge" style="background: #f7fafc; color: #4a5568;" id="preview-due">
                                    @if($assignment->due_date)
                                        <i class="fas fa-calendar-alt"></i> Due: {{ $assignment->due_date->format('M d, Y h:i A') }}
                                        @if($assignment->isOverdue())
                                            <span class="overdue-badge">Overdue</span>
                                        @endif
                                    @else
                                        <i class="fas fa-infinity"></i> No due date
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-chart-bar"></i>
                            Current Stats
                        </h3>
                        <div class="stats-grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="stat-card" style="padding: 0.75rem;">
                                <div class="stat-icon" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-value" style="font-size: 1.25rem;">
                                    @php
                                        use App\Models\AssignmentStudentAccess;
                                        $allowedCount = AssignmentStudentAccess::where('assignment_id', $assignment->id)
                                            ->where('status', 'allowed')
                                            ->count();
                                    @endphp
                                    {{ $allowedCount }}
                                </div>
                                <div class="stat-label">Allowed</div>
                            </div>
                            <div class="stat-card" style="padding: 0.75rem;">
                                <div class="stat-icon" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div class="stat-value" style="font-size: 1.25rem;">
                                    @php
                                        use App\Models\AssignmentSubmission;
                                        $submissionsCount = AssignmentSubmission::where('assignment_id', $assignment->id)->count();
                                    @endphp
                                    {{ $submissionsCount }}
                                </div>
                                <div class="stat-label">Submitted</div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                        <div class="quick-actions-grid">
                            <a href="{{ route('teacher.todo.assignment.access', $encryptedId) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Manage Access</div>
                                    <div class="action-subtitle">Grant or revoke student access</div>
                                </div>
                                <i class="fas fa-chevron-right action-arrow"></i>
                            </a>

                            <a href="{{ route('teacher.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View Progress</div>
                                    <div class="action-subtitle">See all submissions and grades</div>
                                </div>
                                <i class="fas fa-chevron-right action-arrow"></i>
                            </a>
                        </div>
                    </div>

                    {{-- Due Date Guidelines --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-clock"></i>
                            Due Date Guidelines
                        </h3>
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span>Set a due date to control submission window</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span>Leave blank for always-available assignments</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span>After due date, students cannot submit</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                                <span>Extend due date to allow late submissions</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer-modern">
            <button type="button" onclick="window.location.href='{{ route('teacher.todo.assignment.show', $encryptedId) }}'" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </button>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i>
                Update Assignment
            </button>
        </div>
    </form>
</div>

{{-- Danger Zone --}}
@if(auth()->user()->isAdmin())
<div class="form-container" style="margin-top: 1rem;">
    <div class="card-header danger-zone">
        <div class="card-title-group">
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="card-title">Danger Zone</h1>
        </div>
    </div>
    <div class="card-body">
        <div class="danger-zone-header">
            <i class="fas fa-trash-alt"></i>
            Delete Assignment
        </div>
        <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1rem;">
            Once you delete this assignment, all associated submissions and data will be permanently removed. 
            This action cannot be undone.
        </p>
        <button type="button" onclick="confirmDelete('{{ $encryptedId }}')" class="btn btn-danger">
            <i class="fas fa-trash"></i>
            Permanently Delete Assignment
        </button>
    </div>
</div>
@endif

{{-- Delete Form --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// Live preview update
document.getElementById('title').addEventListener('input', function() {
    const title = this.value || 'Assignment Title';
    document.getElementById('preview-title').textContent = title;
});

document.getElementById('is_published').addEventListener('change', function() {
    const statusEl = document.getElementById('preview-status');
    if (this.checked) {
        statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Published';
        statusEl.className = 'assignment-preview-badge published';
    } else {
        statusEl.innerHTML = '<i class="fas fa-pen"></i> Draft';
        statusEl.className = 'assignment-preview-badge draft';
    }
});

document.getElementById('due_date').addEventListener('change', function() {
    const dueDateEl = document.getElementById('preview-due');
    if (this.value) {
        const date = new Date(this.value);
        const formattedDate = date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        dueDateEl.innerHTML = '<i class="fas fa-calendar-alt"></i> Due: ' + formattedDate;
    } else {
        dueDateEl.innerHTML = '<i class="fas fa-infinity"></i> No due date';
    }
});

function confirmDelete(encryptedId) {
    if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ url("teacher/assignments") }}/' + encryptedId;
        form.submit();
    }
}
</script>
@endpush
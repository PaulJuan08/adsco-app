@extends('layouts.admin')

@section('title', 'Edit Assignment')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/assignment-form.css') }}">
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
                <i class="fas fa-edit"></i>
            </div>
            <h1 class="card-title">Edit Assignment: {{ $assignment->title }}</h1>
        </div>
        <a href="{{ route('admin.assignments.show', $encryptedId) }}" class="view-all-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Assignment</span>
        </a>
    </div>

    {{-- Body --}}
    <form method="POST" action="{{ route('admin.assignments.update', $encryptedId) }}" enctype="multipart/form-data">
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

                    {{-- Course & Topic Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-book"></i>
                            Course & Topic
                        </h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="course_id" class="form-label required">Course</label>
                                <select id="course_id" 
                                        name="course_id" 
                                        class="form-select @error('course_id') error @enderror" 
                                        required>
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $assignment->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="topic_id" class="form-label">Topic (Optional)</label>
                                <select id="topic_id" 
                                        name="topic_id" 
                                        class="form-select @error('topic_id') error @enderror">
                                    <option value="">Select a topic</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic->id }}" {{ old('topic_id', $assignment->topic_id) == $topic->id ? 'selected' : '' }}>
                                            {{ $topic->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('topic_id')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Assignment Details Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-cog"></i>
                            Assignment Settings
                        </h3>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="due_date" class="form-label">Due Date</label>
                                <div class="date-input-group">
                                    <input type="datetime-local" 
                                           id="due_date" 
                                           name="due_date" 
                                           class="form-input @error('due_date') error @enderror" 
                                           value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
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

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="available_from" class="form-label">Available From</label>
                                <div class="date-input-group">
                                    <input type="datetime-local" 
                                           id="available_from" 
                                           name="available_from" 
                                           class="form-input @error('available_from') error @enderror" 
                                           value="{{ old('available_from', $assignment->available_from ? $assignment->available_from->format('Y-m-d\TH:i') : '') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                @error('available_from')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="available_until" class="form-label">Available Until</label>
                                <div class="date-input-group">
                                    <input type="datetime-local" 
                                           id="available_until" 
                                           name="available_until" 
                                           class="form-input @error('available_until') error @enderror" 
                                           value="{{ old('available_until', $assignment->available_until ? $assignment->available_until->format('Y-m-d\TH:i') : '') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                @error('available_until')
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

                    {{-- Attachment Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-paperclip"></i>
                            Attachment
                        </h3>

                        @if($assignment->attachment)
                            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f0fdf4; border-radius: 10px; border-left: 4px solid #48bb78;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <i class="fas fa-file-pdf" style="font-size: 2rem; color: #f59e0b;"></i>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #2d3748;">Current Attachment</div>
                                        <div style="font-size: 0.875rem; color: #718096;">{{ basename($assignment->attachment) }}</div>
                                    </div>
                                    <a href="{{ Storage::url($assignment->attachment) }}" 
                                       target="_blank"
                                       style="padding: 0.5rem 1rem; background: #48bb78; color: white; border-radius: 8px; text-decoration: none; font-size: 0.875rem;">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="attachment" class="form-label">
                                {{ $assignment->attachment ? 'Replace Attachment (Optional)' : 'Attachment (Optional)' }}
                            </label>
                            <div class="file-upload" onclick="document.getElementById('attachment').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="file-upload-text">Click to upload or drag and drop</div>
                                <div class="file-upload-subtext">PDF, DOC, DOCX, TXT (Max 10MB)</div>
                            </div>
                            <input type="file" 
                                   id="attachment" 
                                   name="attachment" 
                                   class="file-input"
                                   accept=".pdf,.doc,.docx,.txt">
                            <span class="form-help">
                                <i class="fas fa-info-circle"></i>
                                Upload a new file to replace the existing attachment
                            </span>
                            @error('attachment')
                                <span class="form-error">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div id="file-name" style="display: none; margin-top: 0.5rem; padding: 0.5rem; background: #f0fdf4; border-radius: 8px; color: #065f46;">
                            <i class="fas fa-check-circle"></i>
                            Selected file: <span id="selected-file-name"></span>
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
                                <div class="stat-value" style="font-size: 1.25rem;">{{ $assignment->allowed_students_count ?? 0 }}</div>
                                <div class="stat-label">Allowed</div>
                            </div>
                            <div class="stat-card" style="padding: 0.75rem;">
                                <div class="stat-icon" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div class="stat-value" style="font-size: 1.25rem;">{{ $assignment->submissions_count ?? 0 }}</div>
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
                            <a href="{{ route('admin.todo.assignment.access', $encryptedId) }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Manage Access</div>
                                    <div class="action-subtitle">Grant or revoke student access</div>
                                </div>
                                <i class="fas fa-chevron-right action-arrow"></i>
                            </a>

                            <a href="{{ route('admin.todo.progress', ['type' => 'assignment', 'item_id' => $assignment->id]) }}" class="action-card">
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

                    {{-- Guidelines Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i>
                            Editing Tips
                        </h3>
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Changes are saved immediately after update</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Published assignments are visible to students</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Existing submissions remain unchanged</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Access settings can be managed separately</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer-modern">
            <button type="button" onclick="window.location.href='{{ route('admin.assignments.show', $encryptedId) }}'" class="btn btn-secondary">
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

// File upload handling
document.getElementById('attachment').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    const fileDisplay = document.getElementById('file-name');
    const selectedFileName = document.getElementById('selected-file-name');
    
    if (fileName) {
        selectedFileName.textContent = fileName;
        fileDisplay.style.display = 'block';
    } else {
        fileDisplay.style.display = 'none';
    }
});

// Trigger file input on upload area click
document.querySelector('.file-upload').addEventListener('click', function() {
    document.getElementById('attachment').click();
});

// Drag and drop handling
const uploadArea = document.querySelector('.file-upload');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    uploadArea.style.background = 'linear-gradient(135deg, #f8fafc 0%, #fff 100%)';
    uploadArea.style.borderColor = '#f59e0b';
}

function unhighlight() {
    uploadArea.style.background = '#f8fafc';
    uploadArea.style.borderColor = '#e2e8f0';
}

uploadArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    document.getElementById('attachment').files = files;
    
    // Update file display
    if (files.length > 0) {
        document.getElementById('selected-file-name').textContent = files[0].name;
        document.getElementById('file-name').style.display = 'block';
    }
}

function confirmDelete(encryptedId) {
    if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = '{{ url("admin/assignments") }}/' + encryptedId;
        form.submit();
    }
}
</script>
@endpush
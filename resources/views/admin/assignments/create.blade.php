@extends('layouts.admin')

@section('title', 'Create Assignment')

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
                <i class="fas fa-plus-circle"></i>
            </div>
            <h1 class="card-title">Create New Assignment</h1>
        </div>
        <a href="{{ route('admin.assignments.index') }}" class="view-all-link">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Assignments</span>
        </a>
    </div>

    {{-- Body --}}
    <form method="POST" action="{{ route('admin.assignments.store') }}" enctype="multipart/form-data">
        @csrf
        
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
                                   value="{{ old('title') }}"
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
                                      rows="4">{{ old('description') }}</textarea>
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
                                      rows="6">{{ old('instructions') }}</textarea>
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
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                        <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
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
                                           value="{{ old('due_date') }}">
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
                                       value="{{ old('points', 100) }}"
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
                                           value="{{ old('available_from') }}">
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
                                           value="{{ old('available_until') }}">
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
                                   {{ old('is_published') ? 'checked' : '' }}>
                            <label for="is_published" class="checkbox-label">
                                Publish immediately (make available to students)
                            </label>
                        </div>
                        <span class="form-help" style="margin-left: 2rem;">
                            <i class="fas fa-info-circle"></i>
                            If unchecked, the assignment will be saved as a draft
                        </span>
                    </div>

                    {{-- Attachment Section --}}
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-paperclip"></i>
                            Attachment (Optional)
                        </h3>

                        <div class="form-group">
                            <label for="attachment" class="form-label">Attachment File</label>
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
                                Optional: Upload assignment file, rubric, or resources
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
                                {{ old('title') ?: 'Assignment Title' }}
                            </div>
                            <div class="assignment-preview-meta">
                                <span class="assignment-preview-badge" id="preview-status">
                                    <i class="fas fa-pen"></i>
                                    Draft
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i>
                            Assignment Tips
                        </h3>
                        <div class="tips-grid">
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Clear Instructions</div>
                                    <div class="tip-description">Provide step-by-step guidance</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Set Realistic Deadlines</div>
                                    <div class="tip-description">Consider student workload</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Define Grading Criteria</div>
                                    <div class="tip-description">Include rubric or scoring guide</div>
                                </div>
                            </div>
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-paperclip"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Provide Resources</div>
                                    <div class="tip-description">Attach templates or examples</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Guidelines Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i>
                            Guidelines
                        </h3>
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Title should be clear and descriptive</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Include all necessary instructions</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Set appropriate point value</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Specify availability dates if needed</span>
                            </div>
                            <div class="guideline-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Attach supporting materials</span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Card --}}
                    <div class="sidebar-card">
                        <h3 class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i>
                            Quick Info
                        </h3>
                        <div class="info-row">
                            <i class="fas fa-users info-icon"></i>
                            <div class="info-content">
                                <div class="info-label">Students</div>
                                <div class="info-text">Manage access after creation</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-calendar-alt info-icon"></i>
                            <div class="info-content">
                                <div class="info-label">Availability</div>
                                <div class="info-text">Set time restrictions if needed</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <i class="fas fa-save info-icon"></i>
                            <div class="info-content">
                                <div class="info-label">Save as Draft</div>
                                <div class="info-text">Uncheck publish to save draft</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer-modern">
            <button type="button" onclick="window.location.href='{{ route('admin.assignments.index') }}'" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
            </button>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i>
                Create Assignment
            </button>
        </div>
    </form>
</div>

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
</script>
@endpush
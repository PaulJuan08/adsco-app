@extends('layouts.teacher')

@section('title', isset($topic) ? 'Edit Topic - ' . $topic->title : 'Create New Topic')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-form.css') }}">
@endpush

@section('content')
    <!-- Topic Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas {{ isset($topic) ? 'fa-edit' : 'fa-plus' }} card-icon"></i>
                <h2 class="card-title">{{ isset($topic) ? 'Edit Topic' : 'Create New Topic' }}</h2>
            </div>
            <a href="{{ route('teacher.topics.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Topics
            </a>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview - Live Preview -->
            <div class="topic-preview">
                <div class="topic-preview-avatar" id="previewAvatar">
                    {{ isset($topic) ? strtoupper(substr($topic->title, 0, 1)) : '📚' }}
                </div>
                <div class="topic-preview-title" id="previewTitle">
                    {{ isset($topic) ? $topic->title : 'New Topic' }}
                </div>
                <div class="topic-preview-meta">
                    @if(isset($topic) && $topic->is_published)
                        <span class="status-badge status-published">
                            <i class="fas fa-check-circle"></i> Published
                        </span>
                    @elseif(isset($topic))
                        <span class="status-badge status-draft">
                            <i class="fas fa-clock"></i> Draft
                        </span>
                    @else
                        <span class="status-badge status-auto">
                            <i class="fas fa-rocket"></i> Auto-published
                        </span>
                    @endif
                </div>
            </div>

            <!-- Error Display -->
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            
            <!-- Auto-publish Notice for New Topics -->
            @if(!isset($topic))
            <div class="auto-publish-notice">
                <i class="fas fa-rocket"></i>
                <div>
                    <strong>Auto-published:</strong> Topics are automatically published and will be immediately visible to students.
                </div>
            </div>
            @endif

            <!-- Two Column Layout -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ isset($topic) ? route('teacher.topics.update', Crypt::encrypt($topic->id)) : route('teacher.topics.store') }}" method="POST" enctype="multipart/form-data" id="topicForm">
                        @csrf
                        @if(isset($topic))
                            @method('PUT')
                        @endif
                        
                        <!-- Hidden field to set topic as published -->
                        <input type="hidden" name="is_published" value="1">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            
                            <!-- Title -->
                            <div class="form-group">
                                <label for="title" class="form-label required">
                                    <i class="fas fa-heading"></i> Topic Title
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', $topic->title ?? '') }}"
                                       required
                                       placeholder="e.g., Introduction to Arrays"
                                       class="form-input @error('title') error @enderror">
                                @error('title')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter a descriptive title for your topic
                                </span>
                            </div>
                            
                            <!-- Description -->
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    placeholder="Describe what this topic covers..."
                                    class="form-textarea @error('description') error @enderror">{{ old('description', $topic->description ?? '') }}</textarea>
                                @error('description')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Optional: Add a brief description of the topic content
                                </span>
                            </div>
                        </div>
                        
                        <!-- Resources Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-paperclip"></i> Resources & Attachments
                            </div>
                            
                            <!-- PDF File Upload -->
                            <div class="form-group">
                                <label for="pdf_file" class="form-label">
                                    <i class="fas fa-file-pdf"></i> PDF Document
                                </label>
                                <input type="file" 
                                       id="pdf_file" 
                                       name="pdf_file" 
                                       accept=".pdf"
                                       class="form-file @error('pdf_file') error @enderror">
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Maximum file size: 10MB. PDF files only.
                                </span>
                                
                                <!-- Show current PDF if exists -->
                                @if(isset($topic) && $topic->pdf_file)
                                <div class="current-file">
                                    <div class="current-file-header">
                                        <div class="current-file-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="current-file-info">
                                            <div class="current-file-name">{{ basename($topic->pdf_file) }}</div>
                                            <div class="current-file-type">PDF Document</div>
                                        </div>
                                        <div class="current-file-actions">
                                            <a href="{{ asset($topic->pdf_file) }}" target="_blank" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ asset($topic->pdf_file) }}" download 
                                               class="btn btn-secondary btn-sm">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                    <div class="current-file-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Uploading a new PDF will replace the current one.</span>
                                    </div>
                                </div>
                                @endif
                                
                                @error('pdf_file')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            
                            <!-- Video Link -->
                            <div class="form-group">
                                <label for="video_link" class="form-label">
                                    <i class="fas fa-video"></i> Video Link
                                </label>
                                <input type="url" 
                                       id="video_link" 
                                       name="video_link" 
                                       value="{{ old('video_link', $topic->video_link ?? '') }}"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="form-input @error('video_link') error @enderror">
                                @error('video_link')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter YouTube, Vimeo, or direct video URL
                                </span>
                            </div>
                            
                            <!-- Attachment Link -->
                            <div class="form-group">
                                <label for="attachment" class="form-label">
                                    <i class="fas fa-link"></i> Attachment Link
                                </label>
                                <input type="url" 
                                       id="attachment" 
                                       name="attachment" 
                                       value="{{ old('attachment', $topic->attachment ?? '') }}"
                                       placeholder="https://drive.google.com/file/..."
                                       class="form-input @error('attachment') error @enderror">
                                @error('attachment')
                                    <span class="form-error">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </span>
                                @enderror
                                <span class="form-help">
                                    <i class="fas fa-info-circle"></i> Enter Google Drive, Dropbox, or direct file URL
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - Sidebar -->
                <div class="sidebar-column">
                    <!-- Quick Tips Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-lightbulb"></i> Quick Tips
                        </div>
                        
                        <div class="tips-grid">
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Auto-published</div>
                                    <div class="tip-description">Topics publish immediately and are visible to students</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">PDF Files</div>
                                    <div class="tip-description">Max 10MB, PDF format only. Replaces existing file</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Video Links</div>
                                    <div class="tip-description">YouTube, Vimeo, or direct video URLs supported</div>
                                </div>
                            </div>
                            
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Attachment Links</div>
                                    <div class="tip-description">Google Drive, Dropbox, or direct file URLs</div>
                                </div>
                            </div>
                            
                            <!-- Teacher-specific tip -->
                            <div class="tip-item">
                                <div class="tip-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="tip-content">
                                    <div class="tip-title">Assign to Courses</div>
                                    <div class="tip-description">After saving, assign topic to your courses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Title should be clear and descriptive</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Description helps students understand the topic</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">Resources are optional but enhance learning</span>
                            </div>
                            <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                                <i class="fas fa-check-circle" style="color: #48bb78; font-size: 0.875rem; margin-top: 0.125rem;"></i>
                                <span style="font-size: 0.75rem; color: #4a5568;">All topics are published automatically</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Topic Summary Card (for both Create and Edit mode) -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-chart-simple"></i> Topic Summary
                        </div>
                        
                        <div class="summary-stats">
                            @if(isset($topic))
                                <!-- Edit Mode Summary -->
                                <div class="summary-row">
                                    <span class="summary-label">Topic ID</span>
                                    <span class="summary-value">#{{ $topic->id }}</span>
                                </div>
                                <div class="divider"></div>
                                <div class="summary-row">
                                    <span class="summary-label">Status</span>
                                    <span class="summary-value">
                                        @if($topic->is_published)
                                            <span style="color: #48bb78;">Published</span>
                                        @else
                                            <span style="color: #ed8936;">Draft</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Visibility</span>
                                    <span class="summary-value">
                                        @if($topic->is_published)
                                            Public
                                        @else
                                            Private
                                        @endif
                                    </span>
                                </div>
                                <div class="divider"></div>
                                <div class="summary-row">
                                    <span class="summary-label">Created</span>
                                    <span class="summary-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Last Updated</span>
                                    <span class="summary-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                                </div>
                            @else
                                <!-- Create Mode Summary -->
                                <div class="summary-row">
                                    <span class="summary-label">Status</span>
                                    <span class="summary-value">
                                        <span style="color: #3182ce;">Auto-published</span>
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Visibility</span>
                                    <span class="summary-value">Public - Visible to all students</span>
                                </div>
                                <div class="divider"></div>
                                <div class="summary-row">
                                    <span class="summary-label">Publication</span>
                                    <span class="summary-value">Immediate upon creation</span>
                                </div>
                            @endif
                            
                            <div class="divider"></div>
                            
                            @php
                                $resources = 0;
                                if(isset($topic)) {
                                    if($topic->pdf_file) $resources++;
                                    if($topic->video_link) $resources++;
                                    if($topic->attachment) $resources++;
                                }
                            @endphp
                            
                            <div class="summary-row">
                                <span class="summary-label">Resources</span>
                                <span class="summary-value">
                                    @if(isset($topic))
                                        {{ $resources }} file(s)
                                    @else
                                        0 files
                                    @endif
                                </span>
                            </div>
                            
                            @if(isset($topic))
                            <div class="summary-row">
                                <span class="summary-label">Courses</span>
                                <span class="summary-value">{{ $topic->courses ? $topic->courses->count() : 0 }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <a href="{{ route('teacher.topics.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" form="topicForm" class="btn btn-primary" id="submitButton">
                <i class="fas {{ isset($topic) ? 'fa-save' : 'fa-plus-circle' }}"></i>
                {{ isset($topic) ? 'Update Topic' : 'Create Topic' }}
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('previewTitle');
        const previewAvatar = document.getElementById('previewAvatar');
        const submitButton = document.getElementById('submitButton');
        
        // Live preview update
        function updatePreview() {
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Topic';
            
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            } else {
                previewAvatar.textContent = '📚';
            }
        }
        
        if (titleInput) {
            titleInput.addEventListener('input', updatePreview);
        }
        
        // File size validation for PDF upload
        const pdfFileInput = document.getElementById('pdf_file');
        if (pdfFileInput) {
            pdfFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = file.size / 1024 / 1024;
                    const maxSize = 10;
                    
                    if (fileSize > maxSize) {
                        Swal.fire({
                            title: 'File Too Large',
                            text: `File size (${fileSize.toFixed(2)}MB) exceeds maximum allowed size (${maxSize}MB).`,
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        this.value = '';
                    }
                    
                    if (!file.name.toLowerCase().endsWith('.pdf')) {
                        Swal.fire({
                            title: 'Invalid File Type',
                            text: 'Only PDF files are allowed.',
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        this.value = '';
                    }
                }
            });
        }
        
        // URL validation for video link
        const videoLinkInput = document.getElementById('video_link');
        if (videoLinkInput) {
            videoLinkInput.addEventListener('blur', function() {
                if (this.value && !this.value.match(/^https?:\/\//i)) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // URL validation for attachment link
        const attachmentInput = document.getElementById('attachment');
        if (attachmentInput) {
            attachmentInput.addEventListener('blur', function() {
                if (this.value && !this.value.match(/^https?:\/\//i)) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // Form validation and submission with SweetAlert2
        const form = document.getElementById('topicForm');
        
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const titleInput = document.getElementById('title');
                const title = titleInput.value.trim();
                
                if (!title) {
                    titleInput.classList.add('error');
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter a topic title.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    titleInput.focus();
                    return false;
                } else {
                    titleInput.classList.remove('error');
                }
                
                @if(isset($topic))
                // Edit mode confirmation
                Swal.fire({
                    title: 'Update Topic?',
                    text: 'This topic will remain ' + ('{{ $topic->is_published }}' ? 'published' : 'in draft') + ' and visible to students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Update',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                        submitButton.disabled = true;
                        form.submit();
                    }
                });
                @else
                // Create mode confirmation
                Swal.fire({
                    title: 'Create Topic?',
                    text: 'This topic will be automatically published and visible to students.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Create',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                        submitButton.disabled = true;
                        form.submit();
                    }
                });
                @endif
            });
        }
        
        // Show notifications from session
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'success',
                title: '{{ session('success') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: '{{ session('error') }}',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
@endpush
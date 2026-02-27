@extends('layouts.teacher')

@section('title', 'Create New Topic')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-form.css') }}">
@endpush

@section('content')
    <!-- Topic Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-plus card-icon"></i>
                <h2 class="card-title">Create New Topic</h2>
            </div>
            <a href="{{ route('teacher.topics.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Topics
            </a>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview - Live Preview -->
            <div class="topic-preview">
                <div class="topic-preview-avatar" id="previewAvatar">
                    <span id="avatarLetter">📚</span>
                </div>
                <div class="topic-preview-title" id="previewTitle">New Topic</div>
                <div class="topic-preview-code" id="previewCode">---</div>
                <div class="topic-preview-status status-draft" id="previewStatus">
                    <i class="fas fa-clock"></i> Draft
                </div>
            </div>

            <!-- Publish Toggle -->
            <div class="publish-toggle-container">
                <div class="publish-info">
                    <div class="publish-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="publish-text">
                        <h4>Topic Visibility</h4>
                        <p>Control whether this topic is visible to students</p>
                    </div>
                </div>
                <div class="toggle-wrapper">
                    <div class="toggle-status" id="toggleStatusText">
                        <span class="status-draft"><i class="fas fa-clock"></i> Draft</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="publishToggle" name="is_published" value="1" form="topicForm">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Display validation errors -->
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
            
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ route('teacher.topics.store') }}" method="POST" enctype="multipart/form-data" id="topicForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            
                            <div class="form-group">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading"></i> Topic Title
                                    <span class="required">*</span>
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}" 
                                       required
                                       placeholder="e.g., Introduction to Arrays"
                                       class="form-input @error('title') error @enderror">
                                @error('title')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Enter a descriptive title for your topic
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="4"
                                    placeholder="Describe what this topic covers..."
                                    class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: Add a brief description of the topic content
                                </div>
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
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Maximum file size: 10MB. PDF files only.
                                </div>
                                @error('pdf_file')
                                    <div class="form-error">{{ $message }}</div>
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
                                       value="{{ old('video_link') }}"
                                       placeholder="https://www.youtube.com/watch?v=..."
                                       class="form-input @error('video_link') error @enderror">
                                @error('video_link')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Enter YouTube, Vimeo, or direct video URL
                                </div>
                            </div>
                            
                            <!-- Attachment Link -->
                            <div class="form-group">
                                <label for="attachment" class="form-label">
                                    <i class="fas fa-link"></i> Attachment Link
                                </label>
                                <input type="url" 
                                       id="attachment" 
                                       name="attachment" 
                                       value="{{ old('attachment') }}"
                                       placeholder="https://drive.google.com/file/..."
                                       class="form-input @error('attachment') error @enderror">
                                @error('attachment')
                                    <div class="form-error">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Enter Google Drive, Dropbox, or direct file URL
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="status" value="active">
                    </form>
                </div>
                
                <!-- Right Column - Guidelines Sidebar -->
                <div class="sidebar-column">
                    <!-- Guidelines Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-clipboard-check"></i> Guidelines
                        </div>
                        
                        <div class="guidelines-list">
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-asterisk"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Required Fields</div>
                                    <div class="guideline-text">Fields marked with * are mandatory</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-heading"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Topic Title</div>
                                    <div class="guideline-text">Be clear and descriptive about the content</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">PDF Files</div>
                                    <div class="guideline-text">Max 10MB, PDF format only</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Publish Status</div>
                                    <div class="guideline-text">Toggle to make topic visible to students</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('teacher.topics.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-chalkboard"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All Topics</div>
                                    <div class="action-subtitle">Browse existing topics</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                            
                            <button type="button" onclick="resetForm()" class="action-card" style="width: 100%; border: none; background: #f8fafc; text-align: left; cursor: pointer;">
                                <div class="action-icon">
                                    <i class="fas fa-eraser"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Clear Form</div>
                                    <div class="action-subtitle">Reset all fields</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </button>
                            
                            <a href="{{ route('teacher.topics.create') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Refresh Data</div>
                                    <div class="action-subtitle">Reload form</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <div class="form-actions">
                <a href="{{ route('teacher.topics.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" form="topicForm" class="btn btn-primary" id="submitButton">
                    <i class="fas fa-save"></i> Create Topic
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('previewTitle');
        const previewCode = document.getElementById('previewCode');
        const previewStatus = document.getElementById('previewStatus');
        const publishToggle = document.getElementById('publishToggle');
        const toggleStatusText = document.getElementById('toggleStatusText');
        const avatarLetter = document.getElementById('avatarLetter');
        const submitButton = document.getElementById('submitButton');
        const resourcesCount = document.getElementById('resourcesCount');
        
        // Live preview update
        function updatePreview() {
            // Update title
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Topic';
            
            // Update avatar
            if (title) {
                avatarLetter.textContent = title.charAt(0).toUpperCase();
            } else {
                avatarLetter.textContent = '📚';
            }
            
            // For topics, we'll use first few letters as code preview
            if (title) {
                const words = title.split(' ');
                let code = '';
                if (words.length >= 2) {
                    code = words[0].substring(0, 2).toUpperCase() + words[1].substring(0, 1).toUpperCase();
                } else {
                    code = title.substring(0, 3).toUpperCase();
                }
                previewCode.textContent = code;
            } else {
                previewCode.textContent = '---';
            }
        }
        
        // Update publish status
        function updatePublishStatus() {
            const isPublished = publishToggle.checked;
            
            if (isPublished) {
                toggleStatusText.innerHTML = '<span class="status-published"><i class="fas fa-check-circle"></i> Published</span>';
                previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Published';
                previewStatus.className = 'topic-preview-status status-published';
            } else {
                toggleStatusText.innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
                previewStatus.innerHTML = '<i class="fas fa-clock"></i> Draft';
                previewStatus.className = 'topic-preview-status status-draft';
            }
        }
        
        titleInput.addEventListener('input', updatePreview);
        publishToggle.addEventListener('change', updatePublishStatus);
        
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
                    } else if (!file.name.toLowerCase().endsWith('.pdf')) {
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
        
        // Form validation and submission
        const topicForm = document.getElementById('topicForm');
        if (topicForm) {
            topicForm.addEventListener('submit', function(e) {
                const title = titleInput.value.trim();
                
                let isValid = true;
                
                if (!title) {
                    titleInput.classList.add('error');
                    isValid = false;
                } else {
                    titleInput.classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showToast('Please enter a topic title.', 'error');
                    return;
                }
                
                // Show loading state
                if (submitButton) {
                    const originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                    submitButton.disabled = true;
                    
                    // Revert after 5 seconds (in case submission fails)
                    setTimeout(() => {
                        submitButton.innerHTML = originalHTML;
                        submitButton.disabled = false;
                    }, 5000);
                }
            });
        }
        
        // Toast notification function
        window.showToast = function(message, type = 'info') {
            // Remove existing toast if any
            const existingToast = document.querySelector('.custom-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;
            
            let icon = 'info-circle';
            if (type === 'success') icon = 'check-circle';
            if (type === 'error') icon = 'exclamation-circle';
            
            toast.innerHTML = `
                <i class="fas fa-${icon}" style="font-size: 1.25rem;"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
        
        // Show validation errors if any
        @if($errors->any())
            showToast('Please fix the errors in the form.', 'error');
        @endif
        
        // Show success message if redirected with success
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    });
    
    // Reset form function
    function resetForm() {
        document.getElementById('topicForm').reset();
        document.getElementById('previewTitle').textContent = 'New Topic';
        document.getElementById('previewCode').textContent = '---';
        document.getElementById('avatarLetter').textContent = '📚';
        document.getElementById('publishToggle').checked = false;
        document.getElementById('toggleStatusText').innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
        document.getElementById('previewStatus').innerHTML = '<i class="fas fa-clock"></i> Draft';
        document.getElementById('previewStatus').className = 'topic-preview-status status-draft';
        
        // Clear error states
        document.querySelectorAll('.form-input, .form-textarea, .form-file').forEach(el => {
            el.classList.remove('error');
        });
        
        showToast('Form has been cleared.', 'info');
    }
</script>
@endpush
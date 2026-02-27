@extends('layouts.admin')

@section('title', 'Edit Topic - ' . $topic->title)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/topic-form.css') }}">
@endpush

@section('content')
    <!-- Topic Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-edit card-icon"></i>
                <h2 class="card-title">Edit Topic</h2>
            </div>
            <a href="{{ route('admin.topics.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Topics
            </a>
        </div>
        
        <div class="card-body">
            <!-- Topic Preview - Live Preview (matching course preview style) -->
            <div class="topic-preview">
                <div class="topic-preview-avatar" id="previewAvatar">
                    {{ strtoupper(substr($topic->title, 0, 1)) }}
                </div>
                <div class="topic-preview-title" id="previewTitle">{{ $topic->title }}</div>
                <div class="topic-preview-code" id="previewCode">
                    @php
                        $words = explode(' ', $topic->title);
                        $code = '';
                        if (count($words) >= 2) {
                            $code = substr($words[0], 0, 2) . substr($words[1], 0, 1);
                        } else {
                            $code = substr($topic->title, 0, 3);
                        }
                        echo strtoupper($code);
                    @endphp
                </div>
                <div class="topic-preview-status {{ $topic->is_published ? 'status-published' : 'status-draft' }}" id="previewStatus">
                    <i class="fas {{ $topic->is_published ? 'fa-check-circle' : 'fa-clock' }}"></i>
                    {{ $topic->is_published ? 'Published' : 'Draft' }}
                </div>
            </div>

            <!-- Publish Toggle (exactly matching course style) -->
            <div class="publish-toggle-container">
                <div class="publish-info">
                    <div class="publish-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="publish-text">
                        <h4>Topic Visibility</h4>
                        <p>Toggle to change topic publication status</p>
                    </div>
                </div>
                <div class="toggle-wrapper">
                    <div class="toggle-status" id="toggleStatusText">
                        @if($topic->is_published)
                            <span class="status-published"><i class="fas fa-check-circle"></i> Published</span>
                        @else
                            <span class="status-draft"><i class="fas fa-clock"></i> Draft</span>
                        @endif
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="publishToggle" name="is_published" value="1" form="topicForm" {{ $topic->is_published ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Publish Info Card (exactly matching course style) -->
            <div class="publish-info-card">
                <div class="publish-info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="publish-info-content">
                    <div class="publish-info-label">Current Status</div>
                    <div class="publish-info-value">
                        @if($topic->is_published)
                            <span class="publish-badge published"><i class="fas fa-check-circle"></i> Published</span>
                            <span style="font-size: 0.75rem; color: #718096;">Visible to all enrolled students</span>
                        @else
                            <span class="publish-badge draft"><i class="fas fa-clock"></i> Draft</span>
                            <span style="font-size: 0.75rem; color: #718096;">Only visible to instructors and admins</span>
                        @endif
                    </div>
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
            
            <!-- Display success message if any -->
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <!-- Update Form -->
                    <form action="{{ route('admin.topics.update', Crypt::encrypt($topic->id)) }}" method="POST" enctype="multipart/form-data" id="topicForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden field for publish status - will be updated by toggle -->
                        <input type="hidden" name="is_published" id="is_published_field" value="{{ $topic->is_published ? '1' : '0' }}">
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i> Topic Title
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $topic->title) }}" 
                                           required
                                           class="form-input @error('title') error @enderror"
                                           placeholder="e.g., Introduction to Arrays">
                                    @error('title')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          class="form-textarea @error('description') error @enderror"
                                          placeholder="Describe what this topic covers...">{{ old('description', $topic->description) }}</textarea>
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
                            
                            <div class="form-grid">
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
                                    
                                    <!-- Show current PDF if exists -->
                                    @if($topic->pdf_file)
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
                                                <a href="{{ asset('pdf/' . $topic->pdf_file) }}" target="_blank" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ asset('pdf/' . $topic->pdf_file) }}" download 
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
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-grid" style="margin-top: 1rem;">
                                <div class="form-group">
                                    <label for="video_link" class="form-label">
                                        <i class="fas fa-video"></i> Video Link
                                    </label>
                                    <input type="url" 
                                           id="video_link" 
                                           name="video_link" 
                                           value="{{ old('video_link', $topic->video_link) }}"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           class="form-input @error('video_link') error @enderror">
                                    @error('video_link')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Enter YouTube, Vimeo, or direct video URL
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="attachment" class="form-label">
                                        <i class="fas fa-link"></i> Attachment Link
                                    </label>
                                    <input type="url" 
                                           id="attachment" 
                                           name="attachment" 
                                           value="{{ old('attachment', $topic->attachment) }}"
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
                        </div>
                    </form>
                </div>
                
                <!-- Right Column - Sidebar (matching course style) -->
                <div class="sidebar-column">
                    <!-- Topic Information Card (like Course Information) -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-info-circle"></i> Topic Information
                        </div>
                        
                        <!-- Statistics -->
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-hashtag"></i> Topic ID</span>
                            <span class="info-value">#{{ $topic->id }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-calendar-alt"></i> Created</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->created_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-clock"></i> Last Updated</span>
                            <div style="text-align: right;">
                                <span class="info-value">{{ $topic->updated_at->format('M d, Y') }}</span>
                                <div class="info-subvalue">{{ $topic->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-book"></i> Courses</span>
                            <span class="info-value">{{ $topic->courses ? $topic->courses->count() : 0 }}</span>
                        </div>
                        
                        @php
                            $resources = 0;
                            if($topic->pdf_file) $resources++;
                            if($topic->video_link) $resources++;
                            if($topic->attachment) $resources++;
                        @endphp
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-paperclip"></i> Resources</span>
                            <span class="info-value" id="resourcesCount">{{ $resources }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label"><i class="fas fa-tag"></i> Status</span>
                            <span class="info-value" id="summaryStatus">
                                @if($topic->is_published)
                                    <span style="color: #48bb78;">Published</span>
                                @else
                                    <span style="color: #ed8936;">Draft</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Guidelines Card (matching course style) -->
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
                    
                    <!-- Quick Actions Card (matching course style) -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </div>
                        
                        <div class="quick-actions-grid">
                            <a href="{{ route('admin.topics.index') }}" class="action-card">
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
                            
                            <a href="{{ route('admin.topics.create') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Create New Topic</div>
                                    <div class="action-subtitle">Add another topic</div>
                                </div>
                                <div class="action-arrow">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions (exactly matching course style) -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <div>
                    <form action="{{ route('admin.topics.destroy', Crypt::encrypt($topic->id)) }}" method="POST" id="deleteForm" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" id="deleteButton">
                            <i class="fas fa-trash-alt"></i> Delete Topic
                        </button>
                    </form>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.topics.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" form="topicForm" class="btn btn-primary" id="submitButton">
                        <i class="fas fa-save"></i> Update Topic
                    </button>
                </div>
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
        const previewAvatar = document.getElementById('previewAvatar');
        const previewStatus = document.querySelector('.topic-preview-status');
        const publishToggle = document.getElementById('publishToggle');
        const toggleStatusText = document.getElementById('toggleStatusText');
        const isPublishedField = document.getElementById('is_published_field');
        const summaryStatus = document.getElementById('summaryStatus');
        const resourcesCount = document.getElementById('resourcesCount');
        const submitButton = document.getElementById('submitButton');
        
        // Live preview update (matching course functionality)
        function updatePreview() {
            const title = titleInput.value.trim();
            previewTitle.textContent = title || '{{ $topic->title }}';
            
            // Update avatar
            if (title) {
                previewAvatar.textContent = title.charAt(0).toUpperCase();
            }
            
            // Update code preview
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
        
        if (titleInput) {
            titleInput.addEventListener('input', updatePreview);
        }
        
        // Update publish status display (exactly matching course)
        function updatePublishStatus() {
            const isPublished = publishToggle.checked;
            
            // Update hidden field
            if (isPublishedField) {
                isPublishedField.value = isPublished ? '1' : '0';
            }
            
            // Update toggle status text
            if (toggleStatusText) {
                if (isPublished) {
                    toggleStatusText.innerHTML = '<span class="status-published"><i class="fas fa-check-circle"></i> Published</span>';
                } else {
                    toggleStatusText.innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
                }
            }
            
            // Update preview status
            if (previewStatus) {
                if (isPublished) {
                    previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Published';
                    previewStatus.className = 'topic-preview-status status-published';
                } else {
                    previewStatus.innerHTML = '<i class="fas fa-clock"></i> Draft';
                    previewStatus.className = 'topic-preview-status status-draft';
                }
            }
            
            // Update summary status
            if (summaryStatus) {
                summaryStatus.innerHTML = isPublished 
                    ? '<span style="color: #48bb78;">Published</span>' 
                    : '<span style="color: #ed8936;">Draft</span>';
            }
        }
        
        if (publishToggle) {
            publishToggle.addEventListener('change', updatePublishStatus);
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
                    } else if (!file.name.toLowerCase().endsWith('.pdf')) {
                        Swal.fire({
                            title: 'Invalid File Type',
                            text: 'Only PDF files are allowed.',
                            icon: 'error',
                            confirmButtonColor: '#667eea'
                        });
                        this.value = '';
                    } else {
                        // Update resources count
                        if (resourcesCount) {
                            const currentCount = parseInt(resourcesCount.textContent) || 0;
                            resourcesCount.textContent = currentCount + 1;
                        }
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
        
        // Handle delete button click with SweetAlert2 (exactly matching course)
        const deleteButton = document.getElementById('deleteButton');
        if (deleteButton) {
            deleteButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Delete Topic?',
                    text: 'This action cannot be undone. All topic data will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f56565',
                    cancelButtonColor: '#a0aec0',
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        deleteButton.disabled = true;
                        document.getElementById('deleteForm').submit();
                    }
                });
            });
        }

        // Form validation (matching course style)
        const updateForm = document.getElementById('topicForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function(e) {
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
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter a topic title.',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return;
                }
                
                // Show loading state
                if (submitButton) {
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    submitButton.disabled = true;
                    
                    // Re-enable after timeout (in case form doesn't redirect)
                    setTimeout(() => {
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    }, 5000);
                }
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
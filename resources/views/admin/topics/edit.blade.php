@extends('layouts.admin')

@section('title', isset($topic) ? 'Edit Topic - ' . $topic->title : 'Create New Topic')

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #e0e7ff;
        --primary-dark: #3730a3;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
        --success: #10b981;
        --success-light: #d1fae5;
        --success-dark: #047857;
        --danger: #ef4444;
        --danger-light: #fee2e2;
        --danger-dark: #b91c1c;
        --warning: #f59e0b;
        --warning-light: #fef3c7;
        --warning-dark: #d97706;
        --radius: 0.5rem;
        --radius-sm: 0.25rem;
        --radius-lg: 0.75rem;
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    /* Form Container */
    .form-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-group {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-icon {
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.125rem;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
    }

    .view-all-link {
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }

    .view-all-link:hover {
        gap: 0.625rem;
        color: var(--primary-dark);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer-modern {
        padding: 1.5rem;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    /* Form Elements */
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
    }
    
    .form-label.required::after {
        content: " *";
        color: var(--danger);
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-sm);
        font-size: 0.875rem;
        color: var(--gray-900);
        background: white;
        transition: all 0.2s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-input.error {
        border-color: var(--danger);
    }
    
    .form-file {
        width: 100%;
        padding: 0.75rem;
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-sm);
        background: var(--gray-50);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .form-file:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    
    .form-file:focus {
        outline: none;
        border-color: var(--primary);
        border-style: solid;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
    
    .form-help {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .form-error {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: var(--danger);
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Current File Display */
    .current-file {
        margin-top: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: var(--radius-sm);
        border: 1px solid var(--gray-200);
    }
    
    .current-file-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .current-file-icon {
        width: 36px;
        height: 36px;
        background: rgba(220, 38, 38, 0.1);
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
    }
    
    .current-file-info {
        flex: 1;
    }
    
    .current-file-name {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }
    
    .current-file-type {
        font-size: 0.75rem;
        color: var(--gray-500);
    }
    
    .current-file-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .current-file-warning {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--gray-200);
        font-size: 0.75rem;
        color: var(--warning-dark);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        background: var(--success-light);
        color: var(--success-dark);
    }
    
    /* Buttons */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-secondary:hover {
        background: var(--gray-200);
        color: var(--gray-900);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    
    /* Error Alert */
    .error-alert {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--danger-light);
        color: var(--danger-dark);
        border-radius: var(--radius-sm);
        border: 1px solid var(--danger);
    }
    
    .error-alert-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .error-list {
        margin: 0;
        padding-left: 1.25rem;
    }
    
    .error-list li {
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    
    /* Auto-publish Notice */
    .auto-publish-notice {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--success-light);
        color: var(--success-dark);
        border-radius: var(--radius-sm);
        border: 1px solid var(--success);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .card-footer-modern {
            flex-direction: column;
            gap: 1rem;
        }
        
        .card-footer-modern .btn {
            width: 100%;
            justify-content: center;
        }
        
        .current-file-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .current-file-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endpush

@section('content')
    <!-- Topic Form -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas {{ isset($topic) ? 'fa-edit' : 'fa-plus' }} card-icon"></i>
                <h2 class="card-title">{{ isset($topic) ? 'Edit Topic: ' . $topic->title : 'Create New Topic' }}</h2>
            </div>
            <a href="{{ route('admin.topics.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Topics
            </a>
        </div>
        
        <div class="card-body">
            <!-- Auto-publish Notice -->
            @if(!isset($topic))
            <div class="auto-publish-notice">
                <i class="fas fa-rocket"></i>
                <div>
                    <strong>Note:</strong> All topics are automatically published and will be immediately visible to students.
                </div>
            </div>
            @endif
            
            <form action="{{ isset($topic) ? route('admin.topics.update', Crypt::encrypt($topic->id)) : route('admin.topics.store') }}" method="POST" enctype="multipart/form-data" id="topicForm">
                @csrf
                @if(isset($topic))
                    @method('PUT')
                @endif
                
                <!-- Hidden field to set topic as published -->
                <input type="hidden" name="is_published" value="1">
                
                @if($errors->any())
                <div class="error-alert">
                    <div class="error-alert-header">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Please fix the following errors:</span>
                    </div>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label required">Topic Title</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $topic->title ?? '') }}"
                           required
                           placeholder="Enter topic title"
                           class="form-input @error('title') error @enderror">
                    <span class="form-help">
                        <i class="fas fa-info-circle"></i> Enter a descriptive title for your topic
                    </span>
                    @error('title')
                        <span class="form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">Topic Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Describe what this topic covers..."
                        class="form-input @error('description') error @enderror"
                        style="min-height: 100px; resize: vertical;">{{ old('description', $topic->description ?? '') }}</textarea>
                    <span class="form-help">
                        <i class="fas fa-info-circle"></i> Optional: Add a brief description of the topic content
                    </span>
                    @error('description')
                        <span class="form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                <!-- PDF File Upload -->
                <div class="form-group">
                    <label for="pdf_file" class="form-label">Upload PDF Document</label>
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
                                   class="btn btn-primary" style="font-size: 0.75rem; padding: 0.5rem 0.75rem;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ asset($topic->pdf_file) }}" download 
                                   class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem 0.75rem;">
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
                    <label for="video_link" class="form-label">Video Link</label>
                    <input type="text" 
                           id="video_link" 
                           name="video_link" 
                           value="{{ old('video_link', $topic->video_link ?? '') }}"
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="form-input @error('video_link') error @enderror">
                    <span class="form-help">
                        <i class="fas fa-info-circle"></i> Enter YouTube, Vimeo, or direct video URL
                    </span>
                    @error('video_link')
                        <span class="form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Attachment -->
                <div class="form-group">
                    <label for="attachment" class="form-label">Attachment Link</label>
                    <input type="text" 
                           id="attachment" 
                           name="attachment" 
                           value="{{ old('attachment', $topic->attachment ?? '') }}"
                           placeholder="https://drive.google.com/file/... or https://example.com/files/document.pdf"
                           class="form-input @error('attachment') error @enderror">
                    <span class="form-help">
                        <i class="fas fa-info-circle"></i> Enter Google Drive, Dropbox, or direct file URL
                    </span>
                    @error('attachment')
                        <span class="form-error">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
                
                <!-- Publish Status Indicator -->
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="status-badge">
                        <i class="fas fa-check-circle"></i> 
                        @if(isset($topic))
                            {{ $topic->is_published ? 'Published' : 'Will be published on update' }}
                        @else
                            Auto-published
                        @endif
                    </div>
                    <span class="form-help">
                        <i class="fas fa-info-circle"></i> Topics are automatically published and visible to students
                    </span>
                </div>
        </div>
        
        <div class="card-footer-modern">
            <a href="{{ route('admin.topics.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="submitButton">
                <i class="fas {{ isset($topic) ? 'fa-save' : 'fa-plus-circle' }}"></i>
                {{ isset($topic) ? 'Update Topic' : 'Create & Publish Topic' }}
            </button>
            </form>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="form-container" style="margin-top: 1.5rem;">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-bolt card-icon"></i>
                <h2 class="card-title">Quick Tips</h2>
            </div>
        </div>
        
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--primary-light); border-radius: var(--radius-sm); border: 1px solid var(--primary);">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Auto-published</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Topics publish immediately</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--success-light); border-radius: var(--radius-sm); border: 1px solid var(--success);">
                    <div style="width: 44px; height: 44px; background: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">PDF Files</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">Max 10MB PDF files</div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--info-light); border-radius: var(--radius-sm); border: 1px solid var(--primary);">
                    <div style="width: 44px; height: 44px; background: var(--primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">Video Links</div>
                        <div style="font-size: 0.75rem; opacity: 0.8;">YouTube/Vimeo supported</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File size validation for PDF upload
        const pdfFileInput = document.getElementById('pdf_file');
        if (pdfFileInput) {
            pdfFileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = file.size / 1024 / 1024; // Convert to MB
                    const maxSize = 10; // 10MB
                    
                    if (fileSize > maxSize) {
                        alert(`File size (${fileSize.toFixed(2)}MB) exceeds maximum allowed size (${maxSize}MB).`);
                        this.value = ''; // Clear the file input
                    }
                    
                    if (!file.name.toLowerCase().endsWith('.pdf')) {
                        alert('Only PDF files are allowed.');
                        this.value = ''; // Clear the file input
                    }
                }
            });
        }
        
        // URL validation for video link
        const videoLinkInput = document.getElementById('video_link');
        if (videoLinkInput) {
            videoLinkInput.addEventListener('blur', function() {
                if (this.value && !this.value.startsWith('http://') && !this.value.startsWith('https://')) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // URL validation for attachment link
        const attachmentInput = document.getElementById('attachment');
        if (attachmentInput) {
            attachmentInput.addEventListener('blur', function() {
                if (this.value && !this.value.startsWith('http://') && !this.value.startsWith('https://')) {
                    this.value = 'https://' + this.value;
                }
            });
        }
        
        // Form validation and submission
        const form = document.getElementById('topicForm');
        const submitButton = document.getElementById('submitButton');
        
        if (form && submitButton) {
            form.addEventListener('submit', function(e) {
                const titleInput = document.getElementById('title');
                if (!titleInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter a topic title.');
                    titleInput.focus();
                    return false;
                }
                
                // Show loading state
                const originalHTML = submitButton.innerHTML;
                const isEdit = {{ isset($topic) ? 'true' : 'false' }};
                submitButton.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${isEdit ? 'Updating...' : 'Creating & Publishing...'}`;
                submitButton.disabled = true;
                
                // Add a small delay to show the loading state
                setTimeout(() => {
                    // If form is still not submitted, reset button
                    if (submitButton.disabled) {
                        submitButton.innerHTML = originalHTML;
                        submitButton.disabled = false;
                    }
                }, 5000);
            });
        }
        
        // Optional: Add confirmation dialog before submitting (for edits)
        @if(isset($topic))
        const confirmBeforeSubmit = true; // Set to true if you want confirmation for edits
        
        if (confirmBeforeSubmit && form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const confirmationMessage = `Are you sure you want to update this topic?\n\nNote: The topic will remain published and visible to students.`;
                
                if (confirm(confirmationMessage)) {
                    // Show loading state
                    const originalHTML = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    submitButton.disabled = true;
                    
                    // Submit the form after confirmation
                    setTimeout(() => {
                        form.submit();
                    }, 100);
                }
            });
        }
        @endif
    });
</script>
@endpush
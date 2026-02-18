@extends('layouts.teacher')

@section('title', 'Create New Course - Teacher Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/course-form.css') }}">
@endpush

@section('content')
    <!-- Create Course Form Card -->
    <div class="form-container">
        <div class="card-header">
            <div class="card-title-group">
                <i class="fas fa-book-medical card-icon"></i>
                <h2 class="card-title">Create New Course</h2>
            </div>
            <a href="{{ route('teacher.courses.index') }}" class="view-all-link">
                <i class="fas fa-arrow-left"></i> Back to Courses
            </a>
        </div>
        
        <div class="card-body">
            <!-- Course Preview - Live Preview -->
            <div class="course-preview">
                <div class="course-preview-avatar" id="previewAvatar">
                    <span id="avatarLetter">📚</span>
                </div>
                <div class="course-preview-title" id="previewTitle">New Course</div>
                <div class="course-preview-code" id="previewCode">---</div>
                <div class="course-preview-status">
                    <i class="fas fa-check-circle"></i> Published
                </div>
            </div>

            <!-- Display validation errors -->
            @if($errors->any())
            <div class="validation-alert">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            @if(session('success'))
            <div class="success-alert">
                <div style="display: flex; align-items: center;">
                    <i class="fas fa-check-circle"></i>
                    <strong>{{ session('success') }}</strong>
                </div>
            </div>
            @endif
            
            <!-- Two Column Layout - Form and Sidebar Inline -->
            <div class="two-column-layout">
                <!-- Left Column - Form -->
                <div class="form-column">
                    <form action="{{ route('teacher.courses.store') }}" method="POST" id="courseForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Course Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label required">
                                        <i class="fas fa-heading"></i> Course Title
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           required
                                           placeholder="e.g., Introduction to Programming"
                                           class="form-control @error('title') is-invalid @enderror">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_code" class="form-label required">
                                        <i class="fas fa-code"></i> Course Code
                                    </label>
                                    <input type="text" 
                                           id="course_code" 
                                           name="course_code" 
                                           value="{{ old('course_code') }}" 
                                           required
                                           placeholder="e.g., CS101"
                                           class="form-control @error('course_code') is-invalid @enderror">
                                    <div class="form-hint">
                                        <i class="fas fa-lightbulb"></i> Will auto-generate based on title
                                    </div>
                                    @error('course_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Course Description
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter a detailed description of the course..."
                                          class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Describe what students will learn
                                </div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Course Details Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-cog"></i> Course Details
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="credits" class="form-label required">
                                        <i class="fas fa-cubes"></i> Credits
                                    </label>
                                    <div class="input-with-unit">
                                        <input type="number" 
                                               id="credits" 
                                               name="credits" 
                                               value="{{ old('credits', 3) }}" 
                                               min="0.5" 
                                               max="10"
                                               step="0.5"
                                               required
                                               class="form-control @error('credits') is-invalid @enderror">
                                        <span class="input-unit">credits</span>
                                    </div>
                                    @error('credits')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="department" class="form-label">
                                        <i class="fas fa-building"></i> Department
                                    </label>
                                    <input type="text" 
                                           id="department" 
                                           name="department" 
                                           value="{{ old('department') }}" 
                                           placeholder="e.g., Computer Science"
                                           class="form-control @error('department') is-invalid @enderror">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Optional: Your department
                                    </div>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="learning_outcomes" class="form-label">
                                        <i class="fas fa-tasks"></i> Learning Outcomes
                                    </label>
                                    <textarea id="learning_outcomes" 
                                              name="learning_outcomes" 
                                              rows="3"
                                              placeholder="What will students learn in this course?"
                                              class="form-control @error('learning_outcomes') is-invalid @enderror">{{ old('learning_outcomes') }}</textarea>
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i> Optional: Key learning objectives
                                    </div>
                                    @error('learning_outcomes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="semester" class="form-label">
                                        <i class="fas fa-calendar"></i> Semester
                                    </label>
                                    <select id="semester" 
                                            name="semester"
                                            class="form-select @error('semester') is-invalid @enderror">
                                        <option value="">-- Select Semester (Optional) --</option>
                                        <option value="fall" {{ old('semester') == 'fall' ? 'selected' : '' }}>Fall</option>
                                        <option value="spring" {{ old('semester') == 'spring' ? 'selected' : '' }}>Spring</option>
                                        <option value="summer" {{ old('semester') == 'summer' ? 'selected' : '' }}>Summer</option>
                                        <option value="winter" {{ old('semester') == 'winter' ? 'selected' : '' }}>Winter</option>
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="thumbnail" class="form-label">
                                    <i class="fas fa-image"></i> Thumbnail URL
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <input type="url" 
                                           id="thumbnail" 
                                           name="thumbnail" 
                                           value="{{ old('thumbnail') }}"
                                           placeholder="https://example.com/image.jpg"
                                           class="form-control @error('thumbnail') is-invalid @enderror"
                                           style="flex: 1;">
                                    <button type="button" 
                                            id="preview-thumbnail"
                                            class="btn btn-outline"
                                            style="white-space: nowrap;">
                                        <i class="fas fa-eye"></i> Preview
                                    </button>
                                </div>
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Optional: URL to course thumbnail image
                                </div>
                                
                                <!-- Thumbnail Preview -->
                                <div id="thumbnail-preview" style="margin-top: 0.75rem; display: none;">
                                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--dark); margin-bottom: 0.5rem;">Preview:</div>
                                    <div style="width: 100%; max-width: 400px; height: 225px; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                                        <img id="preview-image" src="" alt="Thumbnail preview" 
                                             style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                        <div id="no-preview" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: var(--gray-500);">
                                            No preview available
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Publishing Options Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-globe"></i> Publishing Options
                            </div>
                            
                            <div class="publishing-options">
                                <div style="display: flex; align-items: center; gap: 2rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" 
                                               name="is_published" 
                                               value="1" 
                                               {{ old('is_published', 1) == 1 ? 'checked' : '' }}
                                               style="width: 16px; height: 16px;">
                                        <span style="font-weight: 500; color: var(--dark);">
                                            <i class="fas fa-check-circle" style="color: var(--success); margin-right: 0.25rem;"></i>
                                            Published
                                        </span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" 
                                               name="is_published" 
                                               value="0" 
                                               {{ old('is_published') == 0 ? 'checked' : '' }}
                                               style="width: 16px; height: 16px;">
                                        <span style="font-weight: 500; color: var(--dark);">
                                            <i class="fas fa-clock" style="color: var(--warning); margin-right: 0.25rem;"></i>
                                            Draft
                                        </span>
                                    </label>
                                </div>
                                <div class="form-hint" style="margin-top: 0.5rem;">
                                    <i class="fas fa-info-circle"></i> 
                                    Published courses are visible to enrolled students. Draft courses are only visible to you.
                                </div>
                                @error('is_published')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Status Notice -->
                        <div class="status-notice">
                            <i class="fas fa-info-circle"></i>
                            <div class="status-notice-content">
                                <div class="status-notice-title">Course Status</div>
                                <div class="status-notice-text">
                                    @if(old('is_published', 1) == 1)
                                        New courses are created as <strong>Published</strong>. They will be visible to enrolled students immediately.
                                    @else
                                        New courses are created as <strong>Draft</strong>. They will only be visible to you until published.
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" name="teacher_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="status" value="active">
                    </form>
                </div>
                
                <!-- Right Column - Guidelines Sidebar -->
                <div class="sidebar-column">
                    <!-- Instructor Info Card -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-title">
                            <i class="fas fa-chalkboard-teacher"></i> Instructor Information
                        </div>
                        
                        <div class="instructor-section" style="padding-bottom: 0;">
                            <div class="instructor-card">
                                <div class="instructor-avatar">
                                    {{ strtoupper(substr(Auth::user()->f_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->l_name, 0, 1)) }}
                                </div>
                                <div class="instructor-info">
                                    <div class="instructor-name">{{ Auth::user()->f_name }} {{ Auth::user()->l_name }}</div>
                                    <div class="instructor-details">
                                        <i class="fas fa-envelope"></i> {{ Auth::user()->email }}<br>
                                        @if(Auth::user()->employee_id)
                                            <i class="fas fa-id-badge"></i> {{ Auth::user()->employee_id }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--gray-500); padding: 0.5rem; background: var(--gray-50); border-radius: 6px;">
                                <i class="fas fa-info-circle"></i> You will be assigned as the instructor for this course.
                            </div>
                        </div>
                    </div>
                    
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
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Course Code</div>
                                    <div class="guideline-text">Use standard format like CS101, MATH201</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-cubes"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Credits</div>
                                    <div class="guideline-text">Enter between 0.5 and 10 credits</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Thumbnail</div>
                                    <div class="guideline-text">Optional: Use a high-quality course image</div>
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
                            <a href="{{ route('teacher.courses.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All Courses</div>
                                    <div class="action-subtitle">Browse your courses</div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-footer-modern">
            <div class="form-actions">
                <a href="{{ route('teacher.courses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" form="courseForm" class="btn btn-primary" id="submitButton">
                    <i class="fas fa-save"></i> Create Course
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const titleInput = document.getElementById('title');
        const codeInput = document.getElementById('course_code');
        const previewTitle = document.getElementById('previewTitle');
        const previewCode = document.getElementById('previewCode');
        const avatarLetter = document.getElementById('avatarLetter');
        const submitButton = document.getElementById('submitButton');
        const publishedRadio = document.querySelector('input[name="is_published"][value="1"]');
        const draftRadio = document.querySelector('input[name="is_published"][value="0"]');
        const statusNotice = document.querySelector('.status-notice-text');
        
        // Live preview update
        function updatePreview() {
            // Update title
            const title = titleInput.value.trim();
            previewTitle.textContent = title || 'New Course';
            
            // Update code
            const code = codeInput.value.trim();
            previewCode.textContent = code || '---';
            
            // Update avatar
            if (code) {
                avatarLetter.textContent = code.charAt(0).toUpperCase();
            } else if (title) {
                avatarLetter.textContent = title.charAt(0).toUpperCase();
            } else {
                avatarLetter.textContent = '📚';
            }
            
            // Update status in preview
            const statusElement = document.querySelector('.course-preview-status');
            if (statusElement) {
                if (publishedRadio && publishedRadio.checked) {
                    statusElement.innerHTML = '<i class="fas fa-check-circle"></i> Published';
                    statusElement.classList.remove('status-draft');
                    statusElement.classList.add('status-published');
                } else {
                    statusElement.innerHTML = '<i class="fas fa-clock"></i> Draft';
                    statusElement.classList.remove('status-published');
                    statusElement.classList.add('status-draft');
                }
            }
            
            // Update status notice text
            if (statusNotice) {
                if (publishedRadio && publishedRadio.checked) {
                    statusNotice.innerHTML = 'New courses are created as <strong>Published</strong>. They will be visible to enrolled students immediately.';
                } else {
                    statusNotice.innerHTML = 'New courses are created as <strong>Draft</strong>. They will only be visible to you until published.';
                }
            }
        }
        
        titleInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);
        
        // Update preview when publish status changes
        if (publishedRadio) {
            publishedRadio.addEventListener('change', updatePreview);
        }
        if (draftRadio) {
            draftRadio.addEventListener('change', updatePreview);
        }
        
        // Auto-generate course code suggestion
        titleInput.addEventListener('input', function() {
            const title = this.value.trim();
            
            if (title && !codeInput.value) {
                const words = title.toUpperCase().split(' ');
                let code = '';
                
                if (words.length >= 2) {
                    if (words[0].length >= 3) {
                        code = words[0].substring(0, 3);
                    } else {
                        code = words[0].substring(0, 2) + words[1].charAt(0);
                    }
                    
                    if (code) {
                        const randomNum = Math.floor(Math.random() * 900) + 100;
                        const suggestedCode = code + randomNum;
                        codeInput.value = suggestedCode;
                        updatePreview();
                        
                        // Show hint
                        const hintDiv = codeInput.closest('.form-group').querySelector('.form-hint');
                        if (hintDiv) {
                            hintDiv.innerHTML = `<i class="fas fa-check-circle"></i> Suggested: ${suggestedCode}`;
                        }
                    }
                }
            }
        });

        // Thumbnail preview functionality
        const thumbnailInput = document.getElementById('thumbnail');
        const previewBtn = document.getElementById('preview-thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const previewImage = document.getElementById('preview-image');
        const noPreview = document.getElementById('no-preview');
        
        function updateThumbnailPreview() {
            const url = thumbnailInput.value.trim();
            
            if (url) {
                thumbnailPreview.style.display = 'block';
                previewImage.src = url;
                previewImage.style.display = 'block';
                noPreview.style.display = 'none';
                
                previewImage.onload = function() {
                    previewImage.style.display = 'block';
                    noPreview.style.display = 'none';
                    noPreview.textContent = 'No preview available';
                    noPreview.style.color = 'var(--gray-500)';
                };
                
                previewImage.onerror = function() {
                    previewImage.style.display = 'none';
                    noPreview.style.display = 'flex';
                    noPreview.textContent = 'Image failed to load';
                    noPreview.style.color = '#ef4444';
                };
            } else {
                thumbnailPreview.style.display = 'none';
            }
        }
        
        if (previewBtn) {
            previewBtn.addEventListener('click', updateThumbnailPreview);
        }
        
        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', updateThumbnailPreview);
            thumbnailInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    updateThumbnailPreview();
                }
            });
        }
        
        // Form validation and submission
        const courseForm = document.getElementById('courseForm');
        if (courseForm) {
            courseForm.addEventListener('submit', function(e) {
                const title = titleInput.value.trim();
                const code = codeInput.value.trim();
                const credits = document.getElementById('credits').value;
                
                let isValid = true;
                
                if (!title) {
                    titleInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    titleInput.classList.remove('is-invalid');
                }
                
                if (!code) {
                    codeInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    codeInput.classList.remove('is-invalid');
                }
                
                if (!credits || parseFloat(credits) < 0.5 || parseFloat(credits) > 10) {
                    document.getElementById('credits').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('credits').classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please fill in all required fields correctly.',
                        icon: 'error',
                        confirmButtonColor: 'var(--primary)'
                    });
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
        
        // Show validation errors if any
        @if($errors->any())
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                icon: 'error',
                title: 'Please fix the errors in the form.',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
    
    // Reset form function
    function resetForm() {
        document.getElementById('courseForm').reset();
        document.getElementById('previewTitle').textContent = 'New Course';
        document.getElementById('previewCode').textContent = '---';
        document.getElementById('avatarLetter').textContent = '📚';
        
        // Reset preview status
        const statusElement = document.querySelector('.course-preview-status');
        if (statusElement) {
            statusElement.innerHTML = '<i class="fas fa-check-circle"></i> Published';
            statusElement.classList.remove('status-draft');
            statusElement.classList.add('status-published');
        }
        
        // Reset status notice
        const statusNotice = document.querySelector('.status-notice-text');
        if (statusNotice) {
            statusNotice.innerHTML = 'New courses are created as <strong>Published</strong>. They will be visible to enrolled students immediately.';
        }
        
        // Clear error states
        document.querySelectorAll('.form-control, .form-select').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        // Hide thumbnail preview
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        if (thumbnailPreview) {
            thumbnailPreview.style.display = 'none';
        }
        
        Swal.fire({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: 'info',
            title: 'Form has been cleared.',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
</script>
@endpush
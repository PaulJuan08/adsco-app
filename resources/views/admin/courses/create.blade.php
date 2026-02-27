@extends('layouts.admin')

@section('title', 'Create New Course - Admin Dashboard')

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
            <a href="{{ route('admin.courses.index') }}" class="view-all-link">
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
                <div class="course-preview-status" id="previewStatus">
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
                        <h4>Course Visibility</h4>
                        <p>Control whether this course is visible to students</p>
                    </div>
                </div>
                <div class="toggle-wrapper">
                    <div class="toggle-status" id="toggleStatusText">
                        <span class="status-draft"><i class="fas fa-clock"></i> Draft</span>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" id="publishToggle" name="is_published" value="1" form="courseForm">
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
                    <form action="{{ route('admin.courses.store') }}" method="POST" id="courseForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Basic Course Information
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading"></i> Course Title
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           required
                                           placeholder="e.g., Introduction to Programming"
                                           class="form-input @error('title') error @enderror">
                                    @error('title')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="course_code" class="form-label">
                                        <i class="fas fa-code"></i> Course Code
                                        <span class="required">*</span>
                                    </label>
                                    <input type="text" 
                                           id="course_code" 
                                           name="course_code" 
                                           value="{{ old('course_code') }}" 
                                           required
                                           placeholder="e.g., CS101"
                                           class="form-input @error('course_code') error @enderror">
                                    <div class="form-hint">
                                        <i class="fas fa-lightbulb"></i> Will auto-generate based on title
                                    </div>
                                    @error('course_code')
                                        <div class="form-error">{{ $message }}</div>
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
                                          class="form-textarea @error('description') error @enderror">{{ old('description') }}</textarea>
                                <div class="form-hint">
                                    <i class="fas fa-info-circle"></i> Describe what students will learn
                                </div>
                                @error('description')
                                    <div class="form-error">{{ $message }}</div>
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
                                    <label for="teacher_id" class="form-label">
                                        <i class="fas fa-chalkboard-teacher"></i> Assign Teacher
                                    </label>
                                    <select id="teacher_id" 
                                            name="teacher_id"
                                            class="form-select @error('teacher_id') error @enderror">
                                        <option value="">-- Select Teacher (Optional) --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->f_name }} {{ $teacher->l_name }} 
                                                @if($teacher->employee_id)
                                                    ({{ $teacher->employee_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">
                                        <i class="fas fa-user-tie"></i> Can be assigned later
                                    </div>
                                    @error('teacher_id')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-group">
                                    <label for="credits" class="form-label">
                                        <i class="fas fa-cubes"></i> Credits
                                        <span class="required">*</span>
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
                                               class="form-input @error('credits') error @enderror">
                                        <span class="input-unit">credits</span>
                                    </div>
                                    @error('credits')
                                        <div class="form-error">{{ $message }}</div>
                                    @enderror
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
                                    <i class="fas fa-code"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Course Code</div>
                                    <div class="guideline-text">Use standard format like CS101, MATH201</div>
                                </div>
                            </div>
                            
                            <div class="guideline-item">
                                <div class="guideline-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Teacher Assignment</div>
                                    <div class="guideline-text">Can be assigned now or later</div>
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
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="guideline-content">
                                    <div class="guideline-title">Publish Status</div>
                                    <div class="guideline-text">Toggle to make course visible to students</div>
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
                            <a href="{{ route('admin.courses.index') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">View All Courses</div>
                                    <div class="action-subtitle">Browse existing courses</div>
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
                            
                            <a href="{{ route('admin.courses.create') }}" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="action-content">
                                    <div class="action-title">Refresh Data</div>
                                    <div class="action-subtitle">Reload teacher list</div>
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
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">
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
        const previewStatus = document.getElementById('previewStatus');
        const publishToggle = document.getElementById('publishToggle');
        const toggleStatusText = document.getElementById('toggleStatusText');
        const avatarLetter = document.getElementById('avatarLetter');
        const submitButton = document.getElementById('submitButton');
        
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
        }
        
        // Update publish status
        function updatePublishStatus() {
            const isPublished = publishToggle.checked;
            
            if (isPublished) {
                toggleStatusText.innerHTML = '<span class="status-published"><i class="fas fa-check-circle"></i> Published</span>';
                previewStatus.innerHTML = '<i class="fas fa-check-circle"></i> Published';
                previewStatus.className = 'course-preview-status status-published';
            } else {
                toggleStatusText.innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
                previewStatus.innerHTML = '<i class="fas fa-clock"></i> Draft';
                previewStatus.className = 'course-preview-status status-draft';
            }
        }
        
        titleInput.addEventListener('input', updatePreview);
        codeInput.addEventListener('input', updatePreview);
        publishToggle.addEventListener('change', updatePublishStatus);
        
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
                        const hintDiv = codeInput.nextElementSibling;
                        if (hintDiv && hintDiv.classList.contains('form-hint')) {
                            hintDiv.innerHTML = `<i class="fas fa-check-circle"></i> Suggested: ${suggestedCode}`;
                        }
                    }
                }
            }
        });
        
        // Form validation and submission
        const courseForm = document.getElementById('courseForm');
        if (courseForm) {
            courseForm.addEventListener('submit', function(e) {
                const title = titleInput.value.trim();
                const code = codeInput.value.trim();
                const credits = document.getElementById('credits').value;
                
                let isValid = true;
                
                if (!title) {
                    titleInput.classList.add('error');
                    isValid = false;
                } else {
                    titleInput.classList.remove('error');
                }
                
                if (!code) {
                    codeInput.classList.add('error');
                    isValid = false;
                } else {
                    codeInput.classList.remove('error');
                }
                
                if (!credits || parseFloat(credits) < 0.5 || parseFloat(credits) > 10) {
                    document.getElementById('credits').classList.add('error');
                    isValid = false;
                } else {
                    document.getElementById('credits').classList.remove('error');
                }
                
                if (!isValid) {
                    e.preventDefault();
                    showToast('Please fill in all required fields correctly.', 'error');
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
        document.getElementById('courseForm').reset();
        document.getElementById('previewTitle').textContent = 'New Course';
        document.getElementById('previewCode').textContent = '---';
        document.getElementById('avatarLetter').textContent = '📚';
        document.getElementById('publishToggle').checked = false;
        document.getElementById('toggleStatusText').innerHTML = '<span class="status-draft"><i class="fas fa-clock"></i> Draft</span>';
        document.getElementById('previewStatus').innerHTML = '<i class="fas fa-clock"></i> Draft';
        document.getElementById('previewStatus').className = 'course-preview-status status-draft';
        
        // Clear error states
        document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(el => {
            el.classList.remove('error');
        });
        
        showToast('Form has been cleared.', 'info');
    }
</script>
@endpush